@extends('admin.layout')

@section('content')
<div class="toolbar">
    <div>
        <h1 style="margin:0 0 6px;">Замовлення #{{ $order->id }}</h1>
        <div class="muted">{{ $order->created_at?->format('d.m.Y H:i') }}</div>
    </div>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">← До списку</a>
</div>

<div class="summary-grid">
    <div class="summary-box">
        <span class="muted">Клієнт</span>
        <strong>{{ $order->customer_name }}</strong>
        <div class="muted">{{ $order->customer_phone }}</div>
    </div>

    <div class="summary-box">
        <span class="muted">Місто</span>
        <strong>{{ $order->city?->name }}</strong>
        @if($order->customer_address)
            <div class="muted">{{ $order->customer_address }}</div>
        @endif
    </div>

    <div class="summary-box">
        <span class="muted">Разом</span>
        <strong>{{ number_format((float) $order->total, 0, '.', ' ') }} ₴</strong>
        <div class="muted">{{ $order->items->sum('quantity') }} позицій</div>
    </div>
</div>

<div class="panel" style="margin-bottom:18px;">
    <form method="POST" action="{{ route('admin.orders.update', $order) }}">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="field" style="margin-bottom:0;">
                <label>Статус</label>
                <select name="status">
                    @foreach($statuses as $status)
                        <option value="{{ $status->value }}" @selected($order->status === $status)>
                            {{ $status->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display:flex;align-items:flex-end;">
                <button class="btn btn-primary" type="submit">Оновити статус</button>
            </div>
        </div>
    </form>
</div>

@if($order->customer_comment)
    <div class="panel" style="margin-bottom:18px;">
        <strong>Коментар клієнта</strong>
        <p class="muted" style="margin:10px 0 0;">{{ $order->customer_comment }}</p>
    </div>
@endif

<div class="panel">
    <table>
        <thead>
        <tr>
            <th>Товар</th>
            <th>Кількість</th>
            <th>Ціна</th>
            <th>Сума</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            <tr>
                <td>
                    <strong>{{ $item->product_name }}</strong>
                    <div class="muted">{{ $item->product_slug }}</div>
                </td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format((float) $item->unit_price, 0, '.', ' ') }} ₴</td>
                <td><strong>{{ number_format((float) $item->line_total, 0, '.', ' ') }} ₴</strong></td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
