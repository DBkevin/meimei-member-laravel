<?php

namespace App\Services;

use App\Models\Member;
use App\Models\PointAccount;
use App\Models\PointProduct;
use App\Models\RedemptionOrder;
use App\Models\VerificationRecord;
use App\Enums\RedemptionOrderStatus;
use App\Enums\ProductStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PointRedemptionService
{
    /**
     * 积分兑换商品
     */
    public function redeem(Member $member, PointProduct $product, int $quantity = 1, ?string $receiverName = null, ?string $receiverPhone = null, ?string $operatorName = null): RedemptionOrder
    {
        // 校验会员
        if (!$member->pointAccount) {
            throw new \RuntimeException('会员积分账户不存在');
        }

        // 校验商品
        if ($product->status !== ProductStatus::LISTED) {
            throw new \RuntimeException('商品已下架');
        }

        // 校验库存
        if ($product->stock < $quantity) {
            throw new \RuntimeException('商品库存不足，当前库存: ' . $product->stock);
        }

        // 计算所需积分
        $totalPoints = $product->points_price * $quantity;

        // 校验会员积分
        $availablePoints = $member->pointAccount->getAvailablePoints();
        if ($availablePoints < $totalPoints) {
            throw new \RuntimeException('可用积分不足，当前可用: ' . $availablePoints . '，需要: ' . $totalPoints);
        }

        // 使用数据库事务确保数据一致性
        return DB::transaction(function () use ($member, $product, $quantity, $totalPoints, $receiverName, $receiverPhone, $operatorName) {
            // 1. 扣减会员积分
            $member->pointAccount->spendPoints(
                $totalPoints,
                '兑换商品: ' . $product->name,
                'redemption_order',
                null,
                $operatorName
            );

            // 2. 扣减商品库存
            $product->stock = $product->stock - $quantity;
            $product->save();

            // 3. 创建兑换订单
            $order = RedemptionOrder::create([
                'order_no' => $this->generateOrderNo(),
                'member_id' => $member->id,
                'point_product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_points' => $product->points_price,
                'total_points' => $totalPoints,
                'status' => RedemptionOrderStatus::PENDING,
                'receiver_name' => $receiverName,
                'receiver_phone' => $receiverPhone,
            ]);

            return $order;
        });
    }

    /**
     * 核销订单
     */
    public function verify(RedemptionOrder $order, ?int $salesRepId = null, ?string $remark = null): RedemptionOrder
    {
        // 校验订单状态
        if ($order->status !== RedemptionOrderStatus::PENDING) {
            throw new \RuntimeException('只有待处理的订单才能核销，当前状态: ' . $order->status->label());
        }

        return DB::transaction(function () use ($order, $salesRepId, $remark) {
            // 1. 更新订单状态
            $order->status = RedemptionOrderStatus::VERIFIED;
            $order->verify_sales_rep_id = $salesRepId;
            $order->completed_at = now();
            $order->save();

            // 2. 创建核销记录
            VerificationRecord::create([
                'redemption_order_id' => $order->id,
                'member_id' => $order->member_id,
                'sales_rep_id' => $salesRepId,
                'verified_at' => now(),
                'remark' => $remark,
            ]);

            return $order;
        });
    }

    /**
     * 取消订单
     */
    public function cancel(RedemptionOrder $order, ?string $operatorName = null, ?string $remark = null): RedemptionOrder
    {
        // 校验订单状态
        if ($order->status !== RedemptionOrderStatus::PENDING) {
            throw new \RuntimeException('只有待处理的订单才能取消，当前状态: ' . $order->status->label());
        }

        return DB::transaction(function () use ($order, $operatorName, $remark) {
            // 1. 退还积分给会员
            $member = $order->member;
            if ($member && $member->pointAccount) {
                $member->pointAccount->refundPoints(
                    $order->total_points,
                    '取消订单: ' . $order->order_no . ' - ' . $remark,
                    'redemption_order',
                    $order->id,
                    $operatorName
                );
            }

            // 2. 恢复商品库存
            $product = $order->pointProduct;
            if ($product) {
                $product->stock = $product->stock + $order->quantity;
                $product->save();
            }

            // 3. 更新订单状态
            $order->status = RedemptionOrderStatus::CANCELLED;
            $order->cancelled_at = now();
            $order->remark = $remark;
            $order->save();

            return $order;
        });
    }

    /**
     * 拒绝订单
     */
    public function reject(RedemptionOrder $order, ?string $operatorName = null, ?string $remark = null): RedemptionOrder
    {
        // 校验订单状态
        if ($order->status !== RedemptionOrderStatus::PENDING) {
            throw new \RuntimeException('只有待处理的订单才能拒绝，当前状态: ' . $order->status->label());
        }

        return DB::transaction(function () use ($order, $operatorName, $remark) {
            // 1. 退还积分给会员
            $member = $order->member;
            if ($member && $member->pointAccount) {
                $member->pointAccount->refundPoints(
                    $order->total_points,
                    '拒绝订单: ' . $order->order_no . ' - ' . $remark,
                    'redemption_order',
                    $order->id,
                    $operatorName
                );
            }

            // 2. 恢复商品库存
            $product = $order->pointProduct;
            if ($product) {
                $product->stock = $product->stock + $order->quantity;
                $product->save();
            }

            // 3. 更新订单状态
            $order->status = RedemptionOrderStatus::REJECTED;
            $order->cancelled_at = now();
            $order->remark = $remark;
            $order->save();

            return $order;
        });
    }

    /**
     * 生成订单号
     */
    protected function generateOrderNo(): string
    {
        return 'RD' . date('YmdHis') . Str::random(6);
    }
}