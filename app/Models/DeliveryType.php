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

class DeliveryType extends Model
{
    // use HasFactory;
    use SoftDeletes, Crud, Mediable, LogsActivity;

    protected $table = 'delivery_types';

    public const PERMISSION_SLUG = 'delivery-types';

    protected $fillable = [
        'name',
        'description',
        'min_day',
        'max_day',
        'price',
        'status',
        'extra',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    public static $logName = "Delivery Type";

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
            ->orWhere('description', 'LIKE', '%' . $searchquery . '%');
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
