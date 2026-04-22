<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointTransaction extends Model
{
    protected $table = 'point_transactions';

    protected $fillable = [
        'member_id',
        'point_account_id',
        'type',
        'points',
        'before_balance',
        'after_balance',
        'ref_type',
        'ref_id',
        'operator_id',
        'operator_name',
        'reason',
        'source',
        'remark',
        'remark',
    ];

    protected $casts = [
        'points' => 'integer',
        'before_balance' => 'integer',
        'after_balance' => 'integer',
    ];

    /**
     * 关系：属于某个会员
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * 关系：属于某个积分账户
     */
    public function pointAccount(): BelongsTo
    {
        return $this->belongsTo(PointAccount::class);
    }
}
