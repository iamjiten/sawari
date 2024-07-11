<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use PhpParser\Node\Scalar\String_;
use ReflectionEnum;

class Enum implements Rule
{

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(private String $enum,private readonly String $message="")
    {

    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        try {
            return (in_array($value,array_column($this->enum::cases(), 'value'))||in_array($value,array_column($this->enum::cases(), 'name')));
        }catch (\Exception $e){
            return false;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message?:'The selected :attribute is invalid.';
    }
}
