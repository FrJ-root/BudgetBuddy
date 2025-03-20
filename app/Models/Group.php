<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'currency',
        'description'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function sharedExpenses(): HasMany
    {
        return $this->hasMany(SharedExpense::class);
    }

    public function settlements(): HasMany
    {
        return $this->hasMany(Settlement::class);
    }

    public function hasRemainingBalances(): bool
    {
        // Logic to check if all balances are settled
        // This is a simplified version - actual implementation would be more complex
        $userBalances = $this->calculateBalances();
        
        foreach ($userBalances as $balance) {
            if (abs($balance['balance']) > 0.01) { // Allow for small rounding errors
                return true;
            }
        }
        
        return false;
    }

    /**
     * Calculate the current balances for all users in the group.
     */
    public function calculateBalances(): array
    {
        // Initialize balances for all users
        $balances = [];
        foreach ($this->users as $user) {
            $balances[$user->id] = [
                'user_id' => $user->id,
                'name' => $user->name,
                'balance' => 0
            ];
        }
        
        // Calculate from expenses
        foreach ($this->sharedExpenses as $expense) {
            // Add what each user paid
            foreach ($expense->payments as $payment) {
                $balances[$payment->user_id]['balance'] += $payment->amount;
            }
            
            // Subtract what each user owes
            foreach ($expense->shares as $share) {
                $balances[$share->user_id]['balance'] -= $share->getAmount();
            }
        }
        
        // Adjust for settlements
        foreach ($this->settlements as $settlement) {
            $balances[$settlement->from_user_id]['balance'] -= $settlement->amount;
            $balances[$settlement->to_user_id]['balance'] += $settlement->amount;
        }
        
        return array_values($balances);
    }
}