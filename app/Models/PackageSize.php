<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Enums\UserTypeEnum;
use App\Traits\Crud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PackageSize extends Model
{
    use SoftDeletes, Crud, Mediable, LogsActivity;

    protected $table = "package_sizes";

    public const PERMISSION_SLUG = 'sizes';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'weight',
        'weight_unit',
        'price',
        'status',
        'created_by',
        'updated_by',
        'extra',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'extra' => 'array'
    ];

    public static $logName = "Package Size";

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

    public function afterCreateProcess(): void
    {
        if (is_array(request()->input('icon'))) $this->syncMedia(request()->input('icon'), 'icon');
    }

    public function afterUpdateProcess(): void
    {
        if (is_array(request()->input('icon'))) $this->syncMedia(request()->input('icon'), 'icon');
    }

    public function scopeQueryfilter($query, $searchquery)
    {
        return $query->where('name', 'LIKE', '%' . $searchquery . '%');
    }

    public function scopeUser($query)
    {
        $query->when(auth()->user()->type != UserTypeEnum::Admin, function ($query) {
            return $query->whereStatus(StatusEnum::Active->value);
        });
        return $query;
    }

    protected function scopeOrderByDescSize($query)
    {
        return $query->orderBy('weight');
    }
}
