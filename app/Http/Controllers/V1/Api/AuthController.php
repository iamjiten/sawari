<?php

namespace App\Http\Controllers\V1\Api;

use App\Enums\StatusEnum;
use App\Exceptions\ApiResponder;
use App\Helpers\SendSMS;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserSignupRequest;
use App\Http\Resources\UserResource;
use App\Models\EmailVerification;
use App\Models\Otp;
use App\Models\User;
use App\Notifications\ResetLink;
use App\Rules\MobileNumber;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use ApiResponder;

    public function generate(Request $request)
    {
        $validatedData = $request->validate([
            'mobile_number' => 'required|numeric|gte:10',
        ]);
        $user = User::where('mobile', $validatedData['mobile_number'])->first();
//        $otp = 123456; // test
        $otp = rand(100000, 999999); //production

        DB::beginTransaction();
        try {
            if ($user) {
                $check = $user->otps()->where('otp_type', 'auth_otp');
                if ($check->count() > 0) {
                    $check->delete();
                }
            }
            $createOtp = Otp::create([
                'user_id' => optional($user)->id ?? rand(10000, 99999),
                'otp_type' => 'auth_otp',
                'code' => $otp,
            ]);

            // un comment mail in production
            // Mail::to($validatedData['email'])->send(new VerifyUser($otp));
            $smsResponse = (new SendSMS())->sendOtp($validatedData['mobile_number'], 'Hello Sawari - Your Otp is ' . $otp);


//            if ($smsResponse) {
            DB::commit();
            return $this->success([
                'user_id' => $createOtp->user_id,
                'type' => $user ? 'signin' : 'signup',
                'otp' => $otp,
            ], 'OTP sent on your registered Mobile');
//            } else {
//                return response()->json([
//                    'message' => 'failed to send Otp',
//                    'status' => 500,
//                    'error_message' => null
//                ]);
//            }


        } catch (Exception $e) {
            DB::rollBack();

            return $this->somethingWentWrong($e);
        }
    }

    public function verify(Request $request)
    {
        if ($request->type == 'signup') {
            $otp = Otp::where(['code' => $request->code, 'user_id' => $request->user_id])->first();
            if ($otp) {
                return $this->success("signup", "Redirecting to Signup");
            } else {
                return $this->error('Otp & User match failed', 404);
            }
        }
        $model = User::findOrFail($request->user_id);
        $lastOtp = $model->otps->where('otp_type', 'auth_otp')->first();
        if ($lastOtp) {
            try {
                if ($lastOtp->code == $request->code) {
                    $lastOtp->delete();

                    if ($model->status?->value == StatusEnum::Inactive?->value) {
                        return $this->error('Your account has been banned. Please contact to support team', 422);
                    }

                    return $this->success($this->responseUserToken($model));
                } else {
                    return $this->error('Invalid token', 404);
                }
            } catch (Exception $ex) {
                return $this->somethingWentWrong($ex);
            }
        } else {
            return $this->error('Token Not Match', 404);
        }
    }

    public function signup()
    {
        $storeRequest = UserSignupRequest::class;
        $data = resolve($storeRequest)->safe()->only((new User())->getFillable());
        try {
            DB::beginTransaction();
            $model = User::create($data);

            if (method_exists(new User(), 'afterCreateProcess')) {
                $model->afterCreateProcess();
            }
            DB::commit();

            return $this->success($this->responseUserToken($model));
        } catch (Exception $e) {
            DB::rollBack();

            return $this->somethingWentWrong($e);
        }
    }

    public function responseUserToken($model)
    {
        $token = $model->createToken('user')->plainTextToken;

        return [
            'user' => UserResource::make($model),
            'token' => $token,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | For Admin Use
    |--------------------------------------------------------------------------
    |
    | Below block of codes are used for admin authentication
    |
    */

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return $this->success([], 'Logged out successfully');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'mobile' => ['required','numeric', new MobileNumber()],
            'password' => 'required'
        ]);

        if (!Auth::attempt($credentials)) {
            return $this->error('Credentials do not match', 401);
        }

        if (auth()->user()->status?->value == StatusEnum::Inactive?->value) {
            return $this->error('Your account has been banned. Please contact to support team', 422);
        }

        $token = auth()->user()->createToken('Admin API Token')->plainTextToken;

        return $this->success($token, 'Logged in successfully');
    }

    public function profile(): JsonResponse
    {
        if (auth()->user()->status?->value == StatusEnum::Inactive?->value) {
            return $this->error('Your account has been banned. Please contact to support team', 422);
        }

        return $this->success(UserResource::make(auth()->user()), 'Profile fetched successfully');
    }

    public function resetPassword(Request $request)
    {
        try {
            $user = User::where('email', $request->input('email'))->firstOrFail();
            $this->generateEmailVerificationCode($user);

            return $this->success($user, 'Reset link has been sent to your email');
        } catch (Exception $e) {

            return $this->somethingWentWrong($e);
        }
    }

    public function verifyEmail($id, $slug, $expires): JsonResponse
    {
        $current_date = now()->format('YmdHis');
        $check = [
            'user_id' => $id,
            'slug' => $slug,
            'expires_at' => $expires,
        ];
        $verify = EmailVerification::where($check)->firstOrFail();
        if ($current_date <= $expires) {
            return $this->success($verify, 'Email verified successfully');
        }

        return $this->error('Password reset link expired', 422);
    }

    public function isVerifiedLink($id): JsonResponse
    {
        $where = [
            'user_id' => $id,
        ];
        try {
            $emailVerify = EmailVerification::where($where)->firstOrFail();
            return $this->success($emailVerify, 'User verified');

        } catch (Exception $e) {
            return $this->somethingWentWrong($e);
        }

    }

    public function changePassword($id, Request $request)
    {
        $request->validate([
            'password' => ['required', 'different:old_password'],
        ]);
        if ($request->input('password') === $request->input('confirm_password')) {
            $user = User::findOrFail($id);
            $user->update(['password' => bcrypt($request->input('password'))]);
            EmailVerification::where('user_id', $id,)->delete();

            return $this->success($user, 'Password changed successfully');
        }

        return $this->error('Confirmation Password Not Match', 422);
    }

    public function generateEmailVerificationCode($user)
    {
        $slug = Str::random(60);
        $url = config('services.frontend.url');
        $expires_at = now()->addMinutes(10)->format('YmdHis');
        $verification_url = $url . "/verify-email/" . $user->id . '/' . $slug . '/' . $expires_at;
        $_data = [
            'user_id' => $user->id,
            'slug' => $slug,
            'expires_at' => $expires_at,
            'url' => $verification_url,
        ];
        EmailVerification::create($_data);
        Notification::send($user, new ResetLink($verification_url));
    }

}
