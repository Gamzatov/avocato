@extends('admin.layout')

@section('content')
<div class="toolbar">
    <div>
        <h1 style="margin:0 0 6px;">Продукти</h1>
        <div class="muted">Додавайте, редагуйте та видаляйте позиції меню.</div>
    </div>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">+ Додати продукт</a>
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
                </td>
                <td>{{ $product->category?->name }}</td>
                <td>
                    @foreach($product->cities as $city)
                        <div>
                            {{ $city->name }}:
                            <strong>{{ number_format($city->pivot->price, 0, '.', ' ') }} ₴</strong>
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
                <td colspan="6" class="muted">Продуктів поки немає.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:18px;">
        {{ $products->links() }}
    </div>
</div>
@endsection
