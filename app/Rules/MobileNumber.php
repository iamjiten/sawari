<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MobileNumber implements Rule
{
    public bool $mobile_number_ntc = true;
    public bool $mobile_number_ncell = true;
    public bool $mobile_number_smart1 = true;
    public bool $mobile_number_smart2 = true;

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $mobile_number_ntc = ((bool)preg_match('/9[78][456][0-9]{7}/', $value));
        $mobile_number_ncell = ((bool)preg_match('/98[012][0-9]{7}/', $value));
        $mobile_number_smart1 = ((bool)preg_match('/96[12][0-9]{7}/', $value));
        $mobile_number_smart2 = ((bool)preg_match('/988[0-9]{7}/', $value));
        return ($mobile_number_ntc || $mobile_number_ncell || $mobile_number_smart1 || $mobile_number_smart2);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return match (true) {
            !$this->mobile_number_ncell, !$this->mobile_number_smart1, !$this->mobile_number_smart2, !$this->mobile_number_ntc => 'mobile number is invalid.',
            default => 'The :attribute must be Valid.',
        };
    }
}
