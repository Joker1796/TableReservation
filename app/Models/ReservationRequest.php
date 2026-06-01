<?php

namespace App\Models;

use App\Enums\ReservationRequestStatus;
use Database\Factories\ReservationRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReservationRequest extends Model
{
    /** @use HasFactory<ReservationRequestFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'date' => 'datetime',
        'status' => ReservationRequestStatus::class,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function invites(): BelongsToMany
    {
        return $this->belongsToMany(Invite::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
