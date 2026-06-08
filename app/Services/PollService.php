<?php

namespace App\Services;

use App\Models\Poll;
use App\Models\PollVote;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PollService
{
    public static function create(Request $request, int $authorId): Poll
    {
        $validated = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'allow_multiple' => ['boolean'],
            'closes_at' => ['nullable', 'date', 'after:now'],
            'options' => ['required', 'array', 'min:2', 'max:20'],
            'options.*' => ['required', 'string', 'max:255'],
        ]);

        $poll = new Poll;
        $poll->question = $validated['question'];
        $poll->description = $validated['description'] ?? null;
        $poll->allow_multiple = $validated['allow_multiple'] ?? false;
        $poll->closes_at = $validated['closes_at'] ?? null;
        $poll->published_at = now();
        $poll->author_id = $authorId;
        $poll->save();

        foreach ($validated['options'] as $index => $text) {
            $poll->options()->create([
                'text' => $text,
                'sort_order' => $index,
            ]);
        }

        return $poll;
    }

    public static function vote(Poll $poll, array $optionIds, User $user): void
    {
        if (! $poll->isOpen()) {
            throw ValidationException::withMessages([
                'poll' => ['Опрос завершён.'],
            ]);
        }

        if ($poll->hasVoted($user)) {
            throw ValidationException::withMessages([
                'poll' => ['Вы уже проголосовали в этом опросе.'],
            ]);
        }

        $validOptionIds = $poll->options()->pluck('id')->all();
        $optionIds = array_values(array_intersect($optionIds, $validOptionIds));

        if (empty($optionIds)) {
            throw ValidationException::withMessages([
                'options' => ['Выберите хотя бы один вариант.'],
            ]);
        }

        if (! $poll->allow_multiple && count($optionIds) > 1) {
            throw ValidationException::withMessages([
                'options' => ['В этом опросе можно выбрать только один вариант.'],
            ]);
        }

        foreach ($optionIds as $optionId) {
            PollVote::create([
                'poll_id' => $poll->id,
                'poll_option_id' => $optionId,
                'user_id' => $user->id,
            ]);
        }
    }

    public static function unvote(Poll $poll, User $user): void
    {
        PollVote::where('poll_id', $poll->id)
            ->where('user_id', $user->id)
            ->delete();
    }
}
