<?php

namespace App\Models;

use App\Traits\Crud;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Models\Activity;

class ActivityLogs extends Activity
{
    use Crud;

    public const PERMISSION_SLUG = 'activities';

    protected $casts = [
        'properties' => 'array',
    ];

    public function scopeQueryfilter($query, $searchquery)
    {
        return $query->where('description', 'LIKE', '%' . $searchquery . '%')
            ->orWhere('log_name', 'LIKE', '%' . $searchquery . '%');
    }

    public function scopeUser($query, $val)
    {
        if ($val) {
            $query = $query->whereIn('causer_id', $val);
        }
        return $query;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'causer_id')->withTrashed();
    }
}
