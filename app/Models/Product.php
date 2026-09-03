<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public const BadgeNew = 'new';

    public const BadgeHit = 'hit';

    public const BadgeSale = 'sale';

    public const BadgeOutOfStock = 'out_of_stock';

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'weight',
        'image',
        'sort_order',
        'badge',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class)
            ->withPivot(['price', 'is_active'])
            ->withTimestamps();
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('sort_order')->orderBy('name');
    }

    /**
     * @return array<string, string>
     */
    public static function badgeOptions(): array
    {
        return [
            self::BadgeNew => 'Новинка',
            self::BadgeHit => 'Хіт',
            self::BadgeSale => 'Акція',
            self::BadgeOutOfStock => 'Немає в наявності',
        ];
    }

    public function badgeLabel(): ?string
    {
        return self::badgeOptions()[$this->badge] ?? null;
    }

    public function isOutOfStock(): bool
    {
        return $this->badge === self::BadgeOutOfStock;
    }
}
