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

class Blog extends Model
{
    use SoftDeletes, Crud, Mediable, LogsActivity;

    public const PERMISSION_SLUG = 'blogs';

    protected $fillable = [
        'title',
        'slug',
        'body',
        'status',
        'created_by',
        'updated_by',
    ];

    public static $logName = "Blogs";

    public function tapActivity(Activity $activity)
    {
        $activity->ip = request()->ip();
        $activity->device = request()->userAgent();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return static::$logName . " has been {$eventName}";
    }

    public function mergeRequest()
    {
        $data = [];
        if (!request()->slug) {
            $data['slug'] = rand(00000, 999999);
        }
        return $data;
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
        return $query->where('title', 'LIKE', '%' . $searchquery . '%')
            ->orWhere('body', 'LIKE', '%' . $searchquery . '%');
    }

    public function afterCreateProcess(): void
    {
        if (is_array(request()->input('icon'))) $this->syncMedia(request()->input('icon'), 'icon');
    }

    public function afterUpdateProcess(): void
    {
        if (is_array(request()->input('icon'))) $this->syncMedia(request()->input('icon'), 'icon');
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
}
