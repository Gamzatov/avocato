<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\OrderStatus;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OrderControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_valid_payload_creates_order_and_returns_created(): void
    {
        [$city, $product] = $this->createAvailableProduct();

        $response = $this->postJson('/api/orders', [
            'city_slug' => $city->slug,
            'customer' => [
                'name' => 'Олена',
                'phone' => '+380730054050',
                'address' => 'вул. Київська, 10',
                'comment' => 'Без імбиру',
            ],
            'items' => [
                ['product_id' => $product->id, 'qty' => 2],
            ],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('message', 'Замовлення створено.')
            ->assertJsonPath('order.status', 'new')
            ->assertJsonPath('order.total', '598.00')
            ->assertJsonPath('order.items_count', 2);

        $this->assertDatabaseHas('orders', [
            'city_id' => $city->id,
            'customer_name' => 'Олена',
            'customer_phone' => '+380730054050',
            'customer_address' => 'вул. Київська, 10',
            'customer_comment' => 'Без імбиру',
            'total' => '598.00',
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'product_name' => 'Філадельфія',
            'product_slug' => 'philadelphia',
            'unit_price' => '299.00',
            'quantity' => 2,
            'line_total' => '598.00',
        ]);
    }

    public function test_returns_validation_errors_when_product_is_unavailable_for_city(): void
    {
        [$city, $product] = $this->createAvailableProduct(isAvailable: false);

        $response = $this->postJson('/api/orders', [
            'city_slug' => $city->slug,
            'customer' => [
                'name' => 'Олена',
                'phone' => '+380730054050',
            ],
            'items' => [
                ['product_id' => $product->id, 'qty' => 1],
            ],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors('items');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);
    }

    public function test_authenticated_admin_can_update_order_status(): void
    {
        [$city, $product] = $this->createAvailableProduct();
        $order = Order::create([
            'city_id' => $city->id,
            'customer_name' => 'Олена',
            'customer_phone' => '+380730054050',
            'total' => 299,
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'product_slug' => $product->slug,
            'unit_price' => 299,
            'quantity' => 1,
            'line_total' => 299,
        ]);

        $response = $this
            ->actingAs(User::factory()->create())
            ->put(route('admin.orders.update', $order), [
                'status' => OrderStatus::Cooking->value,
            ]);

        $response
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHas('success', 'Статус замовлення оновлено.');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cooking',
        ]);
    }

    /**
     * @return array{City, Product}
     */
    private function createAvailableProduct(bool $isAvailable = true): array
    {
        $city = City::create([
            'name' => 'Переяслав',
            'slug' => 'pereiaslav',
            'phone' => '073 005 40 50',
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
            'is_active' => true,
        ]);

        $product->cities()->attach($city, [
            'price' => 299,
            'is_active' => $isAvailable,
        ]);

        return [$city, $product];
    }
}
