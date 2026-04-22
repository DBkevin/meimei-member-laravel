<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\SalesRep;
use App\Models\PointAccount;
use App\Models\PointTransaction;
use App\Services\PointAdjustmentService;
use App\Enums\PointTransactionReason;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointAdjustmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected PointAdjustmentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PointAdjustmentService::class);
    }

    public function test_can_earn_points(): void
    {
        $member = Member::create([
            'name' => '测试会员',
            'phone' => '13900000001',
            'status' => 1,
        ]);
        
        $transaction = $this->service->earn(
            $member,
            100,
            PointTransactionReason::SHOP_REWARD->value,
            '到店奖励测试'
        );
        
        $this->assertInstanceOf(PointTransaction::class, $transaction);
        $this->assertEquals('earn', $transaction->type);
        $this->assertEquals(100, $transaction->points);
        
        $member->refresh();
        $this->assertEquals(100, $member->pointAccount->balance);
        $this->assertEquals(100, $member->pointAccount->total_earned);
    }

    public function test_can_spend_points(): void
    {
        $member = Member::create([
            'name' => '测试会员',
            'phone' => '13900000002',
            'status' => 1,
        ]);
        $member->pointAccount->balance = 500;
        $member->pointAccount->save();
        
        $transaction = $this->service->spend(
            $member,
            100,
            PointTransactionReason::REDEMPTION->value,
            '兑换商品'
        );
        
        $this->assertInstanceOf(PointTransaction::class, $transaction);
        $this->assertEquals('spend', $transaction->type);
        
        $member->refresh();
        $this->assertEquals(400, $member->pointAccount->balance);
        $this->assertEquals(100, $member->pointAccount->total_spent);
    }

    public function test_cannot_spend_insufficient_points(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('可用积分不足');
        
        $member = Member::create([
            'name' => '测试会员',
            'phone' => '13900000003',
            'status' => 1,
        ]);
        $member->pointAccount->balance = 50;
        $member->pointAccount->save();
        
        $this->service->spend(
            $member,
            100,
            PointTransactionReason::REDEMPTION->value,
            '兑换商品'
        );
    }

    public function test_earn_creates_transaction_record(): void
    {
        $member = Member::create([
            'name' => '测试会员',
            'phone' => '13900000004',
            'status' => 1,
        ]);
        
        $transaction = $this->service->earn(
            $member,
            200,
            PointTransactionReason::CONSUME_REWARD->value,
            '消费奖励',
            1
        );
        
        $this->assertDatabaseHas('point_transactions', [
            'member_id' => $member->id,
            'type' => 'earn',
            'reason' => PointTransactionReason::CONSUME_REWARD->value,
            'points' => 200,
            'operator_id' => 1,
            'remark' => '消费奖励',
        ]);
    }

    public function test_spend_creates_transaction_record(): void
    {
        $member = Member::create([
            'name' => '测试会员',
            'phone' => '13900000005',
            'status' => 1,
        ]);
        $member->pointAccount->balance = 500;
        $member->pointAccount->save();
        
        $transaction = $this->service->spend(
            $member,
            150,
            PointTransactionReason::REDEMPTION->value,
            '兑换礼品',
            1
        );
        
        $this->assertDatabaseHas('point_transactions', [
            'member_id' => $member->id,
            'type' => 'spend',
            'reason' => PointTransactionReason::REDEMPTION->value,
            'points' => 150,
            'operator_id' => 1,
            'remark' => '兑换礼品',
        ]);
    }

    public function test_transaction_records_reason_and_remark(): void
    {
        $member = Member::create([
            'name' => '测试会员',
            'phone' => '13900000006',
            'status' => 1,
        ]);
        
        $transaction = $this->service->earn(
            $member,
            300,
            PointTransactionReason::REFERRAL_REWARD->value,
            '转介绍奖励备注'
        );
        
        $this->assertEquals(PointTransactionReason::REFERRAL_REWARD->value, $transaction->reason);
        $this->assertEquals('转介绍奖励备注', $transaction->remark);
    }

    public function test_can_freeze_points(): void
    {
        $member = Member::create([
            'name' => '测试会员',
            'phone' => '13900000007',
            'status' => 1,
        ]);
        $member->pointAccount->balance = 1000;
        $member->pointAccount->save();
        
        $transaction = $this->service->freeze(
            $member,
            300,
            PointTransactionReason::MANUAL_ADJUST->value,
            '冻结测试'
        );
        
        $member->refresh();
        $this->assertEquals(300, $member->pointAccount->frozen_points);
    }

    public function test_cannot_freeze_more_than_available(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('可用积分不足');
        
        $member = Member::create([
            'name' => '测试会员',
            'phone' => '13900000008',
            'status' => 1,
        ]);
        $member->pointAccount->balance = 100;
        $member->pointAccount->save();
        
        $this->service->freeze(
            $member,
            200,
            PointTransactionReason::MANUAL_ADJUST->value
        );
    }

    public function test_can_unfreeze_points(): void
    {
        $member = Member::create([
            'name' => '测试会员',
            'phone' => '13900000009',
            'status' => 1,
        ]);
        $member->pointAccount->balance = 1000;
        $member->pointAccount->frozen_points = 300;
        $member->pointAccount->save();
        
        $transaction = $this->service->unfreeze(
            $member,
            200,
            PointTransactionReason::MANUAL_ADJUST->value,
            '解冻测试'
        );
        
        $member->refresh();
        $this->assertEquals(100, $member->pointAccount->frozen_points);
    }

    public function test_points_must_be_positive_integer(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('积分数量必须为正整数');
        
        $member = Member::create([
            'name' => '测试会员',
            'phone' => '13900000010',
            'status' => 1,
        ]);
        
        $this->service->earn($member, 0, PointTransactionReason::MANUAL_ADJUST->value);
    }

    public function test_can_refund_points(): void
    {
        $member = Member::create([
            'name' => '测试会员',
            'phone' => '13900000011',
            'status' => 1,
        ]);
        $member->pointAccount->balance = 500;
        $member->pointAccount->save();
        
        $transaction = $this->service->refund(
            $member,
            100,
            PointTransactionReason::ORDER_REFUND->value,
            '订单取消退还'
        );
        
        $member->refresh();
        $this->assertEquals(600, $member->pointAccount->balance);
        $this->assertEquals(100, $member->pointAccount->total_earned);
    }

    public function test_order_refund_does_not_break_balance(): void
    {
        $member = Member::create([
            'name' => '测试会员',
            'phone' => '13900000012',
            'status' => 1,
        ]);
        $member->pointAccount->balance = 500;
        $member->pointAccount->total_spent = 200;
        $member->pointAccount->save();
        
        $this->service->refund(
            $member,
            200,
            PointTransactionReason::ORDER_REFUND->value,
            '订单取消'
        );
        
        $member->refresh();
        $this->assertEquals(700, $member->pointAccount->balance);
        $this->assertEquals(200, $member->pointAccount->total_earned);
    }
}
