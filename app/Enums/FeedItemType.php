<?php

namespace App\Enums;

enum FeedItemType: string
{
    case Post = 'post';
    case Poll = 'poll';

    public function label(): string
    {
        return match ($this) {
            self::Post => 'Публикация',
            self::Poll => 'Опрос',
        };
    }
}
