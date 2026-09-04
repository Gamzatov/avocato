<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AppSetting extends Model
{
    public const AllCategoryImage = 'all_category_image';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function value(string $key, ?string $default = null): ?string
    {
        return self::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function setValue(string $key, ?string $value): self
    {
        return self::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }

    public static function allCategoryImageUrl(): string
    {
        $image = self::value(self::AllCategoryImage);

        return $image
            ? Storage::disk('public')->url($image)
            : asset('images/main-hero.jpg');
    }
}
