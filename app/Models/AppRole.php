<?php

namespace App\Models;

use App\Traits\Crud;
use Illuminate\Database\Eloquent\SoftDeletes;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AppRole extends \Spatie\Permission\Models\Role
{
    use Crud, LogsActivity;

    public const PERMISSION_SLUG = 'roles';

    protected $attributes = [
        'guard_name' => 'api'
    ];

    protected $fillable = [
        'name',
        'display_name',
        'is_default',
        'guard_name',
        'created_by',
        'updated_by',
    ];

    public static $logName = "AppRole";

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
        if (!request()->has('guard_name')) {
            $guard_name = 'api';
            $_data['guard_name'] = $guard_name;
        }
        if (!$id) {
            $_data['created_by'] = auth()->id();
        }
        $_data['updated_by'] = auth()->id();

        return $_data;
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function afterCreateProcess(): void
    {
        if (request()->has('permissions')) {
            $this->syncPermissions(request()->get('permissions'));
        }

    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function afterUpdateProcess(): void
    {
        if (request()->has('permissions')) {
            $this->syncPermissions(request()->get('permissions'));
        }
    }
}
