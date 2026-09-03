<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authenticated_admin_can_create_product_without_slug_field(): void
    {
        $category = Category::create([
            'name' => 'Роли',
            'slug' => 'rolls',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $city = City::create([
            'name' => 'Переяслав',
            'slug' => 'pereiaslav',
            'phone' => '+380000000001',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs(User::factory()->create())
            ->post(route('admin.products.store'), [
                'category_id' => $category->id,
                'name' => 'Філадельфія',
                'description' => 'Лосось, сир, рис',
                'weight' => '290 г',
                'sort_order' => 3,
                'is_active' => '1',
                'cities' => [
                    $city->id => [
                        'price' => 245,
                        'is_active' => '1',
                    ],
                ],
            ]);

        $response
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('success', 'Продукт додано.');

        $this->assertDatabaseHas('products', [
            'category_id' => $category->id,
            'name' => 'Філадельфія',
            'slug' => 'filadelfiia',
            'sort_order' => 3,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('city_product', [
            'city_id' => $city->id,
            'price' => 245,
            'is_active' => true,
        ]);
    }

    public function test_authenticated_admin_can_create_product_options(): void
    {
        $category = Category::create([
            'name' => 'Роли',
            'slug' => 'rolls',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $city = City::create([
            'name' => 'Переяслав',
            'slug' => 'pereiaslav',
            'phone' => '+380000000001',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs(User::factory()->create())
            ->post(route('admin.products.store'), [
                'category_id' => $category->id,
                'name' => 'Каліфорнія в ікрі',
                'description' => 'Рис, японський майонез, авокадо, огірок, ікра тобіко.',
                'weight' => '270г',
                'sort_order' => 3,
                'is_active' => '1',
                'cities' => [
                    $city->id => [
                        'price' => 290,
                        'is_active' => '1',
                    ],
                ],
                'options' => [
                    [
                        'name' => 'ЛОСОСЬ',
                        'price' => 300,
                        'weight' => '270г',
                        'sort_order' => 1,
                        'is_active' => '1',
                    ],
                    [
                        'name' => 'ВУГОР',
                        'price' => 310,
                        'weight' => '270г',
                        'sort_order' => 2,
                        'is_active' => '1',
                    ],
                ],
            ]);

        $response->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('product_options', [
            'name' => 'ЛОСОСЬ',
            'price' => '300.00',
            'weight' => '270г',
        ]);
        $this->assertDatabaseHas('product_options', [
            'name' => 'ВУГОР',
            'price' => '310.00',
            'weight' => '270г',
        ]);
    }

    public function test_product_create_form_starts_with_option_builder_button(): void
    {
        Category::create([
            'name' => 'Роли',
            'slug' => 'rolls',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        City::create([
            'name' => 'Переяслав',
            'slug' => 'pereiaslav',
            'phone' => '+380000000001',
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs(User::factory()->create())
            ->get(route('admin.products.create'));

        $response
            ->assertOk()
            ->assertSee('data-option-builder', false)
            ->assertSee('+ Додати вибір начинки')
            ->assertDontSee('name="options[0][name]"', false);
    }

    public function test_authenticated_admin_update_keeps_existing_product_slug(): void
    {
        $category = Category::create([
            'name' => 'Роли',
            'slug' => 'rolls',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $city = City::create([
            'name' => 'Переяслав',
            'slug' => 'pereiaslav',
            'phone' => '+380000000001',
            'is_active' => true,
        ]);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Філадельфія',
            'slug' => 'philadelphia',
            'description' => 'Лосось',
            'weight' => '290 г',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        $product->cities()->sync([
            $city->id => [
                'price' => 245,
                'is_active' => true,
            ],
        ]);

        $response = $this
            ->actingAs(User::factory()->create())
            ->put(route('admin.products.update', $product), [
                'category_id' => $category->id,
                'name' => 'Філадельфія Люкс',
                'description' => 'Лосось, сир',
                'weight' => '310 г',
                'sort_order' => 4,
                'is_active' => '1',
                'cities' => [
                    $city->id => [
                        'price' => 265,
                        'is_active' => '1',
                    ],
                ],
            ]);

        $response
            ->assertRedirect(route('admin.products.index'))
            ->assertSessionHas('success', 'Продукт оновлено.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Філадельфія Люкс',
            'slug' => 'philadelphia',
            'sort_order' => 4,
        ]);
    }

    public function test_authenticated_admin_can_search_products(): void
    {
        $category = Category::create([
            'name' => 'Роли',
            'slug' => 'rolls',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Філадельфія',
            'slug' => 'philadelphia',
            'description' => 'Лосось',
            'weight' => '290 г',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Product::create([
            'category_id' => $category->id,
            'name' => 'Каліфорнія',
            'slug' => 'california',
            'description' => 'Краб',
            'weight' => '260 г',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs(User::factory()->create())
            ->get(route('admin.products.index', ['search' => 'Філадельфія']));

        $response
            ->assertOk()
            ->assertSee('Філадельфія')
            ->assertDontSee('Каліфорнія')
            ->assertSee('value="Філадельфія"', false);
    }
}
