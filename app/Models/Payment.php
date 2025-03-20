<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'shared_expense_id',
        'user_id',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function sharedExpense(): BelongsTo
    {
        return $this->belongsTo(SharedExpense::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}