<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class VehicleOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'order_id'
    ];

    protected $table = 'vehicle_order';
}
