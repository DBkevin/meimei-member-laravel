<?php
namespace App\Services;
use App\Models\Member;
use App\Models\SalesRep;
use App\Models\Doctor;
use App\Models\Showcase;
use App\Models\PointAccount;
use App\Models\PointTransaction;
use App\Models\PointProduct;
use App\Models\RedemptionOrder;
use App\Models\FollowUpRecord;
use App\Models\SalesTask;
use App\Enums\SalesTaskStatus;
use App\Enums\SalesTaskPriority;
use App\Enums\RedemptionOrderStatus;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    private ?Carbon $startDate = null;
    private ?Carbon $endDate = null;
    private ?int $salesRepId = null;
    private ?int $doctorId = null;
    private ?string $projectType = null;
    private ?string $followUpStatus = null;
    private ?string $pointReason = null;

    public function setDateRange(?string $start, ?string $end): self
    {
        $this->startDate = $start ? Carbon::parse($start)->startOfDay() : Carbon::now()->subDays(30)->startOfDay();
        $this->endDate = $end ? Carbon::parse($end)->endOfDay() : Carbon::now()->endOfDay();
        return $this;
    }

    public function setSalesRepId(?int $id): self
    {
        $this->salesRepId = $id;
        return $this;
    }

    public function setDoctorId(?int $id): self
    {
        $this->doctorId = $id;
        return $this;
    }

    public function setProjectType(?string $type): self
    {
        $this->projectType = $type;
        return $this;
    }

    public function setFollowUpStatus(?string $status): self
    {
        $this->followUpStatus = $status;
        return $this;
    }

    public function setPointReason(?string $reason): self
    {
        $this->pointReason = $reason;
        return $this;
    }

    private function dateQuery($query, string $column = 'created_at')
    {
        if ($this->startDate) $query->where($column, '>=', $this->startDate);
        if ($this->endDate) $query->where($column, '<=', $this->endDate);
        return $query;
    }

    public function getMemberStats(): array
    {
        $query = Member::query();
        $this->dateQuery($query);
        $newMembers = (clone $query)->count();
        $totalMembers = Member::count();
        $withPoints = PointAccount::where('balance', '>', 0)->count();
        $withOrders = RedemptionOrder::distinct()->count('member_id');
        $withFollowUps = FollowUpRecord::distinct()->count('member_id');
        $withShowcases = Showcase::distinct()->count('member_id');
        $bySales = SalesRep::withCount('members')->get()->map(fn($s) => ['name' => $s->name, 'count' => $s->members_count]);
        return [
            'total' => $totalMembers,
            'new_in_period' => $newMembers,
            'with_points' => $withPoints,
            'with_orders' => $withOrders,
            'with_follow_ups' => $withFollowUps,
            'with_showcases' => $withShowcases,
            'by_sales' => $bySales
        ];
    }

    public function getPointStats(): array
    {
        $query = PointTransaction::query();
        $this->dateQuery($query);
        $earned = (clone $query)->where('type', 'earn')->sum('points');
        $spent = (clone $query)->where('type', 'spend')->sum('points');
        $refund = (clone $query)->where('type', 'refund')->sum('points');
        $totalBalance = PointAccount::sum('balance');
        $totalFrozen = PointAccount::sum('frozen_points');
        $byReason = PointTransaction::select('reason', DB::raw('sum(points) as total'))->where('type', 'earn')->groupBy('reason')->pluck('total', 'reason')->toArray();
        $topMembers = PointTransaction::select('member_id', DB::raw('sum(points) as total'))->groupBy('member_id')->orderByDesc('total')->limit(10)->with('member')->get()->map(fn($t) => ['name' => $t->member?->name ?? '-', 'phone' => $t->member?->phone ?? '-', 'total' => $t->total]);
        $recent = PointTransaction::with('member')->orderByDesc('created_at')->limit(20)->get()->map(fn($t) => ['id' => $t->id, 'member_name' => $t->member?->name ?? '-', 'member_phone' => $t->member?->phone ?? '-', 'type' => $t->type, 'points' => $t->points, 'reason' => $t->reason ?? '-', 'created_at' => $t->created_at->format('Y-m-d H:i')]);
        return [
            'total_earned' => $earned,
            'total_spent' => $spent,
            'total_refund' => $refund,
            'total_balance' => $totalBalance,
            'total_frozen' => $totalFrozen,
            'by_reason' => $byReason,
            'top_members' => $topMembers,
            'recent' => $recent
        ];
    }

    public function getRedemptionStats(): array
    {
        $query = RedemptionOrder::query();
        $this->dateQuery($query);
        $total = (clone $query)->count();
        $completed = (clone $query)->where('status', RedemptionOrderStatus::VERIFIED)->count();
        $cancelled = (clone $query)->where('status', RedemptionOrderStatus::CANCELLED)->count();
        $rejected = (clone $query)->where('status', RedemptionOrderStatus::REJECTED)->count();
        $rate = $total > 0 ? round($completed / $total * 100, 1) : 0;
        $topProducts = RedemptionOrder::select('point_product_id', DB::raw('count(*) as count'))->groupBy('point_product_id')->orderByDesc('count')->limit(10)->get()->map(fn($o) => ['name' => PointProduct::find($o->point_product_id)?->name ?? '-', 'count' => $o->count]);
        $lowStock = PointProduct::where('stock', '>', 0)->where('stock', '<=', 5)->get()->map(fn($p) => ['name' => $p->name, 'stock' => $p->stock]);
        $recent = RedemptionOrder::with(['member', 'pointProduct'])->orderByDesc('created_at')->limit(20)->get()->map(fn($o) => ['id' => $o->id, 'order_no' => $o->order_no, 'member_name' => $o->member?->name ?? '-', 'member_phone' => $o->member?->phone ?? '-', 'product_name' => $o->product_name, 'total_points' => $o->total_points, 'status' => $o->status->value, 'status_label' => $o->status->label(), 'created_at' => $o->created_at->format('Y-m-d H:i')]);
        return [
            'total' => $total,
            'completed' => $completed,
            'cancelled' => $cancelled,
            'rejected' => $rejected,
            'completion_rate' => $rate,
            'top_products' => $topProducts,
            'low_stock' => $lowStock,
            'recent' => $recent
        ];
    }

    public function getFollowUpStats(): array
    {
        $query = FollowUpRecord::query();
        $this->dateQuery($query);
        $total = (clone $query)->count();
        $todayPending = FollowUpRecord::where('status', 'pending')->whereDate('next_follow_up_at', Carbon::today())->count();
        $overdue = FollowUpRecord::where('status', 'pending')->where('next_follow_up_at', '<', Carbon::now())->count();
        $highIntention = (clone $query)->where('intention_level', 'high')->count();
        $deals = (clone $query)->where('status', 'completed')->count();
        $bySales = SalesRep::withCount('followUpRecords')->get()->map(fn($s) => ['name' => $s->name, 'count' => $s->follow_up_records_count]);
        $highIntentionBySales = SalesRep::withCount(['followUpRecords' => fn($q) => $q->where('intention_level', 'high')])->get()->map(fn($s) => ['name' => $s->name, 'count' => $s->follow_up_records_count]);
        $recent = FollowUpRecord::with(['member', 'salesRep'])->orderByDesc('created_at')->limit(20)->get()->map(fn($f) => ['id' => $f->id, 'member_name' => $f->member?->name ?? '-', 'member_phone' => $f->member?->phone ?? '-', 'sales_rep_name' => $f->salesRep?->name ?? '-', 'type' => $f->type, 'intention_level' => $f->intention_level ?? '-', 'status' => $f->status, 'next_follow_at' => $f->next_follow_up_at?->format('Y-m-d'), 'created_at' => $f->created_at->format('Y-m-d H:i')]);
        return [
            'total' => $total,
            'today_pending' => $todayPending,
            'overdue' => $overdue,
            'high_intention' => $highIntention,
            'deals' => $deals,
            'by_sales' => $bySales,
            'high_intention_by_sales' => $highIntentionBySales,
            'recent' => $recent
        ];
    }

    public function getShowcaseStats(): array
    {
        $query = Showcase::query();
        $this->dateQuery($query);
        $total = (clone $query)->count();
        $authorized = (clone $query)->where('authorization_status', 'authorized')->count();
        $wechatUsable = (clone $query)->where('usable_for_wechat', true)->count();
        $articleUsable = (clone $query)->where('usable_for_article', true)->count();
        $xiaohongshuUsable = (clone $query)->where('usable_for_xiaohongshu', true)->count();
        $featured = (clone $query)->where('is_featured', true)->count();
        $byProject = Showcase::select('project_type', DB::raw('count(*) as count'))->whereNotNull('project_type')->groupBy('project_type')->pluck('count', 'project_type')->toArray();
        $byDoctor = Doctor::withCount('showcases')->get()->map(fn($d) => ['name' => $d->name, 'count' => $d->showcases_count]);
        $recent = Showcase::with(['doctor', 'member'])->orderByDesc('created_at')->limit(20)->get()->map(fn($s) => ['id' => $s->id, 'title' => $s->title, 'project_name' => $s->project_name ?? '-', 'doctor_name' => $s->doctor?->name ?? '-', 'member_name' => $s->member?->name ?? '-', 'authorization_status' => $s->authorization_status, 'is_featured' => $s->is_featured, 'created_at' => $s->created_at->format('Y-m-d')]);
        return [
            'total' => $total,
            'authorized' => $authorized,
            'wechat_usable' => $wechatUsable,
            'article_usable' => $articleUsable,
            'xiaohongshu_usable' => $xiaohongshuUsable,
            'featured' => $featured,
            'by_project' => $byProject,
            'by_doctor' => $byDoctor,
            'recent' => $recent
        ];
    }

    public function getTaskStats(): array
    {
        $query = SalesTask::query();
        $this->dateQuery($query);
        $total = (clone $query)->count();
        $pending = (clone $query)->whereIn('status', [SalesTaskStatus::PENDING->value, SalesTaskStatus::IN_PROGRESS->value])->count();
        $completed = (clone $query)->where('status', SalesTaskStatus::COMPLETED->value)->count();
        $overdue = (clone $query)->whereIn('status', [SalesTaskStatus::PENDING->value, SalesTaskStatus::IN_PROGRESS->value, SalesTaskStatus::OVERDUE->value])->where('due_at', '<', now())->count();
        $highPriority = (clone $query)->where('priority', SalesTaskPriority::HIGH->value)->count();
        $bySales = SalesRep::withCount('salesTasks')->get()->map(fn($s) => ['name' => $s->name, 'count' => $s->sales_tasks_count]);
        $overdueBySales = SalesRep::withCount(['salesTasks' => fn($q) => $q->whereIn('status', [SalesTaskStatus::PENDING->value, SalesTaskStatus::IN_PROGRESS->value, SalesTaskStatus::OVERDUE->value])->where('due_at', '<', now())])->get()->map(fn($s) => ['name' => $s->name, 'count' => $s->sales_tasks_count]);
        $recent = SalesTask::with(['member', 'salesRep'])->orderByDesc('created_at')->limit(20)->get()->map(fn($t) => ['id' => $t->id, 'title' => $t->title, 'member_name' => $t->member?->name ?? '-', 'sales_rep_name' => $t->salesRep?->name ?? '-', 'type' => $t->type->value, 'priority' => $t->priority->value, 'status' => $t->status->value, 'due_at' => $t->due_at?->format('Y-m-d'), 'created_at' => $t->created_at->format('Y-m-d H:i')]);
        return ['total' => $total, 'pending' => $pending, 'completed' => $completed, 'overdue' => $overdue, 'high_priority' => $highPriority, 'by_sales' => $bySales, 'overdue_by_sales' => $overdueBySales, 'recent' => $recent];
    }

    public function exportToCsv(array $data, string $filename): string
    {
        $path = storage_path("app/exports/{$filename}.csv");
        $handle = fopen($path, 'w');
        if (!empty($data)) {
            fputcsv($handle, array_keys($data[0]));
            foreach ($data as $row) {
                fputcsv($handle, array_values($row));
            }
        }
        fclose($handle);
        return $path;
    }
}