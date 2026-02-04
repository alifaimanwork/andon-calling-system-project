<?php

namespace App\Extras\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidHost implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        return $this->isValidFDQN($value)
            || $this->isValidIP($value);
    }

    private function isValidFDQN($value)
    {
        return preg_match('/^(?!:\/\/)(?=.{1,255}$)((.{1,63}\.){1,127}(?![0-9]*$)[a-z0-9-]+\.?)$/i', $value);
    }

    private function isValidIP($value)
    {
        // check for valid ip4 or ip6
        return filter_var($value, FILTER_VALIDATE_IP) !== false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid hostname or IP address';
    }
}
