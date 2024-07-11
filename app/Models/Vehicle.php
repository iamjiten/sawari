<?php

namespace App\Models;

use App\Enums\KycTypeEnum;
use App\Traits\Super;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Crud;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Mediable\Facades\MediaUploader;
use Plank\Mediable\Mediable;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Vehicle extends Model
{
    use HasFactory, Super, SoftDeletes, Crud, Mediable, LogsActivity;

    public const PERMISSION_SLUG = 'vehicles';

    protected $fillable = [
        'user_id',
        'vehicle_type_id',
        'brand_id',
        'model_id',
        'color_id',
        'number_plate',
        'production_year',
        'image',
        'blue_book_first_image',
        'extra',
        'status',
        'remarks',
        'is_available',
        'merchant_id',
        'created_by',
        'updated_by'
    ];
    protected $casts = [
        'extra' => 'array',
        'status' => KycTypeEnum::class
    ];

    public static $logName = "Vehicle";

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
        return $query->where('number_plate', 'LIKE', '%' . $searchquery . '%')
            ->orwhere('production_year', 'LIKE', '%' . $searchquery . '%')
            ->orwhere('production_year', 'LIKE', '%' . $searchquery . '%');
    }

    public function scopeUser($query, $val)
    {
        if ($val) {
            $query = $query->whereIn('user_id', $val);
        }
        return $query;
    }


    public function mergeRequest()
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

    public function scopeVehicle($query, $val)
    {
        if ($val) {
            $query = $query->where('vehicle_type_id', $val);
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

    public function vehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'brand_id');
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'model_id');
    }

    public function color(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'color_id');
    }

    public function vehicleInformation(): HasOne
    {
        return $this->hasOne(VehicleInformation::class);
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(RentalLocation::class, 'rental_location_vehicle', 'vehicle_id', 'rental_location_id');
    }

    public function booked(): HasMany
    {
        return $this->hasMany(BookedRentalVehicle::class, 'vehicle_id');
        // change above code to hasMany if one order can book multiple vehicle
    }

    public function scopeFilterLocations($query, ...$locations)
    {
        $query->where(function ($query) use ($locations) {
            foreach ($locations as $location) {
                $query->whereHas('locations', fn($q) => $q
                    ->where('area_id', $location)
                );
            }
        });
        return $query;
    }

    public function rentalOrders(): HasManyThrough
    {
        return $this->hasManyThrough(
            RentalOrder::class,
            VehicleOrder::class,
            'vehicle_id',
            'id',
            'id',
            'order_id'
        );
    }

    public function scopeIsBooked($query, bool $booked)
    {
        if ($booked) {
            $query->has('booked');
        } else {
            $query->has('booked', '<', 1);
        }
        return $query;
    }

    public function scopeIsBook($query, $booked)
    {
        if ($booked == 'true') {
            $query->has('booked');
        } else {
            $query->has('booked', '<', 1);
        }
        return $query;
    }

    public function scopeIsDriverOnline($query, bool $value)
    {
        return $query->whereHas('vehicleInformation', fn($q) => $q
            ->where('withDriver', $value)
        );
    }

    public function scopeIsDriverOnlineIn($query, array $array)
    {
        return $query->whereHas('vehicleInformation', fn($q) => $q
            ->whereIn('withDriver', $array)
        );
    }

    public function basicInfos(): BelongsToMany
    {
        return $this->belongsToMany(RentalFeature::class, 'vehicle_basic_info_feature', 'vehicle_id', 'basic_info_id');
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'vehicle_module', 'vehicle_id', 'module_id');
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(RentalFeature::class, 'vehicle_service_feature', 'vehicle_id', 'service_id');
    }

    public function scopeApplyFilter($query, array $values)
    {
        $query->where(function ($query) use ($values) {
            foreach ($values as $key => $value) {
                $query->orWhereHas($key, fn($q) => $q
                    ->whereIn('rental_features.id', $value)
                );
            }
        });
        return $query;
    }

    public function afterCreateProcess(): void
    {
        if (request()->file('blue_book_first_image')) {
            $media = MediaUploader::fromSource(request()->file('blue_book_first_image'))
                ->toDestination(config('filesystems.multi') ? 'minio_write'
                    : config('filesystems.default'),
                    "postImage")
                ->makePublic()
                ->upload();

            $this->syncMedia($media, 'blue_book_first_image');
        }
        if (request()->file('insurance_image')) {
            $media = MediaUploader::fromSource(request()->file('insurance_image'))
                ->toDestination(config('filesystems.multi') ? 'minio_write'
                    : config('filesystems.default'),
                    "postImage")
                ->makePublic()
                ->upload();

            $this->syncMedia($media, 'insurance_image');
        }
        if (request()->file('image')) {
            $media = MediaUploader::fromSource(request()->file('image'))
                ->toDestination(config('filesystems.multi') ? 'minio_write'
                    : config('filesystems.default'),
                    "postImage")
                ->makePublic()
                ->upload();

            $this->syncMedia($media, 'image');
        }
    }

    public function afterUpdateProcess(): void
    {
        if (request()->file('blue_book_first_image')) {
            $media = MediaUploader::fromSource(request()->file('blue_book_first_image'))
                ->toDestination(config('filesystems.multi') ? 'minio_write'
                    : config('filesystems.default'),
                    "postImage")
                ->makePublic()
                ->upload();

            $this->syncMedia($media, 'blue_book_first_image');
        }
        if (request()->file('insurance_image')) {
            $media = MediaUploader::fromSource(request()->file('insurance_image'))
                ->toDestination(config('filesystems.multi') ? 'minio_write'
                    : config('filesystems.default'),
                    "postImage")
                ->makePublic()
                ->upload();

            $this->syncMedia($media, 'insurance_image');
        }
        if (request()->file('image')) {
            $media = MediaUploader::fromSource(request()->file('image'))
                ->toDestination(config('filesystems.multi') ? 'minio_write'
                    : config('filesystems.default'),
                    "postImage")
                ->makePublic()
                ->upload();

            $this->syncMedia($media, 'image');
        }
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
