<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Storage;

/**
 * @property int $id
 * @property int $user_id
 * @property int $ticket_id
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $url
 * @property-read \App\Models\Ticket $ticket
 * @property-read \App\Models\User $user
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Screenshot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Screenshot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Screenshot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Screenshot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Screenshot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Screenshot wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Screenshot whereTicketId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Screenshot whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Screenshot whereUserId($value)
 *
 * @mixin \Eloquent
 */
class Screenshot extends Model
{
    protected $fillable = [
        'user_id',
        'ticket_id',
        'path',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($screenshot) {
            if ($screenshot->path && Storage::disk('public')->exists($screenshot->path)) {
                Storage::disk('public')->delete($screenshot->path);
            }
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
     * @return BelongsTo<Ticket, $this>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->path);
    }
}
