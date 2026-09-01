<?php

namespace App;

enum OrderStatus: string
{
    case New = 'new';
    case Confirmed = 'confirmed';
    case Cooking = 'cooking';
    case Ready = 'ready';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::New => 'Нове',
            self::Confirmed => 'Підтверджено',
            self::Cooking => 'Готується',
            self::Ready => 'Готове',
            self::Completed => 'Завершено',
            self::Cancelled => 'Скасовано',
        };
    }
}
