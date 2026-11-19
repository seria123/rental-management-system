<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        $userId = $this->user()->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($userId),
            ],
            'username' => ['nullable', 'string', 'max:255', 'alpha_dash', Rule::unique('user_profiles')->ignore($userId, 'user_id')],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['nullable', 'string', 'in:male,female,other,prefer_not_to_say'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'physical_address' => ['nullable', 'string', 'max:1000'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'kra_pin' => ['nullable', 'string', 'max:50'],
            'agency_name' => ['nullable', 'string', 'max:255'],
            'assigned_property' => ['nullable', 'string', 'max:255'],
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'payment_details' => ['nullable', 'string'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.alpha_dash' => 'Username may only contain letters, numbers, dashes, and underscores.',
            'username.unique' => 'This username is already taken.',
            'profile_picture.image' => 'The profile picture must be a valid image file.',
            'profile_picture.max' => 'The profile picture must not exceed 2MB.',
        ];
    }
}
