<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    protected $table = 'members';

    protected $fillable = [
        'name',
        'phone',
        'gender',
        'birthday',
        'crm_archive_no',
        'source',
        'level',
        'status',
        'sales_rep_id',
        'remark',
    ];

    protected $casts = [
        'birthday' => 'date',
        'status' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        // 创建会员时自动创建积分账户
        static::created(function ($member) {
            PointAccount::create([
                'member_id' => $member->id,
                'balance' => 0,
                'total_earned' => 0,
                'total_spent' => 0,
                'frozen_points' => 0,
            ]);
        });
    }

    /**
     * 关系：属于某个销售
     */
    public function salesRep(): BelongsTo
    {
        return $this->belongsTo(SalesRep::class);
    }

    /**
     * 关系：一对一积分账户
     */
    public function pointAccount(): HasOne
    {
        return $this->hasOne(PointAccount::class);
    }

    /**
     * 关系：多个积分流水
     */
    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    /**
     * 关系：多个兑换订单
     */
    public function redemptionOrders(): HasMany
    {
        return $this->hasMany(RedemptionOrder::class);
    }

    /**
     * 关系：多个核销记录
     */
    public function verificationRecords(): HasMany
    {
        return $this->hasMany(VerificationRecord::class);
    }
}
