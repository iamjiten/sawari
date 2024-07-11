<?php

namespace App\Models;

use App\Enums\SettlementChannelEnum;
use App\Exceptions\ApiResponder;
use App\Traits\Crud;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Settlement extends Model
{
    use HasFactory, ApiResponder, Crud;

    protected $fillable = [
        "type",
        "channel",
        "trip_id",
        "user_id",
        "actual_amount",
        "settlement_amount",
        "settlement_percentage",
        "earned_amount",
        "total_earned_amount",
        "total_settlement_amount",
    ];

    protected $casts = [
        'channel' => SettlementChannelEnum::class
    ];

    public function scopeChannel($query, $val)
    {
        if ($val || $val == 0) {
            $query->where('channel', $val);
        }
        return $query;
    }

    public function scopeUser($query, $val)
    {
        if ($val) {
            $query->whereIn('user_id', $val);
        }
        return $query;
    }

    public function scopeUserType($query, $val)
    {
        if ($val == 3) {
            $query->whereHas('user', function ($q) {
                $q->where('type', 3)->whereNotNull('merchant_id');
            });
        } else if ($val == 2) {
            $query->whereHas('user', function ($q) {
                $q->where('type', 2);
            });
        }
        return $query;
    }

    public function wallet(): MorphOne
    {
        return $this->morphOne(Wallet::class, 'module');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
