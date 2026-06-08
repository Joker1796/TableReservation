<?php

namespace App\Models;

use Database\Factories\PollFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Poll extends Model
{
    /** @use HasFactory<PollFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'allow_multiple' => 'boolean',
        'closes_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class)->orderBy('sort_order');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function isOpen(): bool
    {
        return $this->closes_at === null || $this->closes_at->isFuture();
    }

    public function hasVoted(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }

    public function userVoteIds(User $user): array
    {
        return $this->votes()
            ->where('user_id', $user->id)
            ->pluck('poll_option_id')
            ->all();
    }
}
