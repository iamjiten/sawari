<?php

namespace App\Models;

use App\Enums\MoverStatusEnum;
use App\Enums\TripStatusEnum;
use App\Interfaces\OrderInterface;
use App\Traits\Crud;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MoverOrder extends Model implements OrderInterface
{
    use HasFactory, Crud, LogsActivity;

    protected $fillable = [
        'slug',
        'otp',
        'user_id',
        'distance',
        'status',
        'actual_amount',
        'discount_amount',
        'net_amount',
        "vehicle_type_id",
        "shifting_from_address",
        "shifting_from_longitude",
        "shifting_from_latitude",
        "shifting_to_address",
        "shifting_to_longitude",
        "shifting_to_latitude",
        "shifting_at",
        "no_of_rooms",
        "galli_distance",
        "no_of_loader",
        "no_of_trips",
        'expires_at',
        'extra',
        "route"
    ];

    protected $casts = [
        'extra' => 'array',
        'status' => MoverStatusEnum::class,
        'route' => 'array',
        'galli_distance' => 'float',
        'shifting_to_latitude' => 'double',
        'shifting_to_longitude' => 'double',
        'shifting_from_latitude' => 'double',
        'shifting_from_longitude' => 'double'
    ];

    public static $logName = "Mover Order";

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

    public function mergeRequest()
    {
        $request = request();
        $vehicleType = VehicleType::find($request->vehicle_type_id);

        $latestOrder = MoverOrder::select('id', 'slug')->latest()->first();
        $latestSlugNumber = intval($latestOrder ? preg_replace('/[^0-9]/', '', $latestOrder->slug) : date('Ymd') . '0000');

        $min_distance = 0.5; //500 meter  (this comes from database)
        $discount = 0;  // make this dynamic (comes from database)

        if ($request->distance <= $min_distance) {
            $actual_amount = round($vehicleType->base_fare, 2);

        } else {
            $actual_amount = round($request->distance * $vehicleType->per_distance_unit_cost, 2);
        }
        $actual_amount *= ($request->no_of_trips == 0) ? 1 : $request->no_of_trips;
        $net_amount = round(($actual_amount - $discount), 2);
        $extra = [
            [
                'key' => 'actual_amount',
                'title' => 'Actual amount',
                'amount' => $actual_amount,
            ], [
                'key' => 'discount',
                'title' => 'Discount',
                'amount' => $discount,
            ], [
                'key' => 'net_amount',
                'title' => 'Net amount',
                'amount' => $net_amount
            ],
        ];

        return [
            'slug' => 'M-ORD' . ($latestSlugNumber + 1),
            'otp' => rand(100000, 999999),
            'user_id' => auth()->id(),
            'actual_amount' => $actual_amount,
            'discount_amount' => $discount,
            'net_amount' => $net_amount,
            'extra' => $extra
        ];
    }

    public function scopeReceivedOrders($query)
    {
        return $query->whereStatus(MoverStatusEnum::Received->value)
            ->where('expires_at', '>', now());
    }

    public function scopeQueryfilter($query, $searchquery)
    {
        return $query->where('slug', 'LIKE', '%' . $searchquery . '%');
    }

    public function scopeUser($query, $val)
    {
        if ($val) {
            $query = $query->whereIn('user_id', $val);
        }
        return $query;
    }

    public function scopeVehicle($query, $val)
    {
        if ($val) {
            $query = $query->where('vehicle_type_id', $val);
        }
        return $query;
    }


    public function scopeStatus($query, $val)
    {
        if ($val || $val == 0) {
            $query->where('status', $val);
        }
        return $query;
    }

    public function scopeSelfActivity($query)
    {
        return $query
            ->whereUserId(auth()->id())
            ->whereNotIn('status', [MoverStatusEnum::Pending->value, MoverStatusEnum::Received->value])
            ->withWhereHas('trip', fn($q) => $q
                ->select('id', 'order_id', 'user_id')
                ->with([
                    'user' => fn($q) => $q
                        ->select('id', 'name', 'mobile', 'photo')
                        ->with('vehicle:id,user_id,number_plate,image')
                ]))
            ->latest();
    }

    public function scopeSelfOngoing($query)
    {
        return $query
            ->whereUserId(auth()->id())
            ->where('status', MoverStatusEnum::Received->value);
    }


    public function scopeExceptSelf($query)
    {
        return $query->where('user_id', '!=', auth()->id());
    }


    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactional')->orderBy('id', 'desc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trip(): MorphOne
    {
        return $this->morphOne(Trip::class, 'order');
    }

    public function track(): MorphOne
    {
        return $this->morphOne(OrderTrack::class, 'trackable');
    }
}
