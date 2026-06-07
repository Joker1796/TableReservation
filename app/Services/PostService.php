<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\Request;

class PostService
{
    public static function create(Request $request, int $authorId): Post
    {
        $validated = self::validate($request);

        $post = new Post;
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->published_at = $validated['published_at'] ?? now();
        $post->author_id = $authorId;
        $post->save();

        return $post;
    }

    public static function update(Request $request, Post $post): void
    {
        $validated = self::validate($request);

        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->published_at = $validated['published_at'] ?? now();
        $post->save();
    }

    public static function softDelete(Post $post): void
    {
        $post->delete();
    }

    private static function validate(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
        ]);
    }
}
