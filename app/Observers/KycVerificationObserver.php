<?php

namespace App\Observers;

use App\Enums\KycTypeEnum;
use App\Enums\UserTypeEnum;
use App\Notifications\KycVerificationNotification;

class KycVerificationObserver
{
    private function kycVerificationCheck($user): bool
    {
        if (
            $user?->drivingLicense?->status == KycTypeEnum::Approved &&
            $user?->citizenship?->status == KycTypeEnum::Approved &&
            $user?->vehicle?->status == KycTypeEnum::Approved
        ) {
            return true;
        }
        return false;
    }

    // remove created method in production
    public function created(mixed $kyc): void
    {
        $user = $kyc->user;
        if ($this->kycVerificationCheck($user)) {
            $user->update([
                'kyc_status' => KycTypeEnum::Approved->value,
                'type' => UserTypeEnum::Rider->value
            ]);
            $kyc->user->notify(new KycVerificationNotification($kyc, true));
        }
    }

    public function updated(mixed $kyc): void
    {
        $user = $kyc->user;
        if ($this->kycVerificationCheck($user)) {
            $user->update([
                'kyc_status' => KycTypeEnum::Approved->value,
                'type' => UserTypeEnum::Rider->value
            ]);
        }
    }

}
