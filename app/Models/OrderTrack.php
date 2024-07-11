<?php

namespace App\Models;

use App\Enums\OrderTrackEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'action_type',
        'properties',
        'causer_id',
        'remark',
        'deleted_at'
    ];

    protected $casts = [
        'properties' => 'array',
        'action_type' => OrderTrackEnum::class,
    ];

    public function trackable(): MorphTo
    {
        return $this->morphTo();
    }
}
