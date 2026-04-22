<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RedemptionOrder extends Model
{
    protected $table = 'redemption_orders';

    protected $fillable = [
        'order_no',
        'member_id',
        'point_product_id',
        'product_name',
        'quantity',
        'unit_points',
        'total_points',
        'status',
        'receiver_name',
        'receiver_phone',
        'verify_sales_rep_id',
        'remark',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_points' => 'integer',
        'total_points' => 'integer',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * 关系：属于某个会员
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * 关系：属于某个积分商品
     */
    public function pointProduct(): BelongsTo
    {
        return $this->belongsTo(PointProduct::class);
    }

    /**
     * 关系：属于某个核销销售
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(SalesRep::class, 'verify_sales_rep_id');
    }

    /**
     * 关系：多个核销记录
     */
    public function verificationRecords(): HasMany
    {
        return $this->hasMany(VerificationRecord::class);
    }
}
