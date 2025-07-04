<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'request_date',
        'approval_date',
        'approval_by',
        'due_date',
        'return_date',
        'fine_amount',
        'notes',
    ];

    protected $casts = [
        'request_date' => 'datetime',
        'approval_date' => 'datetime',
        'due_date' => 'datetime',
        'return_date' => 'datetime',
    ];

    /**
     * Get the user who borrowed the book (include soft deleted)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    /**
     * Get the book that was borrowed (include soft deleted)
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'book_id')->withTrashed();
    }

    /**
     * Get the admin who approved the loan (include soft deleted)
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approval_by')->withTrashed();
    }

    /**
     * Check if the loan is overdue
     */
    public function isOverdue(): bool
    {
        if ($this->status === 'dipinjam' && $this->due_date) {
            return Carbon::now()->greaterThan($this->due_date);
        }
        
        return false;
    }

    /**
     * Calculate fine amount based on days overdue
     * 
     * @param float $finePerDay Fine amount per day
     * @return float Total fine amount
     */
    public function calculateFine(float $finePerDay = 1000): float
    {
        if (!$this->isOverdue() || !$this->due_date) {
            return 0;
        }

        $daysLate = Carbon::now()->diffInDays($this->due_date);
        return $daysLate * $finePerDay;
    }

    /**
     * Update status to "terlambat" if overdue
     */
    public function checkOverdueStatus(): void
    {
        if ($this->status === 'dipinjam' && $this->isOverdue()) {
            $this->status = 'terlambat';
            $this->save();
        }
    }
} 