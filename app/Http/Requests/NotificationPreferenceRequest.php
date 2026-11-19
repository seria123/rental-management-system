<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationPreferenceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email_notifications' => ['sometimes', 'boolean'],
            'sms_notifications' => ['sometimes', 'boolean'],
            'in_app_notifications' => ['sometimes', 'boolean'],
            'rent_payment_notifications' => ['sometimes', 'boolean'],
            'maintenance_notifications' => ['sometimes', 'boolean'],
            'system_announcements' => ['sometimes', 'boolean'],
        ];
    }
}
