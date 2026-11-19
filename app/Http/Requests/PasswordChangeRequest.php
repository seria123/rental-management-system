<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class PasswordChangeRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.current_password' => 'The current password is incorrect.',
            'password.confirmed' => 'The new password confirmation does not match.',
        ];
    }
}
