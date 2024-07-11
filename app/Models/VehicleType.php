<?php

namespace App\Models;


use App\Enums\StatusEnum;
use App\Enums\UserTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Crud;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VehicleType extends Model
{
    // use HasFactory;
    use SoftDeletes, Crud, Mediable, LogsActivity;

    protected $table = 'vehicle_types';

    public const PERMISSION_SLUG = 'vehicle-types';

    protected $fillable = [
        'name',
        'weight_capacity',
        'weight_unit',
        'distance_unit',
        'per_distance_unit_cost',
        'base_fare',
        'status',
        'extra',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    public static $logName = "Vehicle Type";

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

    public function scopeStatus($query, $val)
    {
        if ($val || $val == 0) {
            $query->where('status', $val);
        }
        return $query;
    }

    public function scopeStatusMobile($query, $val)
    {
        return $query->where('status', $val);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeUser($query)
    {
        $query->when(auth()->user()->type != UserTypeEnum::Admin, function ($query) {
            return $query->whereStatus(StatusEnum::Active->value);
        });
        return $query;
    }

}
