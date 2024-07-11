<?php

namespace App\Models;


use App\Enums\OrderStatusEnum;
use App\Interfaces\OrderInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Crud;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model implements OrderInterface
{
    // use HasFactory;
    use SoftDeletes, Crud, LogsActivity;

    public const PERMISSION_SLUG = 'package-orders';

    public $timestamps = true;

    protected $fillable = [
        'slug',
        'token',
        'receiver_token',
        'user_id',
        'delivery_type_id',
        'vehicle_type_id',
        'scheduled_at',
        'status',
        'actual_amount',
        'discount_amount',
        'net_amount',
        'promo_code',
        'route',
        'expires_at',
        'extra', // Base fare, Distance Cost, Delivery Cost, Vat, Package Cost
    ];

    protected $casts = [
        'status' => OrderStatusEnum::class,
        'extra' => 'array',
        'route' => 'array',
    ];

    public static $logName = "Package Order";

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

    public function mergeRequest($id = null): array
    {
        $latestOrder = Order::latest()->first();
        $latestSlugNumber = intval($latestOrder ? preg_replace('/[^0-9]/', '', $latestOrder->slug) : date('Ymd') . '0000');
        $findVehicleType = VehicleType::findOrFail(request()->input('vehicle_type_id'));
        $actualAmount = 0;
        $distanceCost = 0;
        foreach (request()->input('packages') as $id) {
            $package = Package::findOrFail($id);
            $actualAmount += $package->amount;
            $distanceCost += round(($package->sender_receiver_distance ?? 0) * ($findVehicleType->per_distance_unit_cost ?? 0), 2);
        }
        // extra key for cost break down
        $extra = [
            [
                'key' => 'base_fare',
                'title' => 'Base Fare',
                'amount' => $findVehicleType->base_fare,
            ], [
                'key' => 'distance_cost',
                'title' => 'Distance Cost',
                'amount' => $distanceCost,
            ], [
                'key' => 'delivery_cost',
                'title' => 'Delivery Cost',
                'amount' => 0,
            ],
        ];

        $data = [
            'token' => rand(100000, 999999), // Generate token
            'receiver_token' => rand(100000, 999999), // Generate receiver token
            'slug' => 'ORD' . ($latestSlugNumber + 1), // Generate slug
            'user_id' => auth()->id(),
            'actual_amount' => $actualAmount,
            'net_amount' => $actualAmount + $findVehicleType->base_fare + $distanceCost,
            'extra' => $extra,
        ];
        if (!request()->has('scheduled_at')) {
            $data['scheduled_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    public function scopeQueryfilter($query, $searchquery)
    {
        return $query->where('slug', 'LIKE', '%' . $searchquery . '%');
    }

    public function afterCreateProcess()
    {
        $request = request();
        $this->packages()->sync($request->packages);
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

    public function scopeSelf($query)
    {
        return $query->whereUserId(auth()->id());
    }

    public function scopeReceivedOrders($query)
    {
        return $query->whereStatus(OrderStatusEnum::Received->value)
            ->where('expires_at', '>', now());
    }

//    public function scopeSelfOngoing($query)
//    {
//        return $query
//            ->whereUserId(auth()->id())
//            ->where('status', OrderStatusEnum::Received->value)
//            ->where('expires_at', '>', now());
//    }

    public function scopeSelfOngoing($query)
    {
        return $query
            ->whereUserId(auth()->id())
            ->where('status', OrderStatusEnum::Received->value);
    }

    public function scopeSelfActivity($query)
    {
        return $query
            ->whereUserId(auth()->id())
            ->whereNotIn('status', [OrderStatusEnum::Pending->value, OrderStatusEnum::Received->value])
            ->latest();
    }

    public function scopeSelfReceiverActivity($query)
    {
        return $query
            ->whereNotIn('status', [OrderStatusEnum::Pending->value, OrderStatusEnum::Received->value])
            ->whereHas('packages', function ($q) {
                $q->whereIsReceiverUser(1)
                    ->whereReceiverId(auth()->id());
            })
            ->latest();
    }


    public function scopeExceptSelf($query)
    {
        return $query->where('user_id', '!=', auth()->id());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, OrderPackage::class, 'order_id', 'package_id');
    }

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function deliveryType(): BelongsTo
    {
        return $this->belongsTo(DeliveryType::class, 'delivery_type_id');
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactional')->orderBy('id', 'desc');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function orderTrack(): HasMany
    {
        return $this->hasMany(OrderTrack::class, 'order_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
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
