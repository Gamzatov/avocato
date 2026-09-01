<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\City;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        City::updateOrCreate(
            ['slug' => 'pereiaslav'],
            [
                'name' => 'Переяслав',
                'phone' => '0735505450',
                'secondary_phone' => '0955505450',
                'is_active' => true,
            ]
        );

        City::updateOrCreate(
            ['slug' => 'berezan'],
            [
                'name' => 'Березань',
                'phone' => '0730054050',
                'secondary_phone' => '0950054050',
                'is_active' => true,
            ]
        );

        $categories = [
            ['name' => 'Роли', 'slug' => 'rolls', 'icon' => '🍣'],
            ['name' => 'Сети', 'slug' => 'sets', 'icon' => '🍱'],
            ['name' => 'Супи', 'slug' => 'soups', 'icon' => '🍜'],
            ['name' => 'Салати', 'slug' => 'salads', 'icon' => '🥗'],
            ['name' => 'Десерти', 'slug' => 'desserts', 'icon' => '🍰'],
            ['name' => 'Мідії', 'slug' => 'mussels', 'icon' => '🦪'],
            ['name' => 'Фаст-фуд', 'slug' => 'fast-food', 'icon' => '🍔'],
            ['name' => 'Вок', 'slug' => 'wok', 'icon' => '🥡'],
            ['name' => 'Напої', 'slug' => 'drinks', 'icon' => '🥤'],
            ['name' => 'Інше', 'slug' => 'other', 'icon' => '🥢'],
        ];

        foreach ($categories as $index => $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category + ['sort_order' => $index + 1, 'is_active' => true]
            );
        }
    }
}
