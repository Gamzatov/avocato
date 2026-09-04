@extends('admin.layout')

@section('content')
<div class="toolbar">
    <div>
        <h1 style="margin:0 0 6px;">Налаштування меню</h1>
        <div class="muted">Картинки та службові елементи меню на сайті.</div>
    </div>
</div>

<form method="POST" action="{{ route('admin.menu-settings.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="panel">
        <div class="form-grid">
            <div>
                <div class="field">
                    <label>Картинка категорії “Все”</label>
                    <input type="file" name="all_category_image" accept="image/*">
                    <div class="muted" style="margin-top:8px;">
                        Ця картинка показується у першій плитці фільтрів на сайті.
                    </div>
                </div>

                <div class="field">
                    <label>Варіанти начинок для селекту</label>
                    @php
                        $optionRows = old('product_option_names', $productOptionNames);
                    @endphp

                    <div class="settings-option-list" data-settings-option-list>
                        @foreach($optionRows as $index => $optionName)
                            <div class="settings-option-row" data-settings-option-row>
                                <input name="product_option_names[]"
                                       value="{{ $optionName }}"
                                       placeholder="Наприклад: ЛОСОСЬ"
                                       @required($index === 0)>
                                <button class="btn btn-danger" type="button" data-remove-settings-option>
                                    Видалити
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <button class="btn btn-secondary" type="button" data-add-settings-option style="margin-top:10px;">
                        + Додати опцію
                    </button>

                    <div class="muted" style="margin-top:8px;">
                        Цей список показується у формі товару в селекті варіантів начинки.
                    </div>
                </div>
            </div>

            <div>
                <label>Поточна картинка</label>
                <img src="{{ $allCategoryImageUrl }}"
                     alt=""
                     style="width:100%;max-width:340px;aspect-ratio:1.3/1;object-fit:cover;border-radius:14px;border:1px solid var(--line);background:#000;">
                @if(! $allCategoryImage)
                    <div class="muted" style="margin-top:8px;">
                        Зараз використовується стандартна картинка.
                    </div>
                @endif
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:18px;">
            <button class="btn btn-primary" type="submit">Зберегти</button>
            <a class="btn btn-secondary" href="{{ route('admin.categories.index') }}">Скасувати</a>
        </div>
    </div>
</form>

<template data-settings-option-template>
    <div class="settings-option-row" data-settings-option-row>
        <input name="product_option_names[]" placeholder="Наприклад: ЛОСОСЬ">
        <button class="btn btn-danger" type="button" data-remove-settings-option>
            Видалити
        </button>
    </div>
</template>

<script>
    (() => {
        const list = document.querySelector('[data-settings-option-list]');
        const addButton = document.querySelector('[data-add-settings-option]');
        const template = document.querySelector('[data-settings-option-template]');

        if (!list || !addButton || !template) {
            return;
        }

        const rows = () => [...list.querySelectorAll('[data-settings-option-row]')];

        const syncRequiredState = () => {
            const visibleRows = rows();

            visibleRows.forEach((row, index) => {
                const input = row.querySelector('input');

                if (input) {
                    input.required = index === 0;
                }
            });
        };

        addButton.addEventListener('click', () => {
            const row = template.content.firstElementChild.cloneNode(true);

            list.append(row);
            syncRequiredState();
            row.querySelector('input')?.focus();
        });

        list.addEventListener('click', (event) => {
            const removeButton = event.target.closest('[data-remove-settings-option]');

            if (!removeButton) {
                return;
            }

            const row = removeButton.closest('[data-settings-option-row]');

            if (rows().length === 1) {
                row.querySelector('input').value = '';
                row.querySelector('input').focus();
                return;
            }

            row.remove();
            syncRequiredState();
        });

        syncRequiredState();
    })();
</script>
@endsection
