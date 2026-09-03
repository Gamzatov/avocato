@php
    $cityPivots = $product->exists
        ? $product->cities->keyBy('id')
        : collect();
    $optionNames = [
        'ЛОСОСЬ',
        'ТУНЕЦЬ',
        'ВУГОР',
        'КРЕВЕТКА',
        'СНІЖНИЙ КРАБ',
        'КОПЧЕНИЙ ЛОСОСЬ',
        'СМАЖЕНИЙ ЛОСОСЬ',
    ];
    $optionRows = old('options', $product->options->map(fn ($option) => [
        'id' => $option->id,
        'name' => $option->name,
        'price' => $option->price,
        'weight' => $option->weight,
        'sort_order' => $option->sort_order,
        'is_active' => $option->is_active,
    ])->all());
    $emptyRowsCount = max(3, 7 - count($optionRows));
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

            <h3>Варіанти для селекту</h3>
            <p class="muted" style="margin-top:-4px;">
                Заповніть ці рядки, якщо товар має вибір начинки або типу.
            </p>

            @foreach($optionRows as $index => $option)
                <div class="city-box">
                    <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option['id'] ?? '' }}">

                    <div class="field">
                        <label>Назва варіанту</label>
                        <select name="options[{{ $index }}][name]">
                            <option value="">Оберіть варіант</option>
                            @foreach($optionNames as $optionName)
                                <option value="{{ $optionName }}" @selected(($option['name'] ?? '') === $optionName)>
                                    {{ $optionName }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-grid">
                        <div class="field">
                            <label>Ціна, грн</label>
                            <input type="number"
                                   step="0.01"
                                   min="0"
                                   name="options[{{ $index }}][price]"
                                   value="{{ $option['price'] ?? '' }}">
                        </div>

                        <div class="field">
                            <label>Вага</label>
                            <input name="options[{{ $index }}][weight]"
                                   value="{{ $option['weight'] ?? '' }}"
                                   placeholder="наприклад: 270г">
                        </div>
                    </div>

                    <input type="hidden" name="options[{{ $index }}][sort_order]" value="{{ $option['sort_order'] ?? $index }}">

                    <div class="checkbox-row">
                        <input type="hidden" name="options[{{ $index }}][is_active]" value="0">
                        <input id="option_{{ $index }}"
                               type="checkbox"
                               name="options[{{ $index }}][is_active]"
                               value="1"
                               @checked((bool) ($option['is_active'] ?? true))>
                        <label for="option_{{ $index }}" style="margin:0;">Показувати варіант</label>
                    </div>

                    @if(!empty($option['id']))
                        <div class="checkbox-row" style="margin-top:10px;">
                            <input type="hidden" name="options[{{ $index }}][delete]" value="0">
                            <input id="option_delete_{{ $index }}"
                                   type="checkbox"
                                   name="options[{{ $index }}][delete]"
                                   value="1">
                            <label for="option_delete_{{ $index }}" style="margin:0;">Видалити варіант</label>
                        </div>
                    @endif
                </div>
            @endforeach

            @for($i = 0; $i < $emptyRowsCount; $i++)
                @php($index = count($optionRows) + $i)
                <div class="city-box">
                    <div class="field">
                        <label>Назва варіанту</label>
                        <select name="options[{{ $index }}][name]">
                            <option value="">Оберіть варіант</option>
                            @foreach($optionNames as $optionName)
                                <option value="{{ $optionName }}">{{ $optionName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-grid">
                        <div class="field">
                            <label>Ціна, грн</label>
                            <input type="number" step="0.01" min="0" name="options[{{ $index }}][price]">
                        </div>

                        <div class="field">
                            <label>Вага</label>
                            <input name="options[{{ $index }}][weight]" placeholder="наприклад: 270г">
                        </div>
                    </div>

                    <input type="hidden" name="options[{{ $index }}][sort_order]" value="{{ $index }}">

                    <div class="checkbox-row">
                        <input type="hidden" name="options[{{ $index }}][is_active]" value="0">
                        <input id="option_{{ $index }}"
                               type="checkbox"
                               name="options[{{ $index }}][is_active]"
                               value="1"
                               checked>
                        <label for="option_{{ $index }}" style="margin:0;">Показувати варіант</label>
                    </div>
                </div>
            @endfor
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
                        <label>Ціна, грн</label>
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
