<?php

namespace App\Services;

use App\Enums\BookingRequestStatus;
use App\Models\BookingRequest;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class BookingRequestService
{
    private static function createOrUpdate(Request $request, BookingRequest $br, ?int $authorId = null): BookingRequest
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'comment' => ['nullable', 'string'],
            'status' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'table_id' => ['sometimes', 'nullable', 'exists:tables,id'],
            'author' => ['sometimes', 'nullable', 'exists:users,id'],
            'user_ids' => ['sometimes', 'nullable', 'array'],
            'user_ids.*' => ['exists:users,id'],
        ]);

        $br->date = $validated['date'];
        $br->comment = $validated['comment'] ?? null;
        $br->status = $validated['status'] ?? $br->status ?? 0;

        if (! $br->author_id) {
            if ($authorId !== null) {
                $br->author_id = $authorId;
            } elseif (! empty($validated['author'])) {
                $br->author()->associate(User::find($validated['author']));
            } else {
                $br->author()->associate($request->user());
            }
        }

        if (array_key_exists('table_id', $validated)) {
            $br->table_id = $validated['table_id'];
        }

        $br->save();

        if (array_key_exists('user_ids', $validated) || $authorId !== null) {
            $userIds = $validated['user_ids'] ?? [];
            if ($authorId !== null && ! in_array($authorId, $userIds)) {
                $userIds[] = $authorId;
            }
            $br->users()->sync($userIds);
        }

        return $br;
    }

    public static function create(Request $request): Response
    {
        $br = self::createOrUpdate($request, new BookingRequest);
        $br->load('author', 'table', 'users');

        return response($br, ResponseAlias::HTTP_OK);
    }

    public static function update(Request $request, BookingRequest $br): Response
    {
        $br = self::createOrUpdate($request, $br);
        $br->load('author', 'table', 'users');

        return response($br, ResponseAlias::HTTP_OK);
    }

    public static function show(BookingRequest $bookingRequest): Response
    {
        return response($bookingRequest, ResponseAlias::HTTP_OK);
    }

    public static function softDelete(BookingRequest $bookingRequest): Response
    {
        $bookingRequest->delete();

        return response($bookingRequest, ResponseAlias::HTTP_OK);
    }

    public static function attachUser(BookingRequest $bookingRequest, User $user): Response
    {
        $bookingRequest->users()->attach($user);
        $bookingRequest->load('users');

        return response($bookingRequest, ResponseAlias::HTTP_OK);
    }

    public static function detachUser(BookingRequest $bookingRequest, User $user): Response
    {
        $bookingRequest->users()->detach($user);
        $bookingRequest->load('users');

        return response($bookingRequest, ResponseAlias::HTTP_OK);
    }

    public static function associateTable(BookingRequest $bookingRequest, Table $table): Response
    {
        $bookingRequest->table()->associate($table);
        $bookingRequest->save();

        $bookingRequest->load('table');

        return response($bookingRequest, ResponseAlias::HTTP_OK);
    }

    public static function deleteTable(BookingRequest $bookingRequest): Response
    {
        $bookingRequest->table()->dissociate();
        $bookingRequest->save();

        $bookingRequest->load('table');

        return response($bookingRequest, ResponseAlias::HTTP_OK);
    }

    public static function dailyCountForUser(int $userId): int
    {
        return BookingRequest::where('author_id', $userId)
            ->whereDate('created_at', today())
            ->count();
    }

    public static function createFromWeb(Request $request, int $authorId): BookingRequest
    {
        $br = self::createOrUpdate($request, new BookingRequest, $authorId);
        $br->load('author');

        return $br;
    }

    public static function updateStatus(Request $request, BookingRequest $br, int $adminId): void
    {
        $validated = $request->validate([
            'status' => ['required', new Enum(BookingRequestStatus::class)],
        ]);

        $newStatus = BookingRequestStatus::from((int) $validated['status']);

        DB::transaction(function () use ($br, $newStatus, $adminId): void {
            $br->status = $newStatus;
            $br->save();

            if ($newStatus === BookingRequestStatus::APPROVED) {
                $reservation = ReservationService::createFromBookingRequest($br);

                if ($br->author_id) {
                    $reservation->users()->attach($br->author_id);
                }

                foreach ($br->users as $user) {
                    if ($user->id === $br->author_id) {
                        continue;
                    }

                    InviteService::createPending($adminId, $user->id, $reservation->id);
                }
            }
        });
    }

    public static function assignTable(Request $request, BookingRequest $br): void
    {
        $validated = $request->validate([
            'table_id' => ['nullable', 'exists:tables,id'],
        ]);

        $br->table_id = $validated['table_id'];
        $br->save();
    }
}
