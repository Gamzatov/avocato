@extends('admin.layout')

@section('content')
<div class="toolbar">
    <div>
        <h1 style="margin:0 0 6px;">Замовлення</h1>
        <div class="muted">Переглядайте нові замовлення та керуйте статусами.</div>
    </div>
</div>

<div class="panel">
    <table>
        <thead>
        <tr>
            <th>№</th>
            <th>Клієнт</th>
            <th>Місто</th>
            <th>Статус</th>
            <th>Позицій</th>
            <th>Сума</th>
            <th>Дата</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($orders as $order)
            <tr>
                <td><strong>#{{ $order->id }}</strong></td>
                <td>
                    <strong>{{ $order->customer_name }}</strong>
                    <div class="muted">{{ $order->customer_phone }}</div>
                </td>
                <td>{{ $order->city?->name }}</td>
                <td>
                    <span class="status-pill">{{ $order->status->label() }}</span>
                </td>
                <td>{{ $order->items_count }}</td>
                <td><strong>{{ number_format((float) $order->total, 0, '.', ' ') }} ₴</strong></td>
                <td>
                    {{ $order->created_at?->format('d.m.Y H:i') }}
                </td>
                <td>
                    <div class="actions">
                        <a class="btn btn-secondary" href="{{ route('admin.orders.show', $order) }}">
                            Відкрити
                        </a>

                        <form method="POST"
                              action="{{ route('admin.orders.destroy', $order) }}"
                              onsubmit="return confirm('Видалити замовлення?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" type="submit">Видалити</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="muted">Замовлень поки немає.</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:18px;">
        {{ $orders->links() }}
    </div>
</div>
@endsection
