@php
    use App\Models\Product;

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

            <div class="option-builder" data-option-builder data-next-index="{{ count($optionRows) }}">
                <div class="option-builder__head">
                    <div>
                        <h3>Варіанти для селекту</h3>
                        <p class="muted">
                            Додавайте начинки по одній: варіант, ціна та вага.
                        </p>
                    </div>
                    <button class="btn btn-secondary" type="button" data-add-option>
                        + Додати вибір начинки
                    </button>
                </div>

                <div class="option-builder__list" data-option-list>
                    <?php foreach ($optionRows as $index => $option): ?>
                        <div class="option-card is-collapsed" data-option-card>
                            <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option['id'] ?? '' }}">
                            <input type="hidden" name="options[{{ $index }}][sort_order]" value="{{ $option['sort_order'] ?? $index }}" data-option-sort>
                            <input type="hidden" name="options[{{ $index }}][delete]" value="0" data-option-delete>

                            <div class="option-card__summary">
                                <div>
                                    <strong data-option-summary-name>{{ $option['name'] ?? 'Новий варіант' }}</strong>
                                    <span data-option-summary-meta>
                                        {{ trim(($option['price'] ?? '') . ' грн' . (!empty($option['weight']) ? ' · ' . $option['weight'] : '')) }}
                                    </span>
                                </div>
                                <div class="option-card__actions">
                                    <button class="btn btn-secondary" type="button" data-edit-option>Редагувати</button>
                                    <button class="btn btn-danger" type="button" data-remove-option>Видалити</button>
                                </div>
                            </div>

                            <div class="option-card__form">
                                <div class="field">
                                    <label>Назва варіанту</label>
                                    <select name="options[{{ $index }}][name]" data-option-name>
                                        <option value="">Оберіть варіант</option>
                                        <?php foreach ($optionNames as $optionName): ?>
                                            <option value="{{ $optionName }}" @selected(($option['name'] ?? '') === $optionName)>
                                                {{ $optionName }}
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-grid">
                                    <div class="field">
                                        <label>Ціна, грн</label>
                                        <input type="number"
                                               step="0.01"
                                               min="0"
                                               name="options[{{ $index }}][price]"
                                               value="{{ $option['price'] ?? '' }}"
                                               data-option-price>
                                    </div>

                                    <div class="field">
                                        <label>Вага</label>
                                        <input name="options[{{ $index }}][weight]"
                                               value="{{ $option['weight'] ?? '' }}"
                                               placeholder="наприклад: 270г"
                                               data-option-weight>
                                    </div>
                                </div>

                                <div class="option-card__footer">
                                    <div class="checkbox-row">
                                        <input type="hidden" name="options[{{ $index }}][is_active]" value="0">
                                        <input id="option_{{ $index }}"
                                               type="checkbox"
                                               name="options[{{ $index }}][is_active]"
                                               value="1"
                                               data-option-active
                                               @checked((bool) ($option['is_active'] ?? true))>
                                        <label for="option_{{ $index }}" style="margin:0;">Показувати варіант</label>
                                    </div>

                                    <button class="btn btn-primary" type="button" data-save-option>Готово</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
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

            <div class="field">
                <label>Позначка</label>
                <select name="badge">
                    <option value="">Без позначки</option>
                    @foreach(Product::badgeOptions() as $badgeValue => $badgeLabel)
                        <option value="{{ $badgeValue }}" @selected(old('badge', $product->badge) === $badgeValue)>
                            {{ $badgeLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="field checkbox-row">
                <input type="hidden" name="is_active" value="0">
                <input id="is_active" type="checkbox" name="is_active" value="1"
                       @checked(old('is_active', $product->exists ? $product->is_active : true))>
                <label for="is_active" style="margin:0;">Показувати продукт</label>
            </div>

            <h3>Міста та ціни</h3>

            <?php foreach ($cities as $city): ?>
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
            <?php endforeach; ?>
        </div>
    </div>

    <div style="display:flex;gap:10px;margin-top:18px;">
        <button class="btn btn-primary" type="submit">Зберегти</button>
        <a class="btn btn-secondary" href="{{ route('admin.products.index') }}">Скасувати</a>
    </div>
</div>

<template data-option-template>
    <div class="option-card" data-option-card>
        <input type="hidden" data-option-field="id" value="">
        <input type="hidden" data-option-field="sort_order" data-option-sort>
        <input type="hidden" data-option-field="delete" value="0" data-option-delete>

        <div class="option-card__summary">
            <div>
                <strong data-option-summary-name>Новий варіант</strong>
                <span data-option-summary-meta>Заповніть дані</span>
            </div>
            <div class="option-card__actions">
                <button class="btn btn-secondary" type="button" data-edit-option>Редагувати</button>
                <button class="btn btn-danger" type="button" data-remove-option>Видалити</button>
            </div>
        </div>

        <div class="option-card__form">
            <div class="field">
                <label>Назва варіанту</label>
                <select data-option-field="name" data-option-name>
                    <option value="">Оберіть варіант</option>
                    <?php foreach ($optionNames as $optionName): ?>
                        <option value="{{ $optionName }}">{{ $optionName }}</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label>Ціна, грн</label>
                    <input type="number" step="0.01" min="0" data-option-field="price" data-option-price>
                </div>

                <div class="field">
                    <label>Вага</label>
                    <input data-option-field="weight" placeholder="наприклад: 270г" data-option-weight>
                </div>
            </div>

            <div class="option-card__footer">
                <div class="checkbox-row">
                    <input type="hidden" data-option-field="is_active_hidden" value="0">
                    <input type="checkbox" value="1" checked data-option-field="is_active" data-option-active>
                    <label style="margin:0;">Показувати варіант</label>
                </div>

                <button class="btn btn-primary" type="button" data-save-option>Готово</button>
            </div>
        </div>
    </div>
</template>

<script>
    (() => {
        const builder = document.querySelector('[data-option-builder]');

        if (!builder) {
            return;
        }

        const list = builder.querySelector('[data-option-list]');
        const template = document.querySelector('[data-option-template]');
        const addButton = builder.querySelector('[data-add-option]');

        const optionCards = () => [...list.querySelectorAll('[data-option-card]')];
        const optionInput = (card, name) => card.querySelector(`[data-option-${name}]`);

        const updateSummary = (card) => {
            const name = optionInput(card, 'name')?.value || 'Новий варіант';
            const price = optionInput(card, 'price')?.value;
            const weight = optionInput(card, 'weight')?.value;
            const meta = [];

            if (price) {
                meta.push(`${price} грн`);
            }

            if (weight) {
                meta.push(weight);
            }

            card.querySelector('[data-option-summary-name]').textContent = name;
            card.querySelector('[data-option-summary-meta]').textContent = meta.join(' · ') || 'Заповніть дані';
        };

        const collapseCard = (card) => {
            updateSummary(card);
            card.classList.add('is-collapsed');
        };

        const expandCard = (card) => {
            card.classList.remove('is-collapsed');
        };

        const prepareNewCard = (card, index) => {
            card.querySelectorAll('[data-option-field]').forEach((input) => {
                const field = input.dataset.optionField;

                if (field === 'is_active_hidden') {
                    input.name = `options[${index}][is_active]`;
                    return;
                }

                input.name = `options[${index}][${field}]`;
            });

            const checkbox = card.querySelector('[data-option-active]');
            const label = checkbox?.closest('.checkbox-row')?.querySelector('label');

            if (checkbox && label) {
                checkbox.id = `option_${index}`;
                label.setAttribute('for', checkbox.id);
            }

            card.querySelector('[data-option-sort]').value = index;
        };

        addButton.addEventListener('click', () => {
            const index = Number(builder.dataset.nextIndex || optionCards().length);
            const card = template.content.firstElementChild.cloneNode(true);

            prepareNewCard(card, index);
            builder.dataset.nextIndex = String(index + 1);
            list.append(card);
            expandCard(card);
            card.querySelector('[data-option-name]')?.focus();
        });

        list.addEventListener('click', (event) => {
            const card = event.target.closest('[data-option-card]');

            if (!card) {
                return;
            }

            if (event.target.closest('[data-edit-option]')) {
                expandCard(card);
            }

            if (event.target.closest('[data-save-option]')) {
                collapseCard(card);
            }

            if (event.target.closest('[data-remove-option]')) {
                const deleteInput = card.querySelector('[data-option-delete]');
                const hasId = Boolean(card.querySelector('input[name$="[id]"]')?.value);

                if (hasId && deleteInput) {
                    deleteInput.value = '1';
                    card.hidden = true;
                    return;
                }

                card.remove();
            }
        });

        list.addEventListener('input', (event) => {
            const card = event.target.closest('[data-option-card]');

            if (card) {
                updateSummary(card);
            }
        });

        list.addEventListener('change', (event) => {
            const card = event.target.closest('[data-option-card]');

            if (card) {
                updateSummary(card);
            }
        });

        optionCards().forEach(updateSummary);
    })();
</script>
