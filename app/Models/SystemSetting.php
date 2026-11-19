<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = [
        'key',
        'group',
        'value',
        'is_encrypted',
        'input_type',
        'options',
        'description',
    ];

    protected $casts = [
        'is_encrypted' => 'boolean',
        'options' => 'array',
    ];

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        $value = $setting->value;

        if ($setting->is_encrypted && $value) {
            $value = decrypt($value);
        }

        // Cast based on input type
        return match ($setting->input_type) {
            'checkbox', 'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'number' => is_numeric($value) ? (float) $value : $value,
            default => $value,
        };
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value, string $group = 'general', array $attributes = []): self
    {
        $setting = static::firstOrCreate(['key' => $key], array_merge([
            'group' => $group,
            'value' => is_array($value) ? json_encode($value) : $value,
        ], $attributes));

        if (! $setting->wasRecentlyCreated) {
            $setting->update([
                'value' => is_array($value) ? json_encode($value) : $value,
                'group' => $group,
            ]);
        }

        return $setting;
    }

    /**
     * Get all settings for a group.
     */
    public static function getGroup(string $group): array
    {
        return static::where('group', $group)->pluck('value', 'key')->toArray();
    }

    /**
     * Get encrypted value.
     */
    public function getDecryptedValue(): mixed
    {
        if ($this->is_encrypted && $this->value) {
            return decrypt($this->value);
        }

        return $this->value;
    }
}
