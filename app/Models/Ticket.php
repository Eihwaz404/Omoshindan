<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_ANALYSIS = 'analysis';
    public const STATUS_PROGRESS = 'progress';
    public const STATUS_PENDING = 'pending';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'subject',
        'description',
        'requester_id',
        'assigned_to_id',
        'area_id',
        'current_area',
        'status',
        'resolved_at',
        'closed_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(SupportArea::class, 'area_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(TicketEvent::class)->latest();
    }

    public function scopeVisibleTo($query, User $user)
    {
        if (! $user->isTechnical()) {
            $query->where('requester_id', $user->id);
        }

        return $query;
    }

    public function isVisibleTo(User $user): bool
    {
        return $user->isTechnical() || $this->requester_id === $user->id;
    }

    public function getReferenceAttribute(): string
    {
        return 'TK-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getStatusLabelAttribute(): string
    {
        return config('support.statuses.'.$this->status, $this->status);
    }

    public function getAreaLabelAttribute(): string
    {
        return $this->area?->name ?? $this->current_area ?? '-';
    }

    /**
     * Infer the most likely support area based on the ticket text.
     */
    public static function detectArea(string $subject, string $description): ?SupportArea
    {
        return SupportArea::detectFromText($subject, $description);
    }
}
