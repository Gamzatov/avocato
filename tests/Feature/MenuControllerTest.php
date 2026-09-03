<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class MenuControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_returns_menu_for_city_slug(): void
    {
        $city = City::create([
            'name' => 'Переяслав',
            'slug' => 'pereiaslav',
            'phone' => '0735505450',
            'secondary_phone' => '0955505450',
            'is_active' => true,
        ]);
        $category = Category::create([
            'name' => 'Роли',
            'slug' => 'rolls',
            'icon' => 'sushi',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Філадельфія',
            'slug' => 'philadelphia',
            'description' => 'Лосось, сир, рис',
            'weight' => '250 г',
            'sort_order' => 1,
            'badge' => 'new',
            'is_active' => true,
        ]);

        $product->cities()->attach($city, [
            'price' => 299,
            'is_active' => true,
        ]);
        ProductOption::create([
            'product_id' => $product->id,
            'name' => 'ЛОСОСЬ',
            'price' => 300,
            'weight' => '270г',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/menu/pereiaslav');

        $response
            ->assertOk()
            ->assertJsonPath('city.slug', 'pereiaslav')
            ->assertJsonPath('city.phone', '0735505450')
            ->assertJsonPath('city.phones.0', '0735505450')
            ->assertJsonPath('city.phones.1', '0955505450')
            ->assertJsonPath('categories.0.slug', 'rolls')
            ->assertJsonPath('categories.0.products.0.slug', 'philadelphia')
            ->assertJsonPath('categories.0.products.0.price', 299)
            ->assertJsonPath('categories.0.products.0.badge', 'new')
            ->assertJsonPath('categories.0.products.0.badge_label', 'Новинка')
            ->assertJsonPath('categories.0.products.0.is_available', true)
            ->assertJsonPath('categories.0.products.0.options.0.name', 'ЛОСОСЬ')
            ->assertJsonPath('categories.0.products.0.options.0.price', '300.00')
            ->assertJsonPath('categories.0.products.0.options.0.weight', '270г');
    }

    public function test_returns_404_when_city_slug_does_not_exist(): void
    {
        $response = $this->getJson('/api/menu/missing-city');

        $response->assertNotFound();
    }
}
