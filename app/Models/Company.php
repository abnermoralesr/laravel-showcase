<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'state',
        'registered_agent_type',
        'registered_agent_id',
    ];

    /**
     * The user who owns this company.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registeredAgent(): MorphTo
    {
        return $this->morphTo('registered_agent');
    }
}
