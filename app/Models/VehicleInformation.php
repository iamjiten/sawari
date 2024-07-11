<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VehicleInformation extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'vehicle_informations';

    protected $fillable = [
        'vehicle_id',
        'detail_info',
        'per_day_fare',
        'per_day_driver_fare',
        'withDriver', // '0 no driver| 1 with driver | 2 might'
        'extra'
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    public static $logName = "Vehicle Information";

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

}
