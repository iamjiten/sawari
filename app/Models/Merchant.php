<?php

namespace App\Models;

use App\Traits\Crud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Merchant extends Model
{
    use SoftDeletes, Crud, LogsActivity, Mediable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'mobile_number',
        'email',
        'address',
        'pan_number',
        'website',
        'extras',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'extras' => 'array',

    ];

    public static $logName = "Merchant";

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
        return $query->where('name', 'LIKE', '%' . $searchquery . '%')
            ->orWhere('mobile_number', 'LIKE', '%' . $searchquery . '%')
            ->orWhere('email', 'LIKE', '%' . $searchquery . '%')
            ->orWhere('pan_number', 'LIKE', '%' . $searchquery . '%');
    }

    public function afterCreateProcess(): void
    {
        if (is_array(request()->input('profile'))) $this->syncMedia(request()->input('profile'), 'profile');
    }

    public function afterUpdateProcess(): void
    {
        if (is_array(request()->input('profile'))) $this->syncMedia(request()->input('profile'), 'profile');
    }

    public function user(): HasMany
    {
        return $this->hasMany(User::class);
    }

}
