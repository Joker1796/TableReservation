<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property int $author_id
 * @property int|null $table_id
 * @property string|null $comment
 * @property \Carbon\CarbonImmutable $date
 * @property \App\Enums\BookingRequestStatus $status
 * @property-read \App\Models\User $author
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invite> $invites
 * @property-read int|null $invites_count
 * @property-read \App\Models\Table|null $table
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\BookingRequestFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest whereTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BookingRequest withoutTrashed()
 */
	class BookingRequest extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string|null $short_description
 * @property string|null $description
 * @property \Carbon\CarbonImmutable|null $starts_at
 * @property \Carbon\CarbonImmutable|null $ends_at
 * @property int $author_id
 * @property bool $is_suggestion
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\User $author
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $participants
 * @property-read int|null $participants_count
 * @method static \Database\Factories\EventFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereEndsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereIsSuggestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereShortDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereStartsAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Event withoutTrashed()
 */
	class Event extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property \App\Enums\InviteStatus $status
 * @property int $author_id
 * @property int $target_id
 * @property int $reservation_id
 * @property-read \App\Models\User $author
 * @property-read \App\Models\Reservation|null $reservation
 * @property-read \App\Models\User $target
 * @method static \Database\Factories\InviteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite whereReservationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Invite withoutTrashed()
 */
	class Invite extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $question
 * @property string|null $description
 * @property bool $allow_multiple
 * @property int $author_id
 * @property \Carbon\CarbonImmutable|null $closes_at
 * @property \Carbon\CarbonImmutable|null $published_at
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\User $author
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PollOption> $options
 * @property-read int|null $options_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PollVote> $votes
 * @property-read int|null $votes_count
 * @method static \Database\Factories\PollFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereAllowMultiple($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereClosesAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereQuestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Poll withoutTrashed()
 */
	class Poll extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $poll_id
 * @property string $text
 * @property int $sort_order
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \App\Models\Poll|null $poll
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PollVote> $votes
 * @property-read int|null $votes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereSortOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollOption whereUpdatedAt($value)
 */
	class PollOption extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $poll_id
 * @property int $poll_option_id
 * @property int $user_id
 * @property \Carbon\CarbonImmutable $created_at
 * @property-read \App\Models\PollOption $option
 * @property-read \App\Models\Poll|null $poll
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote wherePollId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote wherePollOptionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PollVote whereUserId($value)
 */
	class PollVote extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $title
 * @property string $content
 * @property int $author_id
 * @property \Carbon\CarbonImmutable|null $published_at
 * @property bool $is_suggestion
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property-read \App\Models\User $author
 * @method static \Database\Factories\PostFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereAuthorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereIsSuggestion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Post withoutTrashed()
 */
	class Post extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property string|null $comment
 * @property string $date
 * @property int|null $table_id
 * @property-read \App\Models\Table|null $table
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Database\Factories\ReservationFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Reservation withoutTrashed()
 */
	class Reservation extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property \Carbon\CarbonImmutable|null $deleted_at
 * @property string $name
 * @property string|null $description
 * @property \App\Enums\TableStatus $status
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BookingRequest> $bookingRequests
 * @property-read int|null $booking_requests_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @method static \Database\Factories\TableFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Table withoutTrashed()
 */
	class Table extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $contacts
 * @property string|null $avatar
 * @property bool $is_admin
 * @property bool $is_editor
 * @property bool $is_api
 * @property bool $is_invisible
 * @property \Carbon\CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Carbon\CarbonImmutable|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BookingRequest> $bookingRequests
 * @property-read int|null $booking_requests_count
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Invite> $receivedInvites
 * @property-read int|null $received_invites_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Reservation> $reservations
 * @property-read int|null $reservations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User visible()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatar($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereContacts($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsAdmin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsApi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsEditor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereIsInvisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $filename
 * @property string $original_name
 * @property \Carbon\CarbonImmutable|null $created_at
 * @property \Carbon\CarbonImmutable|null $updated_at
 * @property-read mixed $url
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopPhoto newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopPhoto newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopPhoto query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopPhoto whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopPhoto whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopPhoto whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopPhoto whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkshopPhoto whereUpdatedAt($value)
 */
	class WorkshopPhoto extends \Eloquent {}
}

