<?php

namespace App\Services;

use App\Mail\NewPostSuggestionMail;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

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

    public static function suggest(Request $request, int $authorId): Post
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $post = new Post;
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->is_suggestion = true;
        $post->author_id = $authorId;
        $post->save();

        $admins = User::where('is_admin', true)->get();

        foreach ($admins as $admin) {
            Mail::to($admin)->queue(new NewPostSuggestionMail($post));
        }

        return $post;
    }

    public static function approve(Post $post): void
    {
        $post->published_at = now();
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
