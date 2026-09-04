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
@endsection
