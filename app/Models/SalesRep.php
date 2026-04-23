<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesRep extends Model
{
    use SoftDeletes;

    protected $table = 'sales_reps';

    protected $fillable = [
        'name',
        'phone',
        'user_id',
        'status',
        'remark',
    ];

    protected $casts = [
        'status' => 'integer',
    ];

    /**
     * 关系：一个销售多个会员
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * 关系：作为核销销售的订单
     */
    public function verifiedRedemptionOrders(): HasMany
    {
        return $this->hasMany(RedemptionOrder::class, 'verify_sales_rep_id');
    }

    /**
     * 关系：跟进记录
     */
    public function followUpRecords(): HasMany
    {
        return $this->hasMany(FollowUpRecord::class);
    }

    /**
     * 关系：核销记录
     */
    public function verificationRecords(): HasMany
    {
        return $this->hasMany(VerificationRecord::class);
    }

    /**
     * 关系：销售任务
     */
    public function salesTasks(): HasMany
    {
        return $this->hasMany(SalesTask::class);
    }
}