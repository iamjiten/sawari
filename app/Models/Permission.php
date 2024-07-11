<?php

namespace App\Models;

use App\Traits\Crud;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Permission extends \Spatie\Permission\Models\Permission
{
    use LogsActivity, Crud, LogsActivity;

    public const PERMISSION_SLUG = 'permissions';

    protected $fillable = [
        'name',
        'display_name',
        'guard_name',
        'created_by',
        'updated_by',
    ];

    public function mergeRequest($id = null): array
    {
        if (!request()->has('guard_name')) {
            $guard_name = 'api';
            $_data['guard_name'] = $guard_name;
        }
        if (!request()->has('display_name')) {
            $_data['display_name'] = request()->input('name');
        }
        if (!$id) {
            $_data['created_by'] = auth()->id();
        }
        $_data['updated_by'] = auth()->id();

        return $_data;
    }

    public static $logName = "Permissions";

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
