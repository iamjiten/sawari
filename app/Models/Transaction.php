<?php

namespace App\Models;


use App\Enums\TransactionChannelEnum;
use App\Enums\TransactionStatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Crud;
use Spatie\Activitylog\Contracts\Activity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Transaction extends Model
{
    // use HasFactory;
    use SoftDeletes, Crud, LogsActivity;

    protected $table = 'transactions';

    protected $fillable = [
        'pid',
        'user_id',
        'amount',
        'status',
        'channel',
        'parent_id',
        'extra',
        'payment_response',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'extra' => 'array',
        'status' => TransactionStatusEnum::class,
        'channel' => TransactionChannelEnum::class,
    ];

    public static $logName = "Transaction";

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
        return $query->where('pid', 'LIKE', '%' . $searchquery . '%');
    }


    public function scopeUser($query, $val)
    {
        if ($val) {
            $query->whereIn('user_id', $val);
        }
        return $query;
    }

    public function scopeModel($query, $val)
    {
        if ($val == "packages") {
            $query->where('transactional_type', get_class(new Order()));
        } else if ($val == "movers") {
            $query->where('transactional_type', get_class(new MoverOrder()));
        } else if ($val == "rentals") {
            $query->where('transactional_type', get_class(new RentalOrder()));
        }
        return $query;
    }

    public function scopeChannel($query, $val)
    {
        if ($val || $val == 0) {
            $query->where('channel', $val);
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'parent_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function transactional(): MorphTo
    {
        return $this->morphTo();

    }

    public function updateTransaction($channel, $status, $data): bool
    {
        return $this->update([
            'channel' => $channel,
            'status' => $status,
            'payment_response' => $data
        ]);
    }
}
