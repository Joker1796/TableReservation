<?php

namespace App\Enums;

enum BookingRequestStatus: int
{
    case PENDING = 0;
    case APPROVED = 1;
    case REJECTED = 2;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Новая',
            self::APPROVED => 'Одобрена',
            self::REJECTED => 'Отклонена',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'destructive',
        };
    }
}
