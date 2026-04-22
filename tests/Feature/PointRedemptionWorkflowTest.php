<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\PointAccount;
use App\Models\PointProduct;
use App\Models\RedemptionOrder;
use App\Models\VerificationRecord;
use App\Services\PointRedemptionService;
use App\Enums\RedemptionOrderStatus;
use App\Enums\ProductStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointRedemptionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected PointRedemptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PointRedemptionService::class);
    }

    /**
     * Test: 创建会员后自动生成积分账户
     */
    public function test_member_auto_creates_point_account()
    {
        $member = Member::create([
            'name' => '测试会员',
            'phone' => '13900000001',
            'status' => 1,
        ]);

        $this->assertNotNull($member->pointAccount);
        $this->assertEquals(0, $member->pointAccount->balance);
    }

    /**
     * Test: 增加积分后账户余额增加
     */
    public function test_earn_points_increases_balance()
    {
        $member = Member::create([
            'name' => '测试会员2',
            'phone' => '13900000002',
            'status' => 1,
        ]);

        $member->pointAccount->earnPoints(100, '测试积分');

        $this->assertEquals(100, $member->fresh()->pointAccount->balance);
        $this->assertEquals(100, $member->fresh()->pointAccount->total_earned);
    }

    /**
     * Test: 积分不足时不能兑换
     */
    public function test_cannot_redeem_with_insufficient_points()
    {
        $member = Member::create([
            'name' => '测试会员3',
            'phone' => '13900000003',
            'status' => 1,
        ]);

        $product = PointProduct::create([
            'name' => '测试商品1',
            'points_price' => 100,
            'stock' => 10,
            'status' => ProductStatus::LISTED,
        ]);

        // 会员只有0积分，无法兑换需要100积分的商品
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('可用积分不足');

        $this->service->redeem($member, $product);
    }

    /**
     * Test: 库存不足时不能兑换
     */
    public function test_cannot_redeem_with_insufficient_stock()
    {
        $member = Member::create([
            'name' => '测试会员4',
            'phone' => '13900000004',
            'status' => 1,
        ]);

        // 给会员增加积分
        $member->pointAccount->earnPoints(100, '测试积分');

        $product = PointProduct::create([
            'name' => '测试商品2',
            'points_price' => 100,
            'stock' => 0, // 库存为0
            'status' => ProductStatus::LISTED,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('商品库存不足');

        $this->service->redeem($member, $product);
    }

    /**
     * Test: 商品下架时不能兑换
     */
    public function test_cannot_redeem_unlisted_product()
    {
        $member = Member::create([
            'name' => '测试会员5',
            'phone' => '13900000005',
            'status' => 1,
        ]);

        $member->pointAccount->earnPoints(100, '测试积分');

        $product = PointProduct::create([
            'name' => '测试商品3',
            'points_price' => 100,
            'stock' => 10,
            'status' => ProductStatus::DELISTED, // 下架状态
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('商品已下架');

        $this->service->redeem($member, $product);
    }

    /**
     * Test: 兑换成功后扣减积分
     */
    public function test_redeem_deducts_points()
    {
        $member = Member::create([
            'name' => '测试会员6',
            'phone' => '13900000006',
            'status' => 1,
        ]);

        $member->pointAccount->earnPoints(200, '测试积分');

        $product = PointProduct::create([
            'name' => '测试商品4',
            'points_price' => 150,
            'stock' => 10,
            'status' => ProductStatus::LISTED,
        ]);

        $order = $this->service->redeem($member, $product);

        $this->assertEquals(RedemptionOrderStatus::PENDING, $order->status);
        $this->assertEquals(50, $member->fresh()->pointAccount->balance); // 200 - 150 = 50
    }

    /**
     * Test: 兑换成功后扣减库存
     */
    public function test_redeem_deducts_stock()
    {
        $member = Member::create([
            'name' => '测试会员7',
            'phone' => '13900000007',
            'status' => 1,
        ]);

        $member->pointAccount->earnPoints(100, '测试积分');

        $product = PointProduct::create([
            'name' => '测试商品5',
            'points_price' => 100,
            'stock' => 10,
            'status' => ProductStatus::LISTED,
        ]);

        $this->service->redeem($member, $product);

        $this->assertEquals(9, $product->fresh()->stock); // 10 - 1 = 9
    }

    /**
     * Test: 兑换成功后生成积分流水
     */
    public function test_redeem_creates_point_transaction()
    {
        $member = Member::create([
            'name' => '测试会员8',
            'phone' => '13900000008',
            'status' => 1,
        ]);

        $member->pointAccount->earnPoints(100, '测试积分');

        $product = PointProduct::create([
            'name' => '测试商品6',
            'points_price' => 100,
            'stock' => 10,
            'status' => ProductStatus::LISTED,
        ]);

        $order = $this->service->redeem($member, $product);

        $this->assertDatabaseHas('point_transactions', [
            'member_id' => $member->id,
            'type' => 'spend',
            'points' => 100,
            'ref_type' => 'redemption_order',
        ]);
    }

    /**
     * Test: 订单核销后生成核销记录
     */
    public function test_verify_creates_verification_record()
    {
        $member = Member::create([
            'name' => '测试会员9',
            'phone' => '13900000009',
            'status' => 1,
        ]);

        $member->pointAccount->earnPoints(100, '测试积分');

        $product = PointProduct::create([
            'name' => '测试商品7',
            'points_price' => 100,
            'stock' => 10,
            'status' => ProductStatus::LISTED,
        ]);

        $order = $this->service->redeem($member, $product);

        // 核销订单
        $this->service->verify($order, null, '测试核销');

        // 验证订单状态
        $this->assertEquals(RedemptionOrderStatus::VERIFIED, $order->fresh()->status);

        // 验证核销记录已创建
        $this->assertDatabaseHas('verification_records', [
            'redemption_order_id' => $order->id,
            'member_id' => $member->id,
        ]);
    }

    /**
     * Test: 已核销订单不能再次取消
     */
    public function test_cannot_cancel_verified_order()
    {
        $member = Member::create([
            'name' => '测试会员10',
            'phone' => '13900000010',
            'status' => 1,
        ]);

        $member->pointAccount->earnPoints(100, '测试积分');

        $product = PointProduct::create([
            'name' => '测试商品8',
            'points_price' => 100,
            'stock' => 10,
            'status' => ProductStatus::LISTED,
        ]);

        $order = $this->service->redeem($member, $product);
        $this->service->verify($order);

        // 尝试取消已核销的订单，应该失败
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('只有待处理的订单才能取消');

        $this->service->cancel($order);
    }

    /**
     * Test: 已取消订单不能再次核销
     */
    public function test_cannot_verify_cancelled_order()
    {
        $member = Member::create([
            'name' => '测试会员11',
            'phone' => '13900000011',
            'status' => 1,
        ]);

        $member->pointAccount->earnPoints(100, '测试积分');

        $product = PointProduct::create([
            'name' => '测试商品9',
            'points_price' => 100,
            'stock' => 10,
            'status' => ProductStatus::LISTED,
        ]);

        $order = $this->service->redeem($member, $product);
        $this->service->cancel($order);

        // 尝试核销已取消的订单，应该失败
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('只有待处理的订单才能核销');

        $this->service->verify($order);
    }

    /**
     * Test: 取消订单后积分退还
     */
    public function test_cancel_refunds_points()
    {
        $member = Member::create([
            'name' => '测试会员12',
            'phone' => '13900000012',
            'status' => 1,
        ]);

        $member->pointAccount->earnPoints(200, '测试积分');

        $product = PointProduct::create([
            'name' => '测试商品10',
            'points_price' => 100,
            'stock' => 10,
            'status' => ProductStatus::LISTED,
        ]);

        $order = $this->service->redeem($member, $product);

        // 取消订单
        $this->service->cancel($order, null, '测试取消');

        // 积分应该退还
        $this->assertEquals(200, $member->fresh()->pointAccount->balance);
    }

    /**
     * Test: 取消订单后库存恢复
     */
    public function test_cancel_restores_stock()
    {
        $member = Member::create([
            'name' => '测试会员13',
            'phone' => '13900000013',
            'status' => 1,
        ]);

        $member->pointAccount->earnPoints(100, '测试积分');

        $product = PointProduct::create([
            'name' => '测试商品11',
            'points_price' => 100,
            'stock' => 10,
            'status' => ProductStatus::LISTED,
        ]);

        $order = $this->service->redeem($member, $product);

        // 取消订单
        $this->service->cancel($order);

        // 库存应该恢复
        $this->assertEquals(10, $product->fresh()->stock);
    }
}