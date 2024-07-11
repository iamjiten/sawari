<?php

namespace App\Models;

use App\Enums\KycTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Crud;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Exceptions\ApiResponder;
use Plank\Mediable\Facades\MediaUploader;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DrivingLicense extends Model
{
    use HasFactory, SoftDeletes, Crud, ApiResponder, Mediable, LogsActivity;

    protected $table = 'driver_licenses';

    public const PERMISSION_SLUG = 'driver-licenses';

    protected $fillable = [
        'user_id',
        'license_number',
        'front_image',
        'back_image',
        'expired_at',
        'extra',
        'status',
        'remarks',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'extra' => 'array',
        'status' => KycTypeEnum::class
    ];

    public static $logName = "Driving License";

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
        return $query->where('license_number', 'LIKE', '%' . $searchquery . '%')
            ->orWhere('expired_at', 'LIKE', '%' . $searchquery . '%');
    }

    public function mergeRequest($id = null): array
    {
        return [
            'user_id' => auth()->id(),
            'status' => KycTypeEnum::Pending->value, // remove this in production
        ];
    }

    public function scopeStatus($query, $val)
    {
        if ($val) {
            $query = $query->where('status', $val);
        }
        return $query;
    }

    public function scopeUser($query, $val)
    {
        if ($val) {
            $query = $query->whereIn('user_id', $val);
        }
        return $query;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function afterCreateProcess(): void
    {
        if (request()->file('front_image')) {
            $media = MediaUploader::fromSource(request()->file('front_image'))
                ->toDestination(config('filesystems.multi') ? 'minio_write'
                    : config('filesystems.default'),
                    "postImage")
                ->makePublic()
                ->upload();

            $this->syncMedia($media, 'front_image');
        }
        if (request()->file('back_image')) {
            $media = MediaUploader::fromSource(request()->file('back_image'))
                ->toDestination(config('filesystems.multi') ? 'minio_write'
                    : config('filesystems.default'),
                    "postImage")
                ->makePublic()
                ->upload();

            $this->syncMedia($media, 'back_image');
        }
    }

    public function afterUpdateProcess(): void
    {
        if (request()->file('front_image')) {
            $media = MediaUploader::fromSource(request()->file('front_image'))
                ->toDestination(config('filesystems.multi') ? 'minio_write'
                    : config('filesystems.default'),
                    "postImage")
                ->makePublic()
                ->upload();

            $this->syncMedia($media, 'front_image');
        }
        if (request()->file('back_image')) {
            $media = MediaUploader::fromSource(request()->file('back_image'))
                ->toDestination(config('filesystems.multi') ? 'minio_write'
                    : config('filesystems.default'),
                    "postImage")
                ->makePublic()
                ->upload();

            $this->syncMedia($media, 'back_image');
        }
    }

}
