<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\TransactionRequest;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;

class TransactionController extends SuperController
{
    protected array $with = [
        'user',
        'parent',
        'createdBy',
        'updatedBy',
    ];

    public function __construct()
    {
        parent::__construct(
            Transaction::class,
            TransactionResource::class,
            TransactionRequest::class,
            TransactionRequest::class
        );
    }
}
