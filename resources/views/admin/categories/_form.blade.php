<div class="panel">
    <div class="form-grid">
        <div>
            <div class="field">
                <label>Назва *</label>
                <input name="name" value="{{ old('name', $category->name) }}" required>
            </div>

        </div>

        <div>
            <div class="field">
                <label>Іконка</label>
                <input name="icon" value="{{ old('icon', $category->icon) }}" placeholder="наприклад: 🍣">
            </div>

            <div class="field">
                <label>Сортування</label>
                <input type="number"
                       min="0"
                       name="sort_order"
                       value="{{ old('sort_order', $category->sort_order ?? 0) }}">
            </div>

            <div class="field checkbox-row">
                <input type="hidden" name="is_active" value="0">
                <input id="is_active"
                       type="checkbox"
                       name="is_active"
                       value="1"
                       @checked(old('is_active', $category->exists ? $category->is_active : true))>
                <label for="is_active" style="margin:0;">Показувати фільтр на сайті</label>
            </div>
        </div>
    </div>

    <div style="display:flex;gap:10px;margin-top:18px;">
        <button class="btn btn-primary" type="submit">Зберегти</button>
        <a class="btn btn-secondary" href="{{ route('admin.categories.index') }}">Скасувати</a>
    </div>
</div>
