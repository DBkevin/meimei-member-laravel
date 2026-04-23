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
use App\Models\VerificationRecord;
use App\Enums\RedemptionOrderStatus;
use App\Enums\ProductStatus;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * 核心数据统计
     */
    public function getCoreStats(): array
    {
        $thisMonth = now()->startOfMonth();
        
        return [
            'member_count' => Member::count(),
            'member_this_month' => Member::where('created_at', '>=', $thisMonth)->count(),
            'sales_rep_count' => SalesRep::count(),
            'doctor_count' => Doctor::count(),
            'showcase_count' => Showcase::count(),
            'point_account_count' => PointAccount::count(),
            'pending_order_count' => RedemptionOrder::where('status', RedemptionOrderStatus::PENDING)->count(),
            'low_stock_product_count' => PointProduct::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
        ];
    }

    /**
     * 积分运营数据
     */
    public function getPointStats(): array
    {
        return [
            'total_earned' => PointTransaction::where('type', 'earn')->sum('points'),
            'total_spent' => PointTransaction::where('type', 'spend')->sum('points'),
            'total_refund' => PointTransaction::where('type', 'refund')->sum('points'),
            'total_balance' => PointAccount::sum('balance'),
            'total_frozen' => PointAccount::sum('frozen_points'),
        ];
    }

    /**
     * 兑换订单数据
     */
    public function getOrderStats(): array
    {
        $thisMonth = now()->startOfMonth();
        
        return [
            'pending_count' => RedemptionOrder::where('status', RedemptionOrderStatus::PENDING)->count(),
            'verified_count' => RedemptionOrder::where('status', RedemptionOrderStatus::VERIFIED)->count(),
            'cancelled_count' => RedemptionOrder::where('status', RedemptionOrderStatus::CANCELLED)->count(),
            'rejected_count' => RedemptionOrder::where('status', RedemptionOrderStatus::REJECTED)->count(),
            'order_this_month' => RedemptionOrder::where('created_at', '>=', $thisMonth)->count(),
            'verified_this_month' => RedemptionOrder::where('status', RedemptionOrderStatus::VERIFIED)
                ->where('completed_at', '>=', $thisMonth)->count(),
        ];
    }

    /**
     * 商品数据
     */
    public function getProductStats(): array
    {
        // 热门兑换商品 TOP 5
        $topProducts = RedemptionOrder::select('point_product_id', DB::raw('count(*) as order_count'))
            ->groupBy('point_product_id')
            ->orderByDesc('order_count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $product = PointProduct::find($item->point_product_id);
                return [
                    'name' => $product?->name ?? '未知商品',
                    'order_count' => $item->order_count,
                ];
            });

        return [
            'top_products' => $topProducts,
            'low_stock_count' => PointProduct::where('stock', '<=', 5)->where('stock', '>', 0)->count(),
            'out_of_stock_count' => PointProduct::where('stock', 0)->count(),
            'delisted_count' => PointProduct::where('status', ProductStatus::DELISTED)->count(),
        ];
    }

    /**
     * 最近积分流水
     */
    public function getRecentTransactions(int $limit = 10): array
    {
        return PointTransaction::with('member')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'member_name' => $transaction->member?->name ?? '未知会员',
                    'member_phone' => $transaction->member?->phone ?? '',
                    'type' => $transaction->type,
                    'points' => $transaction->points,
                    'before_balance' => $transaction->before_balance,
                    'after_balance' => $transaction->after_balance,
                    'remark' => $transaction->remark,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();
    }

    /**
     * 最近兑换订单
     */
    public function getRecentOrders(int $limit = 10): array
    {
        return RedemptionOrder::with(['member', 'pointProduct'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_no' => $order->order_no,
                    'member_name' => $order->member?->name ?? '未知会员',
                    'member_phone' => $order->member?->phone ?? '',
                    'product_name' => $order->product_name,
                    'total_points' => $order->total_points,
                    'status' => $order->status->value,
                    'status_label' => $order->status->label(),
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                ];
            })
            ->toArray();
    }

    /**
     * 获取完整 Dashboard 数据
     */
    public function getDashboardData(): array
    {
        return [
            'core' => $this->getCoreStats(),
            'points' => $this->getPointStats(),
            'orders' => $this->getOrderStats(),
            'products' => $this->getProductStats(),
            'recent_transactions' => $this->getRecentTransactions(),
            'recent_orders' => $this->getRecentOrders(),
            'follow_up' => $this->getFollowUpStats(),
            'recent_follow_ups' => $this->getRecentFollowUps(),
        ];
    }
}
    /**
     * 跟进数据统计
     */
    public function getFollowUpStats(): array
    {
        $followUpService = app(FollowUpService::class);
        return [
            'today_pending' => $followUpService->getTodayPendingCount(),
            'overdue' => $followUpService->getOverdueCount(),
            'month_count' => $followUpService->getMonthFollowUpCount(),
            'high_intention' => $followUpService->getHighIntentionCount(),
            'month_deal' => $followUpService->getMonthDealCount(),
            'sales_counts' => $followUpService->getSalesFollowUpCounts(),
        ];
    }

    /**
     * 最近跟进记录
     */
    public function getRecentFollowUps(int $limit = 10): array
    {
        $followUpService = app(FollowUpService::class);
        return $followUpService->getRecentFollowUps($limit)->map(fn($f) => [
            'id' => $f->id,
            'member_name' => $f->member?->name ?? '未知会员',
            'member_phone' => $f->member?->phone ?? '',
            'sales_rep_name' => $f->salesRep?->name ?? '-',
            'type' => $f->type,
            'type_label' => \App\Enums\FollowUpType::from($f->type)->label(),
            'channel' => $f->channel,
            'channel_label' => \App\Enums\FollowUpChannel::from($f->channel)->label(),
            'intention_level' => $f->intention_level,
            'intention_label' => $f->intention_level ? \App\Enums\FollowUpIntentionLevel::from($f->intention_level)->label() : '-',
            'status' => $f->status,
            'status_label' => \App\Enums\FollowUpStatus::from($f->status)->label(),
            'next_follow_at' => $f->next_follow_up_at?->format('Y-m-d H:i'),
            'created_at' => $f->created_at->format('Y-m-d H:i:s'),
        ])->toArray();
    }
