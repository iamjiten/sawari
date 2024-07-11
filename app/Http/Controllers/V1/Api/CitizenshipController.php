<?php

namespace App\Http\Controllers\V1\Api;

use App\Enums\AcceptRejectEnum;
use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\CitizenshipRequest;
use App\Http\Resources\CitizenshipResource;
use App\Models\Citizenship;
use App\Notifications\KycVerificationNotification;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class CitizenshipController extends SuperController
{
    public array $withAll = [
        'user',
    ];

    public function __construct()
    {
        parent::__construct(
            Citizenship::class,
            CitizenshipResource::class,
            CitizenshipRequest::class,
            CitizenshipRequest::class
        );
    }

    public function selfCitizenship()
    {
        $citizenship = Citizenship::where('user_id', auth()->id())->first();
        if ($citizenship) {
            return CitizenshipResource::make($citizenship);
        }
        return $this->error('citizenship not found', 500);

    }

    public function changeActionStatus($id, $status)
    {
        if (!in_array($status, [1, 2, 3, 4])) {
            return $this->error('Status not found', 500);
        }
        try {
            DB::beginTransaction();
            $citizenship = $this->model::findOrFail($id);
            if ($status == 3) {
                $data = [
                    'status' => $status,
                    'remarks' => \request()->remark
                ];
            } else {
                $data = [
                    'status' => $status,
                ];
                $citizenship->user->notify(new KycVerificationNotification($citizenship, false));
            }
            $citizenship->update($data);
            DB::commit();


            return $this->success(['status' => true], 'Status change successfully');
        } catch (Exception $e) {
            DB::rollBack();

            return $this->somethingWentWrong($e, 'Failed to change status');
        }
    }
}
