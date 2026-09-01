<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value'];

    /**
     * Fetch one setting value, falling back to $default when the key is
     * missing (e.g. fresh database, or a test without seeders).
     *
     * NOTE: the static get() shadows Eloquent's magic-forwarded Builder::get
     * on this model, so anything else must query via Setting::query()->...
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }
}
