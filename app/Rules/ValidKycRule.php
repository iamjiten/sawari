<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class ValidKycRule implements ValidationRule
{

    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    /**
     * Run the validation rule.
     *
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $data = DB::table($this->table)->find($value);
        if ($data) {
            if ($data->status) {
                $fail('cannot change status of this :attribute');
            };
        } else {
            $fail('invalid :attribute');
        }
    }
}
