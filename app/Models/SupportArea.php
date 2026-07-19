<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class SupportArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'support_area_user', 'support_area_id', 'user_id')->withTimestamps();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'area_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function getLabelAttribute(): string
    {
        return $this->name;
    }

    public static function detectFromText(string $subject, string $description): ?self
    {
        $text = static::normalizeRoutingText($subject.' '.$description);
        $keywords = config('support.routing.keywords', []);
        $areas = static::query()->active()->get()->keyBy('slug');
        $defaultSlug = config('support.routing.default_area');

        $bestSlug = $areas->has($defaultSlug) ? $defaultSlug : $areas->keys()->first();
        $bestScore = 0;

        foreach ($areas as $slug => $area) {
            $score = 0;

            foreach ((array) ($keywords[$slug] ?? []) as $keyword) {
                $score += substr_count($text, static::normalizeRoutingText((string) $keyword));
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestSlug = $slug;
            }
        }

        return $bestSlug ? $areas->get($bestSlug) : null;
    }

    public static function normalizeRoutingText(string $value): string
    {
        $value = Str::lower($value);
        $value = preg_replace('/[^a-z0-9]+/u', ' ', $value) ?? $value;

        return trim($value);
    }
}
