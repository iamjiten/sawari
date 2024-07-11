<?php

namespace App\Models;

use App\Traits\Crud;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Rating extends Model
{
    use HasFactory, SoftDeletes, Crud, LogsActivity;

    protected $fillable = [
        "order_id",
        "user_id",
        "trip_id",
        "rating",
        "review",
        "rated_by",
        "extra"
    ];

    protected $hidden = [
        'laravel_through_key'
    ];

    protected $casts = [
        'extra' => 'array'
    ];

    public static $logName = "Rating";

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
        $trip = Trip::findOrFail(request()->trip_id, ['user_id', 'order_id']);
        return [
            'user_id' => $trip->user_id,
            'order_id' => $trip->order_id,
            'rated_by' => auth()->id(),
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rated_by');
    }

}
