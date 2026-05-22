<?php

namespace App\Enums;

enum TableStatus: string
{
    case READY = 'ready';
    case NOT_READY = 'not_ready';

    public function label(): string
    {
        return match ($this) {
            self::READY => 'Готов',
            self::NOT_READY => 'Не готов',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::READY => 'success',
            self::NOT_READY => 'warning',
        };
    }
}
