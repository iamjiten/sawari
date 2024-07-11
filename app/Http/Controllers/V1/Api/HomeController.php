<?php

namespace App\Http\Controllers\V1\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\URL;

class HomeController extends Controller
{
    public function getBanners()
    {
        return [
            [
                'id' => 1,
                'banner_img' => URL::to('/') . '/images/banners/packages/banner1.png',
            ],
            [
                'id' => 2,
                'banner_img' => URL::to('/') . '/images/banners/packages/banner2.png',
            ],
            [
                'id' => 3,
                'banner_img' => URL::to('/') . '/images/banners/packages/banner3.png',
            ],
        ];
    }
}
