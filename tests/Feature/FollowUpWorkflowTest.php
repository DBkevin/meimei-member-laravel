<?php
namespace Tests\Feature;
use App\Models\Member;
use App\Models\SalesRep;
use App\Models\FollowUpRecord;
use App\Services\FollowUpService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FollowUpWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->followUpService = app(FollowUpService::class);
    }

    public function test_can_create_follow_up_record(): void
    {
        $member = Member::create(['name' => '测试会员', 'phone' => '13800000001']);
        $salesRep = SalesRep::create(['name' => '测试销售', 'phone' => '13900000001']);
        
        $followUp = FollowUpRecord::create([
            'member_id' => $member->id,
            'sales_rep_id' => $salesRep->id,
            'type' => 'aftercare',
            'channel' => 'phone',
            'content' => '术后回访测试',
            'intention_level' => 'high',
            'status' => 'pending',
            'next_follow_up_at' => now()->addDays(3),
        ]);

        $this->assertDatabaseHas('follow_up_records', [
            'id' => $followUp->id,
            'member_id' => $member->id,
            'sales_rep_id' => $salesRep->id,
        ]);
    }

    public function test_follow_up_belongs_to_member(): void
    {
        $member = Member::create(['name' => '测试会员', 'phone' => '13800000002']);
        $followUp = FollowUpRecord::create([
            'member_id' => $member->id,
            'type' => 'aftercare',
            'channel' => 'phone',
            'content' => '测试',
            'status' => 'pending'
        ]);
        $this->assertEquals($member->id, $followUp->member->id);
    }

    public function test_follow_up_belongs_to_sales_rep(): void
    {
        $salesRep = SalesRep::create(['name' => '测试销售', 'phone' => '13900000002']);
        $member = Member::create(['name' => '测试', 'phone' => '13700000001']);
        $followUp = FollowUpRecord::create([
            'sales_rep_id' => $salesRep->id,
            'member_id' => $member->id,
            'type' => 'aftercare',
            'channel' => 'phone',
            'content' => '测试',
            'status' => 'pending'
        ]);
        $this->assertEquals($salesRep->id, $followUp->salesRep->id);
    }

    public function test_can_mark_as_completed(): void
    {
        $member = Member::create(['name' => '测试', 'phone' => '13600000001']);
        $followUp = FollowUpRecord::create([
            'member_id' => $member->id,
            'type' => 'aftercare',
            'channel' => 'phone',
            'content' => '测试',
            'status' => 'pending'
        ]);
        $result = $this->followUpService->completeFollowUp($followUp, '已跟进完成');
        $this->assertEquals('completed', $result->status);
        $this->assertEquals('已跟进完成', $result->result);
    }

    public function test_can_mark_as_need_follow(): void
    {
        $member = Member::create(['name' => '测试', 'phone' => '13600000002']);
        $followUp = FollowUpRecord::create([
            'member_id' => $member->id,
            'type' => 'aftercare',
            'channel' => 'phone',
            'content' => '测试',
            'status' => 'pending'
        ]);
        $nextFollowAt = now()->addDays(5);
        $result = $this->followUpService->markNeedFollow($followUp, $nextFollowAt);
        $this->assertEquals('need_follow', $result->status);
    }

    public function test_can_mark_as_deal(): void
    {
        $member = Member::create(['name' => '测试', 'phone' => '13600000003']);
        $followUp = FollowUpRecord::create([
            'member_id' => $member->id,
            'type' => 'aftercare',
            'channel' => 'phone',
            'content' => '测试',
            'status' => 'pending'
        ]);
        $result = $this->followUpService->markDeal($followUp, '客户已成交');
        $this->assertEquals('deal', $result->status);
        $this->assertEquals('deal', $result->intention_level);
    }

    public function test_can_mark_as_invalid(): void
    {
        $member = Member::create(['name' => '测试', 'phone' => '13600000004']);
        $followUp = FollowUpRecord::create([
            'member_id' => $member->id,
            'type' => 'aftercare',
            'channel' => 'phone',
            'content' => '测试',
            'status' => 'pending'
        ]);
        $result = $this->followUpService->markInvalid($followUp, '客户明确拒绝');
        $this->assertEquals('invalid', $result->status);
        $this->assertEquals('none', $result->intention_level);
    }

    public function test_member_detail_can_query_follow_ups(): void
    {
        $member = Member::create(['name' => '测试', 'phone' => '13600000007']);
        FollowUpRecord::create(['member_id' => $member->id, 'type' => 'aftercare', 'channel' => 'phone', 'content' => '测试1', 'status' => 'pending']);
        FollowUpRecord::create(['member_id' => $member->id, 'type' => 'repurchase', 'channel' => 'wechat', 'content' => '测试2', 'status' => 'completed']);
        FollowUpRecord::create(['member_id' => $member->id, 'type' => 'campaign_invite', 'channel' => 'phone', 'content' => '测试3', 'status' => 'need_follow']);
        $this->assertEquals(3, $member->followUpRecords()->count());
    }
}
