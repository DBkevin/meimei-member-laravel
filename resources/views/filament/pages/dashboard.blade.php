<?php
use Filament\Support\Facades\FilamentColor;
?>

<x-filament-panels::page>
    <div class="space-y-6">
        <!-- 核心数据卡片 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-filament::card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600">{{ $dashboardData['core']['member_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">会员总数</div>
                    <div class="text-xs text-gray-400 mt-1">本月新增: {{ $dashboardData['core']['member_this_month'] ?? 0 }}</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-success-600">{{ $dashboardData['core']['sales_rep_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">销售人数</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-info-600">{{ $dashboardData['core']['doctor_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">医生人数</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-3xl font-bold text-warning-600">{{ $dashboardData['core']['showcase_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">案例数量</div>
                </div>
            </x-filament::card>
        </div>

        <!-- 积分运营数据 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600">{{ number_format($dashboardData['points']['total_earned'] ?? 0) }}</div>
                    <div class="text-sm text-gray-500 mt-1">累计发放积分</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-danger-600">{{ number_format($dashboardData['points']['total_spent'] ?? 0) }}</div>
                    <div class="text-sm text-gray-500 mt-1">累计消耗积分</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-warning-600">{{ number_format($dashboardData['points']['total_refund'] ?? 0) }}</div>
                    <div class="text-sm text-gray-500 mt-1">累计退还积分</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-success-600">{{ number_format($dashboardData['points']['total_balance'] ?? 0) }}</div>
                    <div class="text-sm text-gray-500 mt-1">当前可用积分</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ number_format($dashboardData['points']['total_frozen'] ?? 0) }}</div>
                    <div class="text-sm text-gray-500 mt-1">当前冻结积分</div>
                </div>
            </x-filament::card>
        </div>

        <!-- 兑换订单数据 -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-warning-600">{{ $dashboardData['orders']['pending_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">待处理订单</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-success-600">{{ $dashboardData['orders']['verified_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">已核销订单</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-danger-600">{{ $dashboardData['orders']['cancelled_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">已取消订单</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ $dashboardData['orders']['rejected_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">已拒绝订单</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600">{{ $dashboardData['orders']['order_this_month'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">本月兑换订单</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-info-600">{{ $dashboardData['orders']['verified_this_month'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">本月核销订单</div>
                </div>
            </x-filament::card>
        </div>

        <!-- 商品数据 -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-warning-600">{{ $dashboardData['products']['low_stock_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">低库存商品</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-danger-600">{{ $dashboardData['products']['out_of_stock_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">库存为0商品</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-600">{{ $dashboardData['products']['delisted_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">已下架商品</div>
                </div>
            </x-filament::card>

            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600">{{ $dashboardData['core']['point_account_count'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500 mt-1">积分账户数量</div>
                </div>
            </x-filament::card>
        </div>

        <!-- 热门商品和最近数据 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- 热门兑换商品 -->
            <x-filament::card>
                <div class="font-semibold text-lg mb-4">热门兑换商品 TOP 5</div>
                @if(count($dashboardData['products']['top_products']) > 0)
                    <div class="space-y-2">
                        @foreach($dashboardData['products']['top_products'] as $index => $product)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                <span class="text-gray-700">{{ $index + 1 }}. {{ $product['name'] }}</span>
                                <span class="text-sm text-gray-500">{{ $product['order_count'] }} 次兑换</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-gray-400 text-center py-4">暂无兑换数据</div>
                @endif
            </x-filament::card>

            <!-- 最近兑换订单 -->
            <x-filament::card>
                <div class="font-semibold text-lg mb-4">最近兑换订单</div>
                @if(count($dashboardData['recent_orders']) > 0)
                    <div class="space-y-2">
                        @foreach($dashboardData['recent_orders'] as $order)
                            <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                <div>
                                    <div class="text-gray-700 text-sm">{{ $order['member_name'] }} ({{ $order['member_phone'] }})</div>
                                    <div class="text-xs text-gray-400">{{ $order['product_name'] }} - {{ $order['total_points'] }} 积分</div>
                                </div>
                                <div class="text-right">
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($order['status'] === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order['status'] === 'verified') bg-green-100 text-green-800
                                        @elseif($order['status'] === 'cancelled') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $order['status_label'] }}
                                    </span>
                                    <div class="text-xs text-gray-400 mt-1">{{ $order['created_at'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-gray-400 text-center py-4">暂无订单数据</div>
                @endif
            </x-filament::card>
        </div>

        <!-- 最近积分流水 -->
        <x-filament::card>
            <div class="font-semibold text-lg mb-4">最近积分流水</div>
            @if(count($dashboardData['recent_transactions']) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-gray-500 border-b">
                                <th class="pb-2">会员</th>
                                <th class="pb-2">类型</th>
                                <th class="pb-2">积分</th>
                                <th class="pb-2">变动前</th>
                                <th class="pb-2">变动后</th>
                                <th class="pb-2">备注</th>
                                <th class="pb-2">时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dashboardData['recent_transactions'] as $transaction)
                                <tr class="border-b border-gray-100 last:border-0">
                                    <td class="py-2 text-sm">{{ $transaction['member_name'] }}</td>
                                    <td class="py-2 text-sm">
                                        @if($transaction['type'] === 'earn')
                                            <span class="text-green-600">增加</span>
                                        @elseif($transaction['type'] === 'spend')
                                            <span class="text-red-600">消耗</span>
                                        @elseif($transaction['type'] === 'refund')
                                            <span class="text-yellow-600">退还</span>
                                        @elseif($transaction['type'] === 'adjust')
                                            <span class="text-blue-600">调整</span>
                                        @else
                                            <span class="text-gray-600">{{ $transaction['type'] }}</span>
                                        @endif
                                    </td>
                                    <td class="py-2 text-sm font-medium 
                                        @if($transaction['type'] === 'earn' || $transaction['type'] === 'refund') text-green-600
                                        @elseif($transaction['type'] === 'spend') text-red-600
                                        @else text-gray-700 @endif">
                                        {{ $transaction['type'] === 'spend' ? '-' : '+' }}{{ $transaction['points'] }}
                                    </td>
                                    <td class="py-2 text-sm text-gray-500">{{ $transaction['before_balance'] }}</td>
                                    <td class="py-2 text-sm text-gray-500">{{ $transaction['after_balance'] }}</td>
                                    <td class="py-2 text-sm text-gray-500">{{ $transaction['remark'] ?? '-' }}</td>
                                    <td class="py-2 text-sm text-gray-400">{{ $transaction['created_at'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-gray-400 text-center py-4">暂无积分流水</div>
            @endif
        </x-filament::card>
    </div>
</x-filament-panels::page>