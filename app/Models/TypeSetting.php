<?php

namespace App\Models;

use App\Enums\StatusEnum;
use App\Enums\UserTypeEnum;
use App\Traits\Crud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TypeSetting extends Model
{
    use SoftDeletes, Crud, Mediable, LogsActivity;

    protected $table = "type_settings";

    public const PERMISSION_SLUG = 'type-settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'type',
        'status',
        'extra',
        'parent_id',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'extra' => 'array',
        'status' => StatusEnum::class
    ];


    public static $logName = "Type Setting";

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
        return $query->where('name', 'LIKE', '%' . $searchquery . '%')
            ->orwhere('description', 'LIKE', '%' . $searchquery . '%');
    }

    public function scopeType($query, $val)
    {
        if ($val) {
            $query->where('type', $val);
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
