<?php

namespace App\Models;

use App\Support\TicketSlaCalculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_ANALYSIS = 'analysis';
    public const STATUS_PENDING = 'pending';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    public const PRIORITY_LOW = 'low';
    public const PRIORITY_NORMAL = 'normal';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';

    protected $fillable = [
        'subject',
        'subject_id',
        'description',
        'requester_id',
        'assigned_to_id',
        'area_id',
        'current_area',
        'status',
        'priority',
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

    public function supportSubject(): BelongsTo
    {
        return $this->belongsTo(SupportSubject::class, 'subject_id');
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
        $digits = (string) $this->id;
        $paddedLength = max(9, (int) ceil(strlen($digits) / 3) * 3);
        $paddedDigits = str_pad($digits, $paddedLength, '0', STR_PAD_LEFT);

        return 'TK-'.strrev(trim(chunk_split(strrev($paddedDigits), 3, '.'), '.'));
    }

    public function getStatusLabelAttribute(): string
    {
        return config('support.statuses.'.$this->status, $this->status);
    }

    public function getPriorityLabelAttribute(): string
    {
        return config('support.priorities.'.$this->priority.'.label', $this->priority ?? '-');
    }

    public function getAreaLabelAttribute(): string
    {
        return $this->area?->name ?? $this->current_area ?? '-';
    }

    public function getSubjectLabelAttribute(): string
    {
        return $this->subject ?: ($this->supportSubject?->name ?? '-');
    }

    /**
     * Summary of the SLA clock for the ticket.
     *
     * @return array<string, mixed>
     */
    public function slaSummary(): array
    {
        return app(TicketSlaCalculator::class)->summary($this);
    }

    /**
     * Infer the most likely support area based on the ticket text.
     */
    public static function detectArea(string $subject, string $description): ?SupportArea
    {
        return SupportArea::detectFromText($subject, $description);
    }
}
