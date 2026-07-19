<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

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
        return config('support.areas.'.$this->current_area.'.label', $this->current_area ?? '-');
    }

    /**
     * Infer the most likely support area based on the ticket text.
     */
    public static function detectArea(string $subject, string $description): string
    {
        $defaultArea = config('support.routing.default_area', array_key_first(config('support.areas', [])));
        $keywords = config('support.routing.keywords', []);
        $text = static::normalizeRoutingText($subject.' '.$description);

        $bestArea = $defaultArea;
        $bestScore = 0;

        foreach ($keywords as $area => $areaKeywords) {
            $score = 0;

            foreach ((array) $areaKeywords as $keyword) {
                $score += substr_count($text, static::normalizeRoutingText((string) $keyword));
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestArea = $area;
            }
        }

        return $bestArea;
    }

    private static function normalizeRoutingText(string $value): string
    {
        $value = Str::lower($value);
        $value = preg_replace('/[^a-z0-9]+/u', ' ', $value) ?? $value;

        return trim($value);
    }
}
