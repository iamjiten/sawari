<?php

namespace App\Models;

use App\Traits\Crud;
use App\Traits\Super;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SavedReceiver extends Model
{
    use HasFactory, Super, Crud, SoftDeletes, LogsActivity;

    protected $table = 'sender_saved_receivers';

    protected $fillable = [
        'user_id',
        'name',
        'mobile',
        'nick_name',
        'address',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
    ];

    public static $logName = "Saved Receiver";

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

    public function mergeRequest()
    {
        return [
            'user_id' => auth()->id()
        ];
    }

    public function beforeUpdateProcess(): array
    {
        if ($this->user_id == auth()->id()) {
            return [
                'status' => 200,
                'message' => 'can edit'
            ];
        }
        return [
            'status' => 401,
            'message' => 'unauthorized'
        ];
    }

    public function scopeSelf($query)
    {
        return $query->whereUserId(auth()->id());
    }
}
