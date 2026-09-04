<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AppSetting extends Model
{
    public const AllCategoryImage = 'all_category_image';

    public const ProductOptionNames = 'product_option_names';

    public const DefaultProductOptionNames = [
        'ЛОСОСЬ',
        'ТУНЕЦЬ',
        'ВУГОР',
        'КРЕВЕТКА',
        'СНІЖНИЙ КРАБ',
        'КОПЧЕНИЙ ЛОСОСЬ',
        'СМАЖЕНИЙ ЛОСОСЬ',
        'АВОКАДО',
        'ЧУКА',
        'КУРКА',
    ];

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

        return $image && Storage::disk('public')->exists($image)
            ? asset('storage/'.$image)
            : asset('images/main-hero.jpg');
    }

    /**
     * @return list<string>
     */
    public static function productOptionNames(): array
    {
        $value = self::value(self::ProductOptionNames);

        if (! $value) {
            return self::DefaultProductOptionNames;
        }

        $decodedValue = json_decode($value, true);

        if (! is_array($decodedValue)) {
            return self::DefaultProductOptionNames;
        }

        return collect($decodedValue)
            ->filter(fn ($optionName): bool => is_string($optionName) && trim($optionName) !== '')
            ->map(fn (string $optionName): string => trim($optionName))
            ->unique()
            ->values()
            ->all();
    }

    public static function setProductOptionNames(array $optionNames): self
    {
        $normalizedOptionNames = collect($optionNames)
            ->filter(fn ($optionName): bool => is_string($optionName) && trim($optionName) !== '')
            ->map(fn (string $optionName): string => trim($optionName))
            ->unique()
            ->values()
            ->all();

        return self::setValue(self::ProductOptionNames, json_encode($normalizedOptionNames));
    }
}
