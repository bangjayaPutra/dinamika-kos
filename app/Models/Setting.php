<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /**
     * Setting keys managed through the admin form, with their defaults.
     *
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'site_name' => 'DinamiKA Kos',
            'tagline' => 'Kos nyaman untuk aktivitas dinamis Anda',
            'address' => '',
            'wa_number' => '',
            'maps_url' => '',
            'welcome_text' => '',
            'operating_hours' => '',
        ];
    }

    /**
     * Get the value for the given key, falling back to the default.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $setting = static::where('key', $key)->first(['value']);

        return $setting?->value ?? $default ?? static::defaults()[$key] ?? null;
    }

    /**
     * Get all managed settings as a key-value map.
     *
     * @return array<string, string|null>
     */
    public static function allAsMap(): array
    {
        $stored = static::whereIn('key', array_keys(static::defaults()))
            ->pluck('value', 'key')
            ->all();

        return array_merge(
            array_fill_keys(array_keys(static::defaults()), null),
            $stored
        );
    }
}
