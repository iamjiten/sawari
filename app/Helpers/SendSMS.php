<?php

namespace App\Helpers;


use Illuminate\Support\Facades\Log;

class SendSMS
{
    public function sendOtp($mobile_number, $message): bool
    {
        $args = http_build_query(array(
            'token' => 'v2_snpUzXPoXtJxyLkHP1l9e4BWfQg.07JT',
            'from' => 'TheAlert',
            'to' => $mobile_number,
            'text' => $message
        ));

        $url = "https://api.sparrowsms.com/v2/sms?" . $args;

        // Make the call using API.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        // Execute the request and store the response
        $response = curl_exec($ch);

        // Get the HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close the cURL session
        curl_close($ch);

        Log::info('Request URL :::::::::::: ', [$url]);
        Log::info('SMS api check :::::::::::: ', [$response]);
        Log::info('Message :::::::::::: ', [$message]);

        // Handle the response
        if ($httpCode === 200) {
            $res = json_decode($response, true);
            return true;
        } else {
            return false;
        }
    }
}
