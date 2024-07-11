<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\AddressBookRequest;
use App\Http\Resources\ActivityLogResource;
use App\Http\Resources\AddressBookResource;
use App\Models\ActivityLogs;
use App\Models\AddressBook;
use Illuminate\Http\Resources\Json\JsonResource;


class ActivityLogController extends SuperController
{
    public function __construct()
    {
        parent::__construct(
            ActivityLogs::class,
            ActivityLogResource::class,
            '',
            ''
        );
    }
}
