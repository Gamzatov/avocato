<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authenticated_admin_can_create_filter(): void
    {
        Storage::fake('public');

        $response = $this
            ->actingAs(User::factory()->create())
            ->post(route('admin.categories.store'), [
                'name' => 'Запечені роли',
                'image' => UploadedFile::fake()->image('baked-rolls.jpg'),
                'sort_order' => 11,
                'is_active' => '1',
            ]);

        $response
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success', 'Фільтр додано.');

        $this->assertDatabaseHas('categories', [
            'name' => 'Запечені роли',
            'slug' => 'zapeceni-roli',
            'sort_order' => 11,
            'is_active' => true,
        ]);

        $category = Category::query()->where('slug', 'zapeceni-roli')->firstOrFail();

        $this->assertNotNull($category->image);
        Storage::disk('public')->assertExists($category->image);
    }

    public function test_authenticated_admin_can_update_filter(): void
    {
        Storage::fake('public');

        $category = Category::create([
            'name' => 'Роли',
            'slug' => 'rolls',
            'image' => UploadedFile::fake()->image('rolls.jpg')->store('categories', 'public'),
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs(User::factory()->create())
            ->put(route('admin.categories.update', $category), [
                'name' => 'Авторські роли',
                'image' => UploadedFile::fake()->image('signature-rolls.jpg'),
                'sort_order' => 2,
                'is_active' => '0',
            ]);

        $response
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success', 'Фільтр оновлено.');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Авторські роли',
            'slug' => 'rolls',
            'sort_order' => 2,
            'is_active' => false,
        ]);

        $category->refresh();

        $this->assertNotNull($category->image);
        Storage::disk('public')->assertExists($category->image);
    }

    public function test_does_not_delete_filter_that_has_products(): void
    {
        $category = Category::create([
            'name' => 'Роли',
            'slug' => 'rolls',
            'icon' => '🍣',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Філадельфія',
            'slug' => 'philadelphia',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this
            ->actingAs(User::factory()->create())
            ->delete(route('admin.categories.destroy', $category));

        $response
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHasErrors();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }
}
