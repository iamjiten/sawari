<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Translation\PotentiallyTranslatedString;

class UserMerchantRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $request = request();
        if (!$request->has('id')) {
            $count = User::query()
                ->where('merchant_id', $request->merchant_id)
                ->count();

            if ($count) {
                $fail('Merchant already has user [ One Merchant Contain only One User ]');
            }
        }
    }
}
