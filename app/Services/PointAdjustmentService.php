<?php

namespace App\Services;

use App\Models\Member;
use App\Models\PointAccount;
use App\Models\PointTransaction;
use App\Enums\PointTransactionReason;
use Illuminate\Support\Facades\DB;

/**
 * 积分人工调整服务
 * 
 * 提供积分的人工发放、扣减、退还、冻结、解冻等操作
 * 所有操作都使用数据库事务保证数据一致性
 */
class PointAdjustmentService
{
    /**
     * 发放积分
     */
    public function earn(Member $member, int $points, string $reason, ?string $remark = null, ?int $operatorId = null): PointTransaction
    {
        $this->validatePoints($points);
        
        return DB::transaction(function () use ($member, $points, $reason, $remark, $operatorId) {
            $account = $member->pointAccount;
            
            $beforeBalance = $account->balance;
            $account->balance += $points;
            $account->total_earned += $points;
            $account->save();
            
            return PointTransaction::create([
                'member_id' => $member->id,
                'point_account_id' => $account->id,
                'type' => 'earn',
                'reason' => $reason,
                'points' => $points,
                'before_balance' => $beforeBalance,
                'after_balance' => $account->balance,
                'operator_id' => $operatorId,
                'operator_name' => $this->getOperatorName($operatorId),
                'remark' => $remark,
            ]);
        });
    }

    /**
     * 扣减积分
     */
    public function spend(Member $member, int $points, string $reason, ?string $remark = null, ?int $operatorId = null): PointTransaction
    {
        $this->validatePoints($points);
        
        return DB::transaction(function () use ($member, $points, $reason, $remark, $operatorId) {
            $account = $member->pointAccount;
            
            $availablePoints = $account->balance - $account->frozen_points;
            if ($availablePoints < $points) {
                throw new \InvalidArgumentException("可用积分不足，当前可用积分: {$availablePoints}");
            }
            
            $beforeBalance = $account->balance;
            $account->balance -= $points;
            $account->total_spent += $points;
            $account->save();
            
            return PointTransaction::create([
                'member_id' => $member->id,
                'point_account_id' => $account->id,
                'type' => 'spend',
                'reason' => $reason,
                'points' => $points,
                'before_balance' => $beforeBalance,
                'after_balance' => $account->balance,
                'operator_id' => $operatorId,
                'operator_name' => $this->getOperatorName($operatorId),
                'remark' => $remark,
            ]);
        });
    }

    /**
     * 退还积分
     */
    public function refund(Member $member, int $points, string $reason, ?string $remark = null, ?int $operatorId = null): PointTransaction
    {
        $this->validatePoints($points);
        
        return DB::transaction(function () use ($member, $points, $reason, $remark, $operatorId) {
            $account = $member->pointAccount;
            
            $beforeBalance = $account->balance;
            $account->balance += $points;
            $account->total_earned += $points;
            $account->save();
            
            return PointTransaction::create([
                'member_id' => $member->id,
                'point_account_id' => $account->id,
                'type' => 'refund',
                'reason' => $reason,
                'points' => $points,
                'before_balance' => $beforeBalance,
                'after_balance' => $account->balance,
                'operator_id' => $operatorId,
                'operator_name' => $this->getOperatorName($operatorId),
                'remark' => $remark,
            ]);
        });
    }

    /**
     * 冻结积分
     */
    public function freeze(Member $member, int $points, string $reason, ?string $remark = null, ?int $operatorId = null): PointTransaction
    {
        $this->validatePoints($points);
        
        return DB::transaction(function () use ($member, $points, $reason, $remark, $operatorId) {
            $account = $member->pointAccount;
            
            $availablePoints = $account->balance - $account->frozen_points;
            if ($availablePoints < $points) {
                throw new \InvalidArgumentException("可用积分不足，无法冻结，当前可用积分: {$availablePoints}");
            }
            
            $beforeFrozen = $account->frozen_points;
            $account->frozen_points += $points;
            $account->save();
            
            return PointTransaction::create([
                'member_id' => $member->id,
                'point_account_id' => $account->id,
                'type' => 'adjust',
                'reason' => $reason,
                'points' => $points,
                'before_balance' => $beforeFrozen,
                'after_balance' => $account->frozen_points,
                'operator_id' => $operatorId,
                'operator_name' => $this->getOperatorName($operatorId),
                'remark' => $remark,
            ]);
        });
    }

    /**
     * 解冻积分
     */
    public function unfreeze(Member $member, int $points, string $reason, ?string $remark = null, ?int $operatorId = null): PointTransaction
    {
        $this->validatePoints($points);
        
        return DB::transaction(function () use ($member, $points, $reason, $remark, $operatorId) {
            $account = $member->pointAccount;
            
            if ($account->frozen_points < $points) {
                throw new \InvalidArgumentException("冻结积分不足，当前冻结积分: {$account->frozen_points}");
            }
            
            $beforeFrozen = $account->frozen_points;
            $account->frozen_points -= $points;
            $account->save();
            
            return PointTransaction::create([
                'member_id' => $member->id,
                'point_account_id' => $account->id,
                'type' => 'adjust',
                'reason' => $reason,
                'points' => $points,
                'before_balance' => $beforeFrozen,
                'after_balance' => $account->frozen_points,
                'operator_id' => $operatorId,
                'operator_name' => $this->getOperatorName($operatorId),
                'remark' => $remark,
            ]);
        });
    }

    protected function validatePoints(int $points): void
    {
        if ($points <= 0) {
            throw new \InvalidArgumentException('积分数量必须为正整数');
        }
    }

    protected function getOperatorName(?int $operatorId): ?string
    {
        if (!$operatorId) {
            return null;
        }
        
        $user = \App\Models\User::find($operatorId);
        return $user?->name;
    }
}