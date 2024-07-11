<?php

namespace App\Http\Controllers\V1\Api;

use App\Enums\RentalOrderStatusEnum;
use App\Http\Controllers\V1\SuperController;
use App\Http\Requests\MerchantRequest;
use App\Http\Resources\AnalysisRentalResource;
use App\Http\Resources\MerchantResource;
use App\Models\Merchant;

class MerchantController extends SuperController
{
    public function __construct()
    {
        parent::__construct(
            Merchant::class,
            MerchantResource::class,
            MerchantRequest::class,
            MerchantRequest::class
        );
    }

}
