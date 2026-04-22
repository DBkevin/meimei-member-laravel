<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VerificationRecord extends Model
{
    protected $table = 'verification_records';

    protected $fillable = [
        'redemption_order_id',
        'member_id',
        'sales_rep_id',
        'verified_at',
        'remark',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    /**
     * 关系：属于某个兑换订单
     */
    public function redemptionOrder(): BelongsTo
    {
        return $this->belongsTo(RedemptionOrder::class);
    }

    /**
     * 关系：属于某个会员
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * 关系：属于某个销售
     */
    public function salesRep(): BelongsTo
    {
        return $this->belongsTo(SalesRep::class);
    }
}
