<?php
namespace Tests\Feature;
use App\Models\Member;
use App\Models\SalesRep;
use App\Models\Doctor;
use App\Models\Showcase;
use App\Models\PointAccount;
use App\Models\PointTransaction;
use App\Models\PointProduct;
use App\Models\RedemptionOrder;
use App\Models\FollowUpRecord;
use App\Services\ReportService;
use App\Enums\RedemptionOrderStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_service_returns_member_stats(): void
    {
        $salesRep = SalesRep::create(['name' => '测试销售', 'status' => 1]);
        Member::create(['name' => '会员1', 'phone' => '13800000001', 'sales_rep_id' => $salesRep->id]);
        Member::create(['name' => '会员2', 'phone' => '13800000002', 'sales_rep_id' => $salesRep->id]);
        $service = app(ReportService::class);
        $stats = $service->getMemberStats();
        $this->assertEquals(2, $stats['total']);
        $this->assertCount(1, $stats['by_sales']);
    }

    public function test_report_service_returns_point_stats(): void
    {
        $member = Member::create(['name' => '测试会员', 'phone' => '13800000003']);
        PointTransaction::create(['member_id' => $member->id, 'point_account_id' => 1, 'type' => 'earn', 'points' => 500, 'before_balance' => 0, 'after_balance' => 500, 'reason' => '注册奖励']);
        PointTransaction::create(['member_id' => $member->id, 'point_account_id' => 1, 'type' => 'spend', 'points' => 200, 'before_balance' => 500, 'after_balance' => 300, 'reason' => '兑换商品']);
        $service = app(ReportService::class);
        $stats = $service->getPointStats();
        $this->assertEquals(500, $stats['total_earned']);
        $this->assertEquals(200, $stats['total_spent']);
    }

    public function test_report_service_returns_redemption_stats(): void
    {
        $member = Member::create(['name' => '测试会员', 'phone' => '13800000004']);
        $product = PointProduct::create(['name' => '测试商品', 'points_price' => 100, 'stock' => 10, 'status' => 1]);
        RedemptionOrder::create(['order_no' => 'ORD001', 'member_id' => $member->id, 'point_product_id' => $product->id, 'product_name' => '测试商品', 'quantity' => 1, 'unit_points' => 100, 'total_points' => 100, 'status' => RedemptionOrderStatus::VERIFIED]);
        RedemptionOrder::create(['order_no' => 'ORD002', 'member_id' => $member->id, 'point_product_id' => $product->id, 'product_name' => '测试商品', 'quantity' => 1, 'unit_points' => 100, 'total_points' => 100, 'status' => RedemptionOrderStatus::PENDING]);
        $service = app(ReportService::class);
        $stats = $service->getRedemptionStats();
        $this->assertEquals(2, $stats['total']);
        $this->assertEquals(1, $stats['completed']);
        $this->assertEquals(50.0, $stats['completion_rate']);
    }

    public function test_report_service_returns_follow_up_stats(): void
    {
        $salesRep = SalesRep::create(['name' => '测试销售', 'status' => 1]);
        $member = Member::create(['name' => '测试会员', 'phone' => '13800000005']);
        FollowUpRecord::create(['member_id' => $member->id, 'sales_rep_id' => $salesRep->id, 'type' => 'call', 'channel' => 'phone', 'intention_level' => 'high', 'status' => 'pending', 'content' => '测试内容', 'next_follow_up_at' => now()->addDay()]);
        FollowUpRecord::create(['member_id' => $member->id, 'sales_rep_id' => $salesRep->id, 'type' => 'visit', 'channel' => 'offline', 'intention_level' => 'medium', 'status' => 'completed', 'content' => '测试内容']);
        $service = app(ReportService::class);
        $stats = $service->getFollowUpStats();
        $this->assertEquals(2, $stats['total']);
        $this->assertEquals(1, $stats['high_intention']);
        $this->assertEquals(1, $stats['deals']);
    }

    public function test_report_service_returns_showcase_stats(): void
    {
        $doctor = Doctor::create(['name' => '测试医生', 'status' => 1]);
        Showcase::create(['doctor_id' => $doctor->id, 'title' => '案例1', 'authorization_status' => 'authorized', 'usable_for_wechat' => true, 'status' => 1]);
        Showcase::create(['doctor_id' => $doctor->id, 'title' => '案例2', 'authorization_status' => 'pending', 'usable_for_wechat' => false, 'status' => 1]);
        $service = app(ReportService::class);
        $stats = $service->getShowcaseStats();
        $this->assertEquals(2, $stats['total']);
        $this->assertEquals(1, $stats['authorized']);
        $this->assertEquals(1, $stats['wechat_usable']);
    }

    public function test_date_filter_works(): void
    {
        Member::create(['name' => '测试会员', 'phone' => '13800000006']);
        $service = app(ReportService::class)->setDateRange(now()->subDays(60)->format('Y-m-d'), now()->subDays(30)->format('Y-m-d'));
        $stats = $service->getMemberStats();
        $this->assertEquals(0, $stats['new_in_period']);
    }

    public function test_export_to_csv(): void
    {
        $data = [['name' => '测试1', 'phone' => '1380000001'], ['name' => '测试2', 'phone' => '1380000002']];
        $service = app(ReportService::class);
        $path = $service->exportToCsv($data, 'test_export');
        $this->assertFileExists($path);
        unlink($path);
    }
}
