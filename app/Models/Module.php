<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Enums\UserTypeEnum;
use App\Exceptions\ApiResponder;
use App\Traits\Crud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Module extends Model
{
    use SoftDeletes, Crud, ApiResponder, LogsActivity;

    protected $fillable = [
        'name',
        'status',
        'icon',
        'slug',
        'created_by',
        'updated_by'
    ];

    public static $logName = "Module";

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

    public function scopeUser($query){
        $query->when(auth()->user()->type != UserTypeEnum::Admin, function($query) {
            return $query->whereStatus(StatusEnum::Active->value);
        });
        return $query;
    }

    public function vehicles(): BelongsToMany
    {
        return $this->belongsToMany(Vehicle::class, 'vehicle_module', 'module_id', 'vehicle_id');
    }
}
