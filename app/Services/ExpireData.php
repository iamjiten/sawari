<?php
namespace App\Services;

use Illuminate\Support\Carbon;

class ExpireData{
    private int $packageAddMinute = 2;
    private int $moverAddMinute = 2;

    public function getPackageExpiresAt(): Carbon
    {
        return now()->addMinutes($this->packageAddMinute);
    }

    public function getMoverExpiresAt(): Carbon
    {
        return now()->addMinutes($this->moverAddMinute);
    }
}