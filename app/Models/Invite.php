<?php

namespace App\Models;

use App\Enums\InviteStatus;
use Database\Factories\InviteFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invite extends Model
{
    /** @use HasFactory<InviteFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'status' => InviteStatus::class,
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_id');
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class, 'reservation_id');
    }

    public function accept(): static
    {
        if ($this->reservation) {
            $this->reservation->users()->attach($this->target);
            $this->reservation->save();
        }

        $this->status = InviteStatus::ACCEPTED;
        $this->save();

        return $this;
    }

    public function revoke(): static
    {
        if ($this->reservation && $this->reservation->users->contains($this->target)) {
            $this->reservation->users()->detach($this->target);
            $this->reservation->save();
        }

        $this->status = InviteStatus::REVOKED;
        $this->save();

        return $this;
    }
}
