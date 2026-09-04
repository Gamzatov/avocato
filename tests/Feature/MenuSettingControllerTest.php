<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MenuSettingControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_authenticated_admin_can_update_all_category_image(): void
    {
        Storage::fake('public');

        $response = $this
            ->actingAs(User::factory()->create())
            ->put(route('admin.menu-settings.update'), [
                'all_category_image' => UploadedFile::fake()->image('all.jpg'),
                'product_option_names' => AppSetting::DefaultProductOptionNames,
            ]);

        $response
            ->assertRedirect(route('admin.menu-settings.edit'))
            ->assertSessionHas('success', 'Налаштування меню оновлено.');

        $image = AppSetting::value(AppSetting::AllCategoryImage);

        $this->assertNotNull($image);
        Storage::disk('public')->assertExists($image);
    }

    public function test_authenticated_admin_can_update_product_option_names(): void
    {
        $response = $this
            ->actingAs(User::factory()->create())
            ->put(route('admin.menu-settings.update'), [
                'product_option_names' => ['ЛОСОСЬ', 'Манго', 'Манго', '', 'Курка'],
            ]);

        $response
            ->assertRedirect(route('admin.menu-settings.edit'))
            ->assertSessionHas('success', 'Налаштування меню оновлено.');

        $this->assertSame(['ЛОСОСЬ', 'Манго', 'Курка'], AppSetting::productOptionNames());
    }
}
