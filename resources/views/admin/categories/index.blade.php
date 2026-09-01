@extends('admin.layout')

@section('content')
<div class="toolbar">
    <div>
        <h1 style="margin:0 0 6px;">Фільтри</h1>
        <div class="muted">Керуйте категоріями, які показуються на головній сторінці меню.</div>
    </div>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">+ Додати фільтр</a>
</div>

<div class="panel">
    <table>
        <thead>
        <tr>
            <th>Іконка</th>
            <th>Назва</th>
            <th>Сортування</th>
            <th>Продуктів</th>
            <th>Статус</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($categories as $category)
            <tr>
                <td style="font-size:28px;">{{ $category->icon ?: '—' }}</td>
                <td><strong>{{ $category->name }}</strong></td>
                <td>{{ $category->sort_order }}</td>
                <td>{{ $category->products_count }}</td>
                <td>{{ $category->is_active ? 'Активний' : 'Прихований' }}</td>
                <td>
                    <div class="actions">
                        <a class="btn btn-secondary" href="{{ route('admin.categories.edit', $category) }}">
                            Редагувати
                        </a>

                        <form method="POST"
                              action="{{ route('admin.categories.destroy', $category) }}"
                              onsubmit="return confirm('Видалити фільтр?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Видалити</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="muted">Фільтрів поки немає.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:18px;">
        {{ $categories->links() }}
    </div>
</div>
@endsection
