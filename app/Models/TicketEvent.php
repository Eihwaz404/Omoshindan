<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketEvent extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'ticket_id',
        'actor_id',
        'type',
        'from_status',
        'to_status',
        'from_area',
        'to_area',
        'note',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return config('support.event_types.'.$this->type, $this->type);
    }
}
