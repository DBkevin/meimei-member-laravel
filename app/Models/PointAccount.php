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

    /**
     * 增加积分
     */
    public function earnPoints(int $points, ?string $remark = null, ?string $refType = null, ?int $refId = null, ?string $operatorName = null): self
    {
        if ($points <= 0) {
            throw new \InvalidArgumentException('积分必须大于0');
        }

        $beforeBalance = $this->balance;
        $afterBalance = $beforeBalance + $points;

        // 创建积分流水
        PointTransaction::create([
            'member_id' => $this->member_id,
            'point_account_id' => $this->id,
            'type' => \App\Enums\PointTransactionType::EARN,
            'points' => $points,
            'before_balance' => $beforeBalance,
            'after_balance' => $afterBalance,
            'ref_type' => $refType,
            'ref_id' => $refId,
            'operator_name' => $operatorName,
            'remark' => $remark,
        ]);

        // 更新账户
        $this->balance = $afterBalance;
        $this->total_earned = $this->total_earned + $points;
        $this->save();

        return $this;
    }

    /**
     * 扣减积分（兑换消费）
     */
    public function spendPoints(int $points, ?string $remark = null, ?string $refType = null, ?int $refId = null, ?string $operatorName = null): self
    {
        if ($points <= 0) {
            throw new \InvalidArgumentException('积分必须大于0');
        }

        // 检查可用积分是否充足（可用积分 = 余额 - 冻结积分）
        $availablePoints = $this->balance - $this->frozen_points;
        if ($availablePoints < $points) {
            throw new \RuntimeException('可用积分不足，当前可用: ' . $availablePoints . '，需要: ' . $points);
        }

        $beforeBalance = $this->balance;
        $afterBalance = $beforeBalance - $points;

        // 创建积分流水
        PointTransaction::create([
            'member_id' => $this->member_id,
            'point_account_id' => $this->id,
            'type' => \App\Enums\PointTransactionType::SPEND,
            'points' => $points,
            'before_balance' => $beforeBalance,
            'after_balance' => $afterBalance,
            'ref_type' => $refType,
            'ref_id' => $refId,
            'operator_name' => $operatorName,
            'remark' => $remark,
        ]);

        // 更新账户
        $this->balance = $afterBalance;
        $this->total_spent = $this->total_spent + $points;
        $this->save();

        return $this;
    }

    /**
     * 调整积分（管理员操作）
     */
    public function adjustPoints(int $points, ?string $remark = null, ?string $operatorName = null): self
    {
        $beforeBalance = $this->balance;
        $afterBalance = $beforeBalance + $points;

        // 调整后余额不能为负
        if ($afterBalance < 0) {
            throw new \RuntimeException('调整后积分不能为负数');
        }

        // 创建积分流水
        PointTransaction::create([
            'member_id' => $this->member_id,
            'point_account_id' => $this->id,
            'type' => \App\Enums\PointTransactionType::ADJUST,
            'points' => $points,
            'before_balance' => $beforeBalance,
            'after_balance' => $afterBalance,
            'operator_name' => $operatorName,
            'remark' => $remark,
        ]);

        // 更新账户
        $this->balance = $afterBalance;
        if ($points > 0) {
            $this->total_earned = $this->total_earned + $points;
        } else {
            $this->total_spent = $this->total_spent + abs($points);
        }
        $this->save();

        return $this;
    }

    /**
     * 退款积分（取消订单时退回）
     */
    public function refundPoints(int $points, ?string $remark = null, ?string $refType = null, ?int $refId = null, ?string $operatorName = null): self
    {
        if ($points <= 0) {
            throw new \InvalidArgumentException('积分必须大于0');
        }

        $beforeBalance = $this->balance;
        $afterBalance = $beforeBalance + $points;

        // 创建积分流水
        PointTransaction::create([
            'member_id' => $this->member_id,
            'point_account_id' => $this->id,
            'type' => \App\Enums\PointTransactionType::REFUND,
            'points' => $points,
            'before_balance' => $beforeBalance,
            'after_balance' => $afterBalance,
            'ref_type' => $refType,
            'ref_id' => $refId,
            'operator_name' => $operatorName,
            'remark' => $remark,
        ]);

        // 更新账户
        $this->balance = $afterBalance;
        $this->save();

        return $this;
    }

    /**
     * 冻结积分
     */
    public function freezePoints(int $points): self
    {
        if ($points <= 0) {
            throw new \InvalidArgumentException('积分必须大于0');
        }

        // 检查可用积分是否充足
        $availablePoints = $this->balance - $this->frozen_points;
        if ($availablePoints < $points) {
            throw new \RuntimeException('可用积分不足，无法冻结');
        }

        $this->frozen_points = $this->frozen_points + $points;
        $this->save();

        return $this;
    }

    /**
     * 解冻积分
     */
    public function unfreezePoints(int $points): self
    {
        if ($points <= 0) {
            throw new \InvalidArgumentException('积分必须大于0');
        }

        if ($points > $this->frozen_points) {
            throw new \RuntimeException('冻结积分不足');
        }

        $this->frozen_points = $this->frozen_points - $points;
        $this->save();

        return $this;
    }

    /**
     * 获取可用积分
     */
    public function getAvailablePoints(): int
    {
        return $this->balance - $this->frozen_points;
    }
}
