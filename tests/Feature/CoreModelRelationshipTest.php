<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\Member;
use App\Models\PointAccount;
use App\Models\PointProduct;
use App\Models\PointTransaction;
use App\Models\RedemptionOrder;
use App\Models\SalesRep;
use App\Models\Showcase;
use App\Models\VerificationRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: 可以创建 SalesRep
     */
    public function test_can_create_sales_rep()
    {
        $salesRep = SalesRep::create([
            'name' => '张三',
            'phone' => '13800138000',
            'status' => 1,
        ]);

        $this->assertDatabaseHas('sales_reps', [
            'id' => $salesRep->id,
            'name' => '张三',
            'phone' => '13800138000',
        ]);
    }

    /**
     * Test: 可以创建 Member 并关联 SalesRep
     */
    public function test_can_create_member_with_sales_rep()
    {
        $salesRep = SalesRep::create([
            'name' => '李四',
            'status' => 1,
        ]);

        $member = Member::create([
            'name' => '王五',
            'phone' => '13800138001',
            'sales_rep_id' => $salesRep->id,
            'status' => 1,
        ]);

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'name' => '王五',
            'phone' => '13800138001',
            'sales_rep_id' => $salesRep->id,
        ]);

        $this->assertEquals($salesRep->id, $member->salesRep->id);
    }

    /**
     * Test: 创建 Member 时自动创建 PointAccount
     */
    public function test_point_account_auto_created_when_member_created()
    {
        $member = Member::create([
            'name' => '赵六',
            'phone' => '13800138002',
            'status' => 1,
        ]);

        $this->assertTrue($member->pointAccount()->exists());
        $this->assertDatabaseHas('point_accounts', [
            'member_id' => $member->id,
            'balance' => 0,
            'total_earned' => 0,
            'total_spent' => 0,
            'frozen_points' => 0,
        ]);
    }

    /**
     * Test: 可以创建 Doctor
     */
    public function test_can_create_doctor()
    {
        $doctor = Doctor::create([
            'name' => '医生1',
            'title' => '主任医生',
            'status' => 1,
        ]);

        $this->assertDatabaseHas('doctors', [
            'id' => $doctor->id,
            'name' => '医生1',
            'title' => '主任医生',
        ]);
    }

    /**
     * Test: 可以创建 Showcase 并关联 Doctor
     */
    public function test_can_create_showcase_with_doctor()
    {
        $doctor = Doctor::create([
            'name' => '医生2',
            'status' => 1,
        ]);

        $showcase = Showcase::create([
            'doctor_id' => $doctor->id,
            'title' => '案例1',
            'media_type' => 'image',
            'status' => 1,
        ]);

        $this->assertDatabaseHas('showcases', [
            'id' => $showcase->id,
            'doctor_id' => $doctor->id,
            'title' => '案例1',
        ]);

        $this->assertEquals($doctor->id, $showcase->doctor->id);
    }

    /**
     * Test: 可以创建 PointProduct
     */
    public function test_can_create_point_product()
    {
        $product = PointProduct::create([
            'name' => '商品1',
            'points_price' => 100,
            'stock' => 50,
            'status' => 1,
        ]);

        $this->assertDatabaseHas('point_products', [
            'id' => $product->id,
            'name' => '商品1',
            'points_price' => 100,
            'stock' => 50,
        ]);
    }

    /**
     * Test: 可以创建 RedemptionOrder 并关联 Member 和 PointProduct
     */
    public function test_can_create_redemption_order()
    {
        $member = Member::create([
            'name' => '孙七',
            'phone' => '13800138003',
            'status' => 1,
        ]);

        $product = PointProduct::create([
            'name' => '商品2',
            'points_price' => 200,
            'status' => 1,
        ]);

        $order = RedemptionOrder::create([
            'order_no' => 'ORD20260422001',
            'member_id' => $member->id,
            'point_product_id' => $product->id,
            'product_name' => '商品2',
            'quantity' => 1,
            'unit_points' => 200,
            'total_points' => 200,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('redemption_orders', [
            'id' => $order->id,
            'order_no' => 'ORD20260422001',
            'member_id' => $member->id,
            'point_product_id' => $product->id,
        ]);

        $this->assertEquals($member->id, $order->member->id);
        $this->assertEquals($product->id, $order->pointProduct->id);
    }

    /**
     * Test: 可以创建 VerificationRecord 并关联 RedemptionOrder
     */
    public function test_can_create_verification_record()
    {
        $member = Member::create([
            'name' => '周八',
            'phone' => '13800138004',
            'status' => 1,
        ]);

        $product = PointProduct::create([
            'name' => '商品3',
            'points_price' => 300,
            'status' => 1,
        ]);

        $order = RedemptionOrder::create([
            'order_no' => 'ORD20260422002',
            'member_id' => $member->id,
            'point_product_id' => $product->id,
            'product_name' => '商品3',
            'quantity' => 1,
            'unit_points' => 300,
            'total_points' => 300,
            'status' => 'pending',
        ]);

        $record = VerificationRecord::create([
            'redemption_order_id' => $order->id,
            'member_id' => $member->id,
            'verified_at' => now(),
        ]);

        $this->assertDatabaseHas('verification_records', [
            'id' => $record->id,
            'redemption_order_id' => $order->id,
            'member_id' => $member->id,
        ]);

        $this->assertEquals($order->id, $record->redemptionOrder->id);
    }

    /**
     * Test: 手机号唯一约束有效
     */
    public function test_member_phone_unique_constraint()
    {
        Member::create([
            'name' => '吴九',
            'phone' => '13800138005',
            'status' => 1,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Member::create([
            'name' => '郑十',
            'phone' => '13800138005',
            'status' => 1,
        ]);
    }

    /**
     * Test: soft delete 基础可用
     */
    public function test_soft_delete_works()
    {
        $member = Member::create([
            'name' => '刘十一',
            'phone' => '13800138006',
            'status' => 1,
        ]);

        $memberId = $member->id;

        $member->delete();

        // 软删除后，应该在带有 withTrashed 的查询中找到
        $this->assertTrue(Member::withTrashed()->where('id', $memberId)->exists());

        // 正常查询中应该找不到
        $this->assertFalse(Member::where('id', $memberId)->exists());
    }

    /**
     * Test: PointTransaction 可以创建流水
     */
    public function test_can_create_point_transaction()
    {
        $member = Member::create([
            'name' => '陈十二',
            'phone' => '13800138007',
            'status' => 1,
        ]);

        $pointAccount = $member->pointAccount;

        $transaction = PointTransaction::create([
            'member_id' => $member->id,
            'point_account_id' => $pointAccount->id,
            'type' => 'earn',
            'points' => 100,
            'before_balance' => 0,
            'after_balance' => 100,
            'operator_name' => '系统',
            'remark' => '新会员赠送',
        ]);

        $this->assertDatabaseHas('point_transactions', [
            'id' => $transaction->id,
            'member_id' => $member->id,
            'point_account_id' => $pointAccount->id,
            'type' => 'earn',
            'points' => 100,
        ]);
    }
}
