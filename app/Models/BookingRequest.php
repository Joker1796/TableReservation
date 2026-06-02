<?php

namespace App\Models;

use App\Enums\BookingRequestStatus;
use Database\Factories\BookingRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingRequest extends Model
{
    /** @use HasFactory<BookingRequestFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'date' => 'datetime',
        'status' => BookingRequestStatus::class,
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'booking_request_user');
    }

    public function invites(): BelongsToMany
    {
        return $this->belongsToMany(Invite::class, 'booking_request_invite');
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
