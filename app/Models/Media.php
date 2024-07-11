<?php

namespace App\Models;

use App\Traits\CRUD;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Media as MediaAlias;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Media extends \Plank\Mediable\Media
{
    use HasFactory, Mediable, CRUD, LogsActivity;

    protected $table = 'media';

    protected $fillable = [
        'reference_name',
        'disk',
        'alt_text',
        'caption',
    ];

    public static $logName = "Media";

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

    public function scopeQueryFilter($query, $searchquery)
    {
        return $query->where(function ($qq) use ($searchquery) {
            $qq->where('filename', 'LIKE', '%' . $searchquery . '%');
        });
    }

    public function scopeMediatype($query, $val)
    {
        if ($val == 'video') {
            return $query->where('aggregate_type', Media::TYPE_VIDEO);
        }

        if ($val == 'image') {
            return $query->whereIn('aggregate_type',
                [MediaAlias::TYPE_IMAGE, MediaAlias::TYPE_IMAGE_VECTOR]);
        }

        if ($val == 'attachment') {
            return $query->whereIn('aggregate_type', [
                MediaAlias::TYPE_DOCUMENT, MediaAlias::TYPE_PDF, MediaAlias::TYPE_SPREADSHEET,
                MediaAlias::TYPE_PRESENTATION,
            ]);
        }
    }
}
