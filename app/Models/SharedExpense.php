<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SharedExpense extends Model
{
    protected $fillable = [
        'group_id',
        'user_id',
        'title',
        'amount',
        'description',
        'date',
        'split_type'
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(Share::class);
    }
}