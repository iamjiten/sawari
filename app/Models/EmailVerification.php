<?php

namespace App\Models;

use App\Models\Traits\CRUD;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailVerification extends Model
{
    protected $table = 'email_verifications';
    protected $fillable = [
        'user_id',
        'code',
        'slug',
        'expires_at',
        'url',
    ];
}
