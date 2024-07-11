<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Translation\PotentiallyTranslatedString;

class BrandValueRule implements ValidationRule
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
        $count = DB::table('settings')
                ->where('key', $request->key)
                ->where('value', $request->value)
                ->where('parent_id', $request->parent_id)
                ->count();
        if($count){
            $fail('Brand already exists');
        }
    }
}
