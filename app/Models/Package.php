<?php

namespace App\Models;

use App\Enums\PackageStatusEnum;
use App\Enums\StatusEnum;
use App\Enums\UserTypeEnum;
use App\Exceptions\ApiResponder;
use App\Traits\Crud;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Str;

class Package extends Model
{
    use SoftDeletes, Crud, ApiResponder, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    public const PERMISSION_SLUG = 'packages';

    protected $fillable = [
        'uuid',
        'name',
        'sender_id',
        'receiver_id',
        'is_receiver_user',
        'package_category_id',
        'package_sensible_id',
        'package_size_id',
        'sender_address',
        'sender_latitude',
        'sender_longitude',
        'receiver_address',
        'receiver_latitude',
        'receiver_longitude',
        'sender_receiver_distance_unit',
        'sender_receiver_distance',
        'amount',
        'status',
        'extra',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'extra' => 'array',
        'receiver_latitude' => 'double',
        'receiver_longitude' => 'double',
        'sender_longitude' => 'double',
        'sender_latitude' => 'double',
        'status' => PackageStatusEnum::class
    ];

    protected $hidden = ['pivot'];

    public static $logName = "Package";

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

    // merge request will be called by controller
    // in store function for adding additional data in request of package
    public function mergeRequest($id = null): array|object
    {
        // Calculation for total cost of package
        $packageCategoryId = request()->input('package_category_id');
        $packageSensibleId = request()->input('package_sensible_id');
        $packageSizeId = request()->input('package_size_id');

        $packageCategory = TypeSetting::find($packageCategoryId);
        $packageSensible = TypeSetting::find($packageSensibleId);
        $packageSize = PackageSize::find($packageSizeId);

        $totalAmount = ($packageCategory ? $packageCategory->price : 0) +
            ($packageSensible ? $packageSensible->price : 0) +
            ($packageSize ? $packageSize->price : 0);

        $_data = [
            'uuid' => Str::uuid()->toString(),
            'sender_id' => auth()->id(),
            'amount' => $totalAmount
        ];

        // If receiver ID is provided, check if it exists in users table
        $user = User::where('mobile', request()->input('receiver_mobile'))->first('id');

        if ($user) {
            $_data['receiver_id'] = $user->id;
            $_data['is_receiver_user'] = true;
        } else {
            try {
                DB::beginTransaction();

                // Create a new receiver
                $receiver = Receiver::create([
                    'user_id' => auth()->id(),
                    'name' => request()->input('receiver_name'),
                    'mobile' => request()->input('receiver_mobile'),
                    'nick_name' => request()->input('receiver_nick_name'),
                    'address' => request()->input('receiver_address'),
                    'latitude' => request()->input('receiver_latitude'),
                    'longitude' => request()->input('receiver_longitude')
                ]);

                DB::commit();

                $_data['receiver_id'] = $receiver->id;
                $_data['is_receiver_user'] = false;
            } catch (Exception $e) {
                DB::rollBack();

                return [];
            }
        }

        return $_data;
    }

    public function scopeQueryfilter($query, $searchquery)
    {
        return $query->where('name', 'LIKE', '%' . $searchquery . '%');
    }


    public function scopeStatus($query, $val)
    {
        if ($val) {
            $query = $query->where('status', $val);
        }
        return $query;
    }

    public function scopeSender($query, $val)
    {
        if ($val) {
            $query = $query->whereIn('sender_id', $val);
        }
        return $query;
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiverAsUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(Receiver::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TypeSetting::class, 'package_category_id');
    }

    public function sensible(): BelongsTo
    {
        return $this->belongsTo(TypeSetting::class, 'package_sensible_id');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, OrderPackage::class, 'package_id', 'order_id');
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(PackageSize::class, 'package_size_id');
    }

    public function scopeSelfSender($query)
    {
        return $query->whereSenderId(auth()->id());
    }

    public function scopePending($query)
    {
        return $query->whereStatus(PackageStatusEnum::Pending->value);
    }

    public function scopeSelfReceiver($query)
    {
        return $query->whereIsReceiverUser(1)->whereReceiverId(auth()->id());
    }

    protected function senderReceiverDistance(): Attribute
    {
        return Attribute::make(
            set: fn($value) => round($value, 3)
        );
    }

    public function scopeUser($query)
    {
        $query->when(auth()->user()->type != UserTypeEnum::Admin, function ($query) {
            return $query->whereStatus(StatusEnum::Active->value);
        });
        return $query;
    }
}
