<?php

namespace App\Services;

use App\Enums\FeedItemType;
use App\Models\Post;

class FeedService
{
    public static function paginate(?string $cursor): array
    {
        $paginator = Post::with('author:id,name')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->cursorPaginate(15, ['*'], 'cursor', $cursor);

        $result = $paginator->toArray();
        $result['data'] = array_map(
            fn (array $item) => array_merge($item, ['type' => FeedItemType::Post->value]),
            $result['data'],
        );

        return $result;
    }
}
