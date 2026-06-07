<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminPostController extends Controller
{
    public function index(): Response
    {
        $posts = Post::with('author:id,name')->latest()->paginate(15);

        return Inertia::render('admin/posts/Index', [
            'posts' => $posts,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/posts/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        PostService::create($request, auth()->id());

        return redirect()->route('admin.posts.index');
    }

    public function edit(int $id): Response
    {
        $post = Post::findOrFail($id);

        return Inertia::render('admin/posts/Edit', [
            'post' => $post,
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $post = Post::findOrFail($id);
        PostService::update($request, $post);

        return redirect()->route('admin.posts.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $post = Post::findOrFail($id);
        PostService::softDelete($post);

        return redirect()->route('admin.posts.index');
    }
}
