<?php

namespace App\Enums;

enum InviteStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case REVOKED = 'revoked';
    case EXPIRED = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'В ожидании',
            self::ACCEPTED => 'Принято',
            self::REVOKED => 'Отклонено',
            self::EXPIRED => 'Просрочено',
        };
    }
}
