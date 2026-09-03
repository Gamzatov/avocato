@extends('admin.layout')

@section('content')
<div class="toolbar">
    <div>
        <h1 style="margin:0 0 6px;">Продукти</h1>
        <div class="muted">Додавайте, редагуйте та видаляйте позиції меню.</div>
    </div>
    <div class="toolbar-actions">
        <form class="admin-search" method="GET" action="{{ route('admin.products.index') }}">
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Пошук товару"
                aria-label="Пошук товару"
            >
            <button class="btn btn-secondary" type="submit">Шукати</button>
            @if($search !== '')
                <a class="btn btn-secondary" href="{{ route('admin.products.index') }}">Скинути</a>
            @endif
        </form>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">+ Додати продукт</a>
    </div>
</div>

<div class="panel">
    <table>
        <thead>
        <tr>
            <th>Фото</th>
            <th>Назва</th>
            <th>Категорія</th>
            <th>Міста / ціна</th>
            <th>Статус</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($products as $product)
            <tr>
                <td>
                    @if($product->image)
                        <img class="thumb" src="{{ asset('storage/'.$product->image) }}" alt="">
                    @else
                        <div class="thumb"></div>
                    @endif
                </td>
                <td>
                    <strong>{{ $product->name }}</strong>
                    <div class="muted">{{ $product->weight }}</div>
                    @if($product->options->isNotEmpty())
                        <div class="muted">
                            Варіанти: {{ $product->options->pluck('name')->join(', ') }}
                        </div>
                    @endif
                </td>
                <td>{{ $product->category?->name }}</td>
                <td>
                    @foreach($product->cities as $city)
                        <div>
                            {{ $city->name }}:
                            <strong>{{ number_format($city->pivot->price, 0, '.', ' ') }} грн</strong>
                            @if(!$city->pivot->is_active)
                                <span class="muted">(вимк.)</span>
                            @endif
                        </div>
                    @endforeach
                </td>
                <td>
                    {{ $product->is_active ? 'Активний' : 'Прихований' }}
                </td>
                <td>
                    <div class="actions">
                        <a class="btn btn-secondary" href="{{ route('admin.products.edit', $product) }}">
                            Редагувати
                        </a>

                        <form method="POST"
                              action="{{ route('admin.products.destroy', $product) }}"
                              onsubmit="return confirm('Видалити продукт?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Видалити</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="muted">
                    {{ $search !== '' ? 'За цим пошуком товарів немає.' : 'Продуктів поки немає.' }}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:18px;">
        {{ $products->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection
