<?php

namespace App\Models;

use App\Traits\Crud;
use App\Traits\Super;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RentalArea extends Model
{
    use HasFactory, Super, Crud, LogsActivity;

    protected $fillable = [
        'province',
        'district',
        'city',
        'area',
        'status',
        'extra'
    ];

    protected $casts = [
        'extra' => 'array'
    ];

    public static $logName = "Rental Area";

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

    public function locations(): HasMany
    {
        return $this->hasMany(RentalLocation::class, 'area_id');
    }
}
