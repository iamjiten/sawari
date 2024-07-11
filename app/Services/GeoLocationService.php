<?php

namespace App\Services;

use App\Enums\TripStatusEnum;
use App\Enums\UserTypeEnum;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GeoLocationService
{
    public function geocode($latitude, $longitude)
    {
        $country = 'np';
        $types = 'poi,district,neighborhood,locality,place,address';
        $limit = 1;
        $accessToken = "pk.eyJ1IjoibWFuaXNoYWRoaWthcmkzNDEiLCJhIjoiY2tqamt0MmY4MXBjYTJwbXMya2JxMGx6NyJ9.rlR2mSSjhQ2KXdyLX-aCCA";

        $url = "https://api.mapbox.com/geocoding/v5/mapbox.places/{$longitude},{$latitude}.json?country={$country}&types={$types}&limit={$limit}&access_token={$accessToken}";

        $response = Http::get($url);
        $response = $response->json();

        return count($response['features']) > 0 ? $response['features'][0]['place_name'] : null;
    }
}
