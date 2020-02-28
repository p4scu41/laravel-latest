<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserRegistration extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user  = new User();
        $rules = $user->validation_rules;

        $rules['password'] .= '|confirmed';

        return $rules;
    }
}
