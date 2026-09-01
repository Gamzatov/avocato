<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class City extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'phone',
        'secondary_phone',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['price', 'is_active'])
            ->withTimestamps();
    }

    /**
     * @return list<string>
     */
    public function phones(): array
    {
        return array_values(array_filter([
            $this->phone,
            $this->secondary_phone,
        ]));
    }
}
