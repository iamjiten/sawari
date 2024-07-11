<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Enums\UserTypeEnum;
use App\Helpers\SendSMS;
use App\Traits\Crud;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Opcodes\LogViewer\Log;
use Plank\Mediable\Facades\MediaUploader;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;


/**
 * @method static findOrFail($id)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, Mediable, LogsActivity, HasRoles, Crud;

    const PERMISSION_SLUG = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'gender',
        'email',
        'mobile',
        'photo',
        'password',
        'dob',
        'status',
        'is_online',
        'last_seen',
        'type',
        'address',
        'latitude',
        'longitude',
        'kyc_status',
        'ask_to_rate_trip',
        'merchant_id',
        'extra',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'extra' => 'array',
        'status' => StatusEnum::class,
        'type' => UserTypeEnum::class,
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    public static $logName = "User";

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->device = request()->userAgent();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return static::$logName . " has been {$eventName}";
    }

    /**
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        $logOptions = new LogOptions();
        $logOptions->logAll();
        $logOptions->logName = static::$logName;
        $logOptions->logOnlyDirty();

        return $logOptions;
    }

    public function scopeQueryfilter($query, $searchquery)
    {
        return $query->where('name', 'LIKE', '%' . $searchquery . '%')
            ->orWhere('email', 'LIKE', '%' . $searchquery . '%')
            ->orWhere('mobile', 'LIKE', '%' . $searchquery . '%');
    }

    public function scopeType($query, $val)
    {
        if ($val) {
            $query = $query->where('type', $val);
        }
        return $query;
    }

    public function scopeMerchant($query, $val)
    {
        if ($val == 1) {
            $query = $query->whereNotNull('merchant_id');
        } else {
            $query = $query->whereNull('merchant_id');
        }
        return $query;
    }

    public function otps(): HasMany
    {
        return $this->hasMany(Otp::class);
    }

    public function drivingLicense(): HasOne
    {
        return $this->hasOne(DrivingLicense::class);
    }

    public function citizenship(): HasOne
    {
        return $this->hasOne(Citizenship::class);
    }

    public function trip()
    {
        return $this->hasMany(Trip::class);
    }

    public function wallet()
    {
        return $this->hasMany(Wallet::class);
    }

    public function beforeUpdateProcess()
    {
        if (!request()->type == 3) {
            if ($this->id != auth()->id()) {
                return [
                    'status' => 401,
                    'message' => 'unauthorized'
                ];
            }
            return [
                'status' => 200,
                'message' => 'can edit'
            ];
        }
    }

    public function afterCreateProcess(): void
    {
        try {
            if (request()->file('image')) {
                $media = MediaUploader::fromSource(request()->file('image'))
                    ->toDestination(
                        config('filesystems.multi') ? 'minio_write'
                            : config('filesystems.default'),
                        "postImage"
                    )
                    ->makePublic()
                    ->upload();

                $this->syncMedia($media, 'profile');
            }
            if (request()->type == 3 && request()->has('role')) {
                $role = Role::findById(request()->role, 'api');

                $this->syncRoles($role);
            }

            if (request()->type == 3) {
                $password = rand(100000000, 999999999);

                $this->update([
                    'password' => bcrypt($password)
                ]);
                (new SendSMS())->sendOtp($this->mobile, 'Hello Sawari - Your Login Password is ' . $password);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error($e);
        }
    }

    public function afterUpdateProcess(): void
    {
        if (request()->file('image')) {
            $media = MediaUploader::fromSource(request()->file('image'))
                ->toDestination(
                    config('filesystems.multi') ? 'minio_write'
                        : config('filesystems.default'),
                    "postImage"
                )
                ->makePublic()
                ->upload();

            $this->syncMedia($media, 'profile');
        }

        if (request()->type == 3 && request()->has('role')) {
            $role = Role::findById(request()->role, 'api');

            $this->syncRoles($role);
        }
    }

    public function afterDeleteProcess(): void
    {
        $this->update([
            'mobile' => $this->mobile . rand(0000, 9999),
            'email' => $this->email ? $this->email . rand(0000, 9999) : null,
        ]);
    }

    public function vehicle(): HasOne
    {
        return $this->hasOne(Vehicle::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function rentalOrder(): HasMany
    {
        return $this->hasMany(RentalOrder::class);
    }

    public function settlement(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    public function setRiderLocation($location): bool
    {
        return $this->update([
            'extra' => $location
        ]);
    }

    public function rateTrip(): BelongsTo
    {
        return $this->belongsTo(Trip::class, 'ask_to_rate_trip');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
