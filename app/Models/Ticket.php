<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $scope_id
 * @property string $title
 * @property string $description
 * @property string $priority
 * @property string $status
 * @property string|null $deadline
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Screenshot> $screenshots
 * @property-read int|null $screenshots_count
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereDeadline($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereScopeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Ticket whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Ticket extends Model
{
    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'deadline',
        'user_id',
    ];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (Ticket $ticket) {
            if ($ticket->user_id === null) {
                /** @var int $id */
                $id = auth()->id();
                $ticket->user_id = $id;
            }
            $ticket->scope_id = self::genScopeId($ticket->user_id);
        });
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Screenshot, $this>
     */
    public function screenshots(): HasMany
    {
        return $this->hasMany(Screenshot::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_user_ticket', 'ticket_id', 'user_id_sender')
            ->withPivot('chat_message_id', 'user_id_receiver')
            ->withTimestamps();
    }

    /**
     * @throws Exception
     */
    private static function genScopeId(int|string|null $user_id)
    {
        if ($user_id === null) {
            throw new Exception('User id is null');
        }

        $lastScopeId = Ticket::where('user_id', $user_id)->max('scope_id');

        if ($lastScopeId === null) {
            return 1;
        }

        return $lastScopeId + 1;
    }
}
