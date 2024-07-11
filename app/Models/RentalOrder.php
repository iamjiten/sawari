<?php

namespace App\Models;

use App\Enums\RentalOrderStatusEnum;
use App\Interfaces\OrderInterface;
use App\Services\RentalOrderService;
use App\Traits\Crud;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RentalOrder extends Model implements OrderInterface
{
    use HasFactory, Crud, SoftDeletes, LogsActivity;

    protected $fillable = [
        'user_id',
        'slug',
        'status',
        'actual_amount',
        'discount_amount',
        'net_amount',
        'pickup_location_id',
        'drop_off_location_id',
        'pickup_date',
        'drop_off_date',
        'withDriver',
        'reason_id',
        'remarks',
        'extra',
    ];

    protected $casts = [
        'status' => RentalOrderStatusEnum::class,
        'extra' => 'array'
    ];

    public static $logName = "Rental Order";

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

    public function scopeUser($query, $val)
    {
        if ($val) {
            $query = $query->whereIn('user_id', $val);
        }
        return $query;
    }

    public function scopeQueryfilter($query, $searchquery)
    {
        return $query->where('slug', 'LIKE', '%' . $searchquery . '%');
    }

    public function scopeVehicle($query, $val)
    {
        if ($val) {
            $query = $query->whereHas('vehicles', function ($q) use ($val) {
                $q->where('vehicle_type_id', $val);
            });
        }
        return $query;
    }

    public function mergeRequest(): array
    {
        $latestOrder = RentalOrder::latest()->first();
        $latestSlugNumber = intval($latestOrder ? preg_replace('/[^0-9]/', '', $latestOrder->slug) : (date('Ymd') . '0000'));
        $request = request();
        $rentalOrderService = new RentalOrderService();
        $vehicle = Vehicle::query()
            ->select('id')
            ->withWhereHas('vehicleInformation', fn($q) => $q
                ->select('vehicle_id', 'per_day_fare', 'per_day_driver_fare', 'discount_percent', 'withDriver')
            )->find($request->vehicle_id);

        $booked_days = $rentalOrderService->calculateReserveDays(
            $request->pickup_date,
            $request->drop_off_date
        );
        $vehicleInformation = $vehicle->vehicleInformation;

        $vehicle_fare = $vehicleInformation->per_day_fare * $booked_days;
        $withDriver = $vehicleInformation->withDriver;
        $driver_fare = $vehicleInformation->per_day_driver_fare * $booked_days;

        $actual_amount = round($rentalOrderService->calculateActualAmount(
            $vehicle_fare,
            $driver_fare,
            $withDriver
        ));

        $discount = round($vehicleInformation->discount_percent * $actual_amount / 100, 2);

        $extra = [
            'duration' => $rentalOrderService->calculateReserveDuration(
                $request->pickup_date,
                $request->drop_off_date
            ),
            'payment_break_down' => [
                [
                    'key' => 'vehicle_fare',
                    'title' => 'Vehicle Fare',
                    'format' => $booked_days . '*' . $vehicleInformation->per_day_fare,
                    'amount' => $vehicle_fare,
                    'type' => "positive",
                ],
                [
                    'key' => 'driver_fare',
                    'title' => 'Driver Cost',
                    'format' => $booked_days . '*' . $vehicleInformation->per_day_driver_fare,
                    'amount' => $driver_fare,
                    'type' => "positive",
                ],
                [
                    'key' => 'discount',
                    'title' => 'Discount',
                    'format' => $vehicleInformation->discount_percent . ' %',
                    'amount' => $discount,
                    'type' => "negative",
                ]
            ]
        ];

        return [
            'user_id' => auth()->id(),
            'slug' => 'R-ORD' . ($latestSlugNumber + 1), // Generate slug
            'withDriver' => $withDriver,
            'actual_amount' => $actual_amount,
            'discount_amount' => $discount,
            'net_amount' => $actual_amount - $discount,
            'extra' => $extra
        ];
    }

    public function scopeSelfStatus($query, $val)
    {
        return $query
            ->where('status', $val)
            ->whereUserId(auth()->id());
    }

    public function scopeSelfActivity($query)
    {
        return $query
            ->whereUserId(auth()->id())
            ->whereNotIn('status', [RentalOrderStatusEnum::Pending->value, RentalOrderStatusEnum::Received->value])
            ->with(['vehicles' => fn($q) => $q->get()])
            ->with('transaction:pid,amount,status,channel,transactional_id')
            ->latest();
    }

    public function afterCreateProcess()
    {
        $this->vehicles()->attach(request()->input('vehicle_id'));
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactional')->orderBy('id', 'desc');
    }

    public function pickupLocation(): BelongsTo
    {
        return $this->belongsTo(RentalLocation::class, 'pickup_location_id');
    }

    public function dropOffLocation(): BelongsTo
    {
        return $this->belongsTo(RentalLocation::class, 'drop_off_location_id');
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_order', 'order_id', 'vehicle_id')->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booked(): HasOne
    {
        return $this->hasOne(BookedRentalVehicle::class, 'order_id');
        // make this hasMany when one order can have multiple vehicle
    }

    public function assignedVehicle(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'assigned_vehicle', 'order_id', 'vehicle_id')->withTimestamps();
        // make this hasMany when one order can have multiple vehicle
    }

    public function scopeReceivedOrders($query)
    {
        return $query->whereStatus(RentalOrderStatusEnum::Received->value);
    }

    public function scopeExceptSelf($query)
    {
        return $query->where('user_id', '!=', auth()->id());
    }

    public function scopeSelfOngoing($query)
    {
        return $query
            ->whereUserId(auth()->id())
            ->where('status', RentalOrderStatusEnum::Received->value);
    }

    public function scopeStatus($query, $val)
    {
        if ($val || $val == 0) {
            $query->where('status', $val);
        }
        return $query;
    }

    public function track(): MorphOne
    {
        return $this->morphOne(OrderTrack::class, 'trackable');
    }


}
