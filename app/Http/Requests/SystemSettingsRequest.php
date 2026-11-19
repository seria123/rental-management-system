<?php

namespace App\Http\Requests;

use App\Models\SystemSetting;
use Illuminate\Foundation\Http\FormRequest;

class SystemSettingsRequest extends FormRequest
{
    public function rules(): array
    {
        $settingKey = $this->route('setting');

        $rules = [];

        // Dynamic validation based on the setting being updated
        if ($settingKey) {
            $rules['value'] = ['required'];
        }

        // Bulk update validation
        if ($this->input('settings')) {
            foreach ($this->input('settings') as $key => $value) {
                $rules["settings.{$key}"] = ['sometimes', 'required'];
            }
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $settingKey = $this->route('setting');

            if ($settingKey) {
                $setting = SystemSetting::where('key', $settingKey)->first();

                if ($setting && $setting->input_type === 'number') {
                    $value = $this->input('value');
                    if ($value !== null && ! is_numeric($value)) {
                        $validator->errors()->add('value', 'The value must be a number.');
                    }
                }

                if ($setting && $setting->input_type === 'checkbox') {
                    $value = $this->input('value');
                    if ($value !== null && ! in_array($value, ['0', '1', true, false], true)) {
                        $validator->errors()->add('value', 'The value must be a boolean.');
                    }
                }
            }
        });
    }
}
