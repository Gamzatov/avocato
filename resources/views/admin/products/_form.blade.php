@php
    $cityPivots = $product->exists
        ? $product->cities->keyBy('id')
        : collect();
@endphp

<div class="panel">
    <div class="form-grid">
        <div>
            <div class="field">
                <label>Назва *</label>
                <input name="name" value="{{ old('name', $product->name) }}" required>
            </div>

            <div class="field">
                <label>Категорія *</label>
                <select name="category_id" required>
                    <option value="">Оберіть категорію</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            @selected((string) old('category_id', $product->category_id) === (string) $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Склад / опис</label>
                <textarea name="description">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="field">
                <label>Вага</label>
                <input name="weight" value="{{ old('weight', $product->weight) }}" placeholder="наприклад: 290 г">
            </div>
        </div>

        <div>
            <div class="field">
                <label>Фото</label>
                <input type="file" name="image" accept="image/*">

                @if($product->image)
                    <img src="{{ asset('storage/'.$product->image) }}"
                         alt=""
                         style="width:180px;margin-top:12px;border-radius:12px;">
                @endif
            </div>

            <div class="field">
                <label>Сортування</label>
                <input type="number" min="0" name="sort_order"
                       value="{{ old('sort_order', $product->sort_order ?? 0) }}">
            </div>

            <div class="field checkbox-row">
                <input type="hidden" name="is_active" value="0">
                <input id="is_active" type="checkbox" name="is_active" value="1"
                       @checked(old('is_active', $product->exists ? $product->is_active : true))>
                <label for="is_active" style="margin:0;">Показувати продукт</label>
            </div>

            <h3>Міста та ціни</h3>

            @foreach($cities as $city)
                @php
                    $pivot = $cityPivots->get($city->id)?->pivot;
                @endphp

                <div class="city-box">
                    <strong>{{ $city->name }}</strong>

                    <div class="field" style="margin-top:12px;">
                        <label>Ціна, ₴</label>
                        <input type="number"
                               step="0.01"
                               min="0"
                               name="cities[{{ $city->id }}][price]"
                               value="{{ old("cities.{$city->id}.price", $pivot?->price) }}">
                    </div>

                    <div class="checkbox-row">
                        <input type="hidden"
                               name="cities[{{ $city->id }}][is_active]"
                               value="0">
                        <input id="city_{{ $city->id }}"
                               type="checkbox"
                               name="cities[{{ $city->id }}][is_active]"
                               value="1"
                               @checked(old("cities.{$city->id}.is_active", $pivot?->is_active ?? true))>
                        <label for="city_{{ $city->id }}" style="margin:0;">
                            Є в меню цього міста
                        </label>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div style="display:flex;gap:10px;margin-top:18px;">
        <button class="btn btn-primary" type="submit">Зберегти</button>
        <a class="btn btn-secondary" href="{{ route('admin.products.index') }}">Скасувати</a>
    </div>
</div>
