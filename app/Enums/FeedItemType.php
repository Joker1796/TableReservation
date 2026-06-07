<?php

namespace App\Enums;

enum FeedItemType: string
{
    case Post = 'post';

    public function label(): string
    {
        return match ($this) {
            self::Post => 'Публикация',
        };
    }
}
