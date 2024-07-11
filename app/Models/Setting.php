<?php

namespace App\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\Crud;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Setting extends Model
{
    use HasFactory, SoftDeletes, Crud, LogsActivity;

    public const PERMISSION_SLUG = 'settings';

    protected $fillable = [
        'key',
        'value',
        'value_json',
        'parent_id',
        'editable',
        'display_order',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
        'value_json' => 'array',
    ];

    public static $logName = "Setting";

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

    public function scopeQueryfilter($query, $searchquery)
    {
        return $query->where('value', 'LIKE', '%' . $searchquery . '%');
    }


    protected function extra(): Attribute
    {
        return Attribute::make(
            get: fn($value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }

    public function scopeKey($query, $val)
    {
        if ($val) {
            $query->where('key', $val);
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

    public function scopeParentId($query, $val)
    {
        if ($val || $val == 0) {
            $query->where('parent_id', $val);
        }
        return $query;
    }


    public function parent(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'parent_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function brand(): HasMany
    {
        return $this->hasMany(Setting::class, 'parent_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'parent_id');
    }

    public function brandToModel(): HasMany
    {
        return $this->hasMany(Setting::class, 'parent_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'parent_id');
    }

    public function modelToColor(): HasMany
    {
        return $this->hasMany(Setting::class, 'parent_id');
    }

    public function children()
    {
        return Setting::query()
            ->select('key', 'value', 'value_json')
            ->where('parent_id', $this->id)
            ->get()
            ->groupBy('key');
    }


}
