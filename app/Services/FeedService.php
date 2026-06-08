<?php

namespace App\Services;

use App\Enums\FeedItemType;
use App\Models\Poll;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FeedService
{
    private const PER_PAGE = 15;

    public static function paginate(?string $cursor, ?User $user = null): array
    {
        $limit = self::PER_PAGE;
        $cursorData = $cursor ? json_decode(base64_decode($cursor), true) : null;

        $postQuery = DB::table('posts')
            ->selectRaw("'post' as feed_type, id, published_at")
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereNull('deleted_at');

        $pollQuery = DB::table('polls')
            ->selectRaw("'poll' as feed_type, id, published_at")
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->whereNull('deleted_at');

        if ($cursorData) {
            $publishedAt = $cursorData['published_at'];
            $id = (int) $cursorData['id'];

            $applyCursor = function ($q) use ($publishedAt, $id): void {
                $q->where('published_at', '<', $publishedAt)
                    ->orWhere(function ($q2) use ($publishedAt, $id): void {
                        $q2->where('published_at', $publishedAt)->where('id', '<', $id);
                    });
            };

            $postQuery->where($applyCursor);
            $pollQuery->where($applyCursor);
        }

        $rows = $postQuery->unionAll($pollQuery)
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit + 1)
            ->get();

        $hasMore = $rows->count() > $limit;
        $rows = $rows->take($limit);

        $postIds = $rows->where('feed_type', 'post')->pluck('id')->all();
        $pollIds = $rows->where('feed_type', 'poll')->pluck('id')->all();

        $posts = collect();
        if ($postIds) {
            $posts = Post::with('author:id,name')
                ->whereIn('id', $postIds)
                ->get()
                ->keyBy('id');
        }

        $polls = collect();
        if ($pollIds) {
            $polls = Poll::with(['author:id,name', 'options' => fn ($q) => $q->withCount('votes')])
                ->withCount('votes')
                ->whereIn('id', $pollIds)
                ->get()
                ->keyBy('id');
        }

        $data = $rows->map(function ($row) use ($posts, $polls, $user) {
            if ($row->feed_type === 'post') {
                $post = $posts[$row->id];

                return array_merge($post->toArray(), ['type' => FeedItemType::Post->value]);
            }

            return self::formatPollItem($polls[$row->id], $user);
        })->values()->all();

        $nextCursor = null;
        if ($hasMore) {
            $lastRow = $rows->last();
            $nextCursor = base64_encode(json_encode([
                'published_at' => $lastRow->published_at,
                'id' => $lastRow->id,
            ]));
        }

        return [
            'data' => $data,
            'next_cursor' => $nextCursor,
            'next_page_url' => $nextCursor ? "/feed?cursor={$nextCursor}" : null,
            'per_page' => $limit,
        ];
    }

    public static function formatPollItem(Poll $poll, ?User $user = null): array
    {
        $userVoteIds = $user ? $poll->userVoteIds($user) : [];
        $hasVoted = ! empty($userVoteIds);

        return [
            'type' => FeedItemType::Poll->value,
            'id' => $poll->id,
            'question' => $poll->question,
            'description' => $poll->description,
            'allow_multiple' => $poll->allow_multiple,
            'author' => $poll->author ? ['id' => $poll->author->id, 'name' => $poll->author->name] : null,
            'published_at' => $poll->published_at,
            'closes_at' => $poll->closes_at,
            'is_open' => $poll->isOpen(),
            'has_voted' => $hasVoted,
            'user_vote_ids' => $hasVoted ? $userVoteIds : [],
            'total_votes' => $poll->votes_count ?? $poll->votes()->count(),
            'options' => $poll->options->map(fn ($o) => [
                'id' => $o->id,
                'text' => $o->text,
                'sort_order' => $o->sort_order,
                'votes_count' => $hasVoted ? ($o->votes_count ?? $o->votes()->count()) : 0,
            ])->all(),
        ];
    }
}
