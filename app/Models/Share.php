<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Share extends Model
{
    use HasFactory;

    protected $fillable = [
        'shared_expense_id',
        'user_id',
        'percentage',
        'fixed_amount'
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'fixed_amount' => 'decimal:2'
    ];

    public function sharedExpense(): BelongsTo
    {
        return $this->belongsTo(SharedExpense::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAmount(): float
    {
        if (!is_null($this->fixed_amount)) {
            return (float)$this->fixed_amount;
        }

        if (!is_null($this->percentage)) {
            return (float)$this->sharedExpense->amount * ((float)$this->percentage / 100);
        }

        return (float)$this->sharedExpense->amount / $this->sharedExpense->shares()->count();
    }
}