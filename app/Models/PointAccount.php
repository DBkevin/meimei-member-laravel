<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointAccount extends Model
{
    protected $table = 'point_accounts';

    protected $fillable = [
        'member_id',
        'balance',
        'total_earned',
        'total_spent',
        'frozen_points',
    ];

    protected $casts = [
        'balance' => 'integer',
        'total_earned' => 'integer',
        'total_spent' => 'integer',
        'frozen_points' => 'integer',
    ];

    public $timestamps = false;

    /**
     * 关系：属于某个会员
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * 关系：多个积分流水
     */
    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }
}
