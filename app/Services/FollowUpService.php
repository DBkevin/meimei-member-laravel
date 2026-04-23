<?php
namespace App\Services;
use App\Models\FollowUpRecord;
use App\Models\Member;
use App\Models\SalesRep;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class FollowUpService
{
    public function createFollowUp(array $data): FollowUpRecord
    {
        return DB::transaction(function () use ($data) {
            $followUp = FollowUpRecord::create([
                'member_id' => $data['member_id'],
                'sales_rep_id' => $data['sales_rep_id'] ?? Auth::id(),
                'type' => $data['type'],
                'channel' => $data['channel'],
                'content' => $data['content'],
                'intention_level' => $data['intention_level'] ?? null,
                'next_follow_up_at' => $data['next_follow_up_at'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'result' => $data['result'] ?? null,
                'created_by' => Auth::id(),
            ]);
            return $followUp;
        });
    }
    public function completeFollowUp(FollowUpRecord $followUp, ?string $result = null): FollowUpRecord
    {
        $followUp->update(['status' => 'completed', 'result' => $result]);
        return $followUp->refresh();
    }
    public function markNeedFollow(FollowUpRecord $followUp, ?string $nextFollowAt = null): FollowUpRecord
    {
        $followUp->update(['status' => 'need_follow', 'next_follow_up_at' => $nextFollowAt]);
        return $followUp->refresh();
    }
    public function markDeal(FollowUpRecord $followUp, ?string $result = null): FollowUpRecord
    {
        $followUp->update(['status' => 'deal', 'result' => $result, 'intention_level' => 'deal']);
        return $followUp->refresh();
    }
    public function markInvalid(FollowUpRecord $followUp, ?string $result = null): FollowUpRecord
    {
        $followUp->update(['status' => 'invalid', 'result' => $result, 'intention_level' => 'none']);
        return $followUp->refresh();
    }
    public function getTodayPendingCount(): int
    {
        return FollowUpRecord::where('status', 'pending')
            ->whereDate('next_follow_up_at', '<=', now())
            ->count();
    }
    public function getOverdueCount(): int
    {
        return FollowUpRecord::whereIn('status', ['pending', 'need_follow'])
            ->whereDate('next_follow_up_at', '<', now()->startOfDay())
            ->count();
    }
    public function getMonthFollowUpCount(): int
    {
        return FollowUpRecord::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }
    public function getHighIntentionCount(): int
    {
        return FollowUpRecord::where('intention_level', 'high')
            ->where('status', '!=', 'invalid')
            ->distinct('member_id')
            ->count('member_id');
    }
    public function getMonthDealCount(): int
    {
        return FollowUpRecord::where('status', 'deal')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }
    public function getSalesFollowUpCounts(): array
    {
        return FollowUpRecord::select('sales_rep_id', DB::raw('count(*) as count'))
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->groupBy('sales_rep_id')
            ->pluck('count', 'sales_rep_id')
            ->toArray();
    }
    public function getRecentFollowUps(int $limit = 10)
    {
        return FollowUpRecord::with(['member', 'salesRep'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
