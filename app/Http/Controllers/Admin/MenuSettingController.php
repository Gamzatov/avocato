<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.menu-settings.edit', [
            'allCategoryImage' => AppSetting::value(AppSetting::AllCategoryImage),
            'allCategoryImageUrl' => AppSetting::allCategoryImageUrl(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'all_category_image' => ['nullable', 'image', 'max:4096'],
        ]);

        if ($request->hasFile('all_category_image')) {
            $previousImage = AppSetting::value(AppSetting::AllCategoryImage);

            if ($previousImage) {
                Storage::disk('public')->delete($previousImage);
            }

            AppSetting::setValue(
                AppSetting::AllCategoryImage,
                $validated['all_category_image']->store('settings', 'public'),
            );
        }

        return redirect()
            ->route('admin.menu-settings.edit')
            ->with('success', 'Налаштування меню оновлено.');
    }
}
