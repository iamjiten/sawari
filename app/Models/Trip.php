<?php

namespace App\Models;

use App\Enums\TripStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Crud;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Trip extends Model
{
    use HasFactory, SoftDeletes, Crud, LogsActivity;

    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'status',
        'reason_id',
        'action_by',
        'action_at',
        'extra',
    ];

    protected $casts = [
        'extra' => 'array',
        'status' => TripStatusEnum::class,
    ];

    public static $logName = "Trip";

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
//
//    public function order(): BelongsTo
//    {
//        return $this->belongsTo(Order::class);
//    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'reason_id')->where('key', 'reason');
    }

    public function scopeRiderActivity($query)
    {
        return $query->whereUserId(auth()->id())
            ->type(Order::class)
            ->where('status', '!=', TripStatusEnum::Assigned)
            ->latest();
    }

    public function scopeMoverActivity($query)
    {
        return $query->whereUserId(auth()->id())
            ->type(MoverOrder::class)
            ->where('status', '!=', TripStatusEnum::Assigned)
            ->with(['order' => fn($q) => $q->select('id', 'slug', 'shifting_from_address', 'shifting_to_address', 'galli_distance')])
            ->latest();
    }

    public function order(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeType($query, $model)
    {
        return $query->where('order_type', $model);
    }

    public function settlement(): HasOne
    {
        return $this->hasOne(Settlement::class);
    }
}
