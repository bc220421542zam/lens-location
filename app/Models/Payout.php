<?php

namespace App\Models;

use App\Enums\PayoutBatchStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payout extends Model
{
    protected $fillable = ['owner_id', 'total_amount', 'period_start', 'period_end', 'status', 'processed_at'];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'period_start' => 'datetime',
            'period_end'   => 'datetime',
            'status'       => PayoutBatchStatus::class,
            'processed_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function transactions(): BelongsToMany
    {
        return $this->belongsToMany(Transaction::class, 'payout_transaction')->withTimestamps();
    }
}
