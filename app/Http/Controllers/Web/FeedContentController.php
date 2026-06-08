<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Services\EventService;
use App\Services\FeedService;
use App\Services\PollService;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FeedContentController extends Controller
{
    public function storePost(Request $request): RedirectResponse
    {
        PostService::create($request, auth()->id());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Публикация создана.']);

        return redirect()->route('feed');
    }

    public function storePoll(Request $request): RedirectResponse
    {
        PollService::create($request, auth()->id());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Опрос создан.']);

        return redirect()->route('feed');
    }

    public function storeEvent(Request $request): RedirectResponse
    {
        EventService::create($request, auth()->id());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Событие создано.']);

        return redirect()->route('feed');
    }

    public function suggestEvent(Request $request): RedirectResponse
    {
        EventService::suggest($request, auth()->id());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Предложение отправлено. Спасибо!']);

        return redirect()->route('feed');
    }

    public function storeSuggestion(Request $request): RedirectResponse
    {
        PostService::suggest($request, auth()->id());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Предложение отправлено. Спасибо!']);

        return redirect()->route('feed');
    }

    public function vote(Request $request, Poll $poll): JsonResponse
    {
        $request->validate([
            'option_ids' => ['required', 'array'],
            'option_ids.*' => ['integer'],
        ]);

        PollService::vote($poll, $request->input('option_ids'), $request->user());

        $poll->load(['author:id,name', 'options' => fn ($q) => $q->withCount('votes')]);
        $poll->loadCount('votes');

        return response()->json(FeedService::formatPollItem($poll, $request->user()));
    }

    public function unvote(Request $request, Poll $poll): JsonResponse
    {
        PollService::unvote($poll, $request->user());

        $poll->load(['author:id,name', 'options' => fn ($q) => $q->withCount('votes')]);
        $poll->loadCount('votes');

        return response()->json(FeedService::formatPollItem($poll, $request->user()));
    }
}
