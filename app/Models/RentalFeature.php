<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Plank\Mediable\Facades\MediaUploader;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RentalFeature extends Model
{
    use HasFactory, Mediable, LogsActivity;

    protected $fillable = [
        'module',
        'category',
        'key',
        'value',
        'extra'
    ];

    protected $hidden = [
        "pivot"
    ];

    public static $logName = "Rental Feature";

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

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function afterCreateProcess(): void
    {
        if (request()->file('icon')) {
            $media = MediaUploader::fromSource(request()->file('icon'))
                ->toDestination(config('filesystems.multi') ? 'minio_write'
                    : config('filesystems.default'),
                    "postImage")
                ->makePublic()
                ->upload();

            $this->syncMedia($media, 'icon');
        }
    }

    public function afterUpdateProcess(): void
    {
        if (request()->file('icon')) {
            $media = MediaUploader::fromSource(request()->file('icon'))
                ->toDestination(config('filesystems.multi') ? 'minio_write'
                    : config('filesystems.default'),
                    "postImage")
                ->makePublic()
                ->upload();

            $this->syncMedia($media, 'icon');
        }
    }
}
