<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Hash;

class CheckTokenRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    private $email;
    public function __construct($email)
    {
        //
        $this->email = $email;
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
        //

        $check = DB::table('password_resets')
            ->where([
                'email' => $this->email
            ])
            ->first();
        if(!isset($check->email) || empty($this->email)) return false;
        $check = (array) $check;

        $check = Hash::check($value, $check[$attribute]);
        return $check;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El token ha expirado o no coincide con nuestros registros.';
    }
}
