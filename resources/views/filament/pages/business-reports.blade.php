<x-filament-panels::page>
    <form wire:submit="loadStats" class="mb-6">
        <div class="grid grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">开始日期</label>
                <input type="date" wire:model="startDate" class="w-full rounded-md border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">结束日期</label>
                <input type="date" wire:model="endDate" class="w-full rounded-md border-gray-300">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">销售</label>
                <select wire:model="salesRepId" class="w-full rounded-md border-gray-300">
                    <option value="">全部</option>
                    @foreach(\App\Models\SalesRep::pluck('name', 'id') as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">医生</label>
                <select wire:model="doctorId" class="w-full rounded-md border-gray-300">
                    <option value="">全部</option>
                    @foreach(\App\Models\Doctor::pluck('name', 'id') as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex gap-2">
            <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">查询</button>
            <button type="button" wire:click="$set('startDate', '{{ now()->subDays(30)->format('Y-m-d') }}'); $set('endDate', '{{ now()->format('Y-m-d') }}'); $set('salesRepId', null); $set('doctorId', null); $set('projectType', null); loadStats();" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">重置</button>
        </div>
    </form>
    <div class="space-y-6">
        <!-- 会员分析 -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">会员分析</h2>
            <div class="grid grid-cols-4 gap-4 mb-4">
                <div class="bg-blue-50 p-4 rounded-lg"><div class="text-2xl font-bold text-blue-600">{{ $memberStats['total'] ?? 0 }}</div><div class="text-sm text-gray-600">会员总数</div></div>
                <div class="bg-green-50 p-4 rounded-lg"><div class="text-2xl font-bold text-green-600">{{ $memberStats['new_in_period'] ?? 0 }}</div><div class="text-sm text-gray-600">新增会员</div></div>
                <div class="bg-purple-50 p-4 rounded-lg"><div class="text-2xl font-bold text-purple-600">{{ $memberStats['with_points'] ?? 0 }}</div><div class="text-sm text-gray-600">有积分账户</div></div>
                <div class="bg-orange-50 p-4 rounded-lg"><div class="text-2xl font-bold text-orange-600">{{ $memberStats['with_orders'] ?? 0 }}</div><div class="text-sm text-gray-600">有兑换记录</div></div>
            </div>
            <table class="w-full text-sm">
                <thead><tr class="bg-gray-50"><th class="px-4 py-2 text-left">销售</th><th class="px-4 py-2 text-left">会员数</th></tr></thead>
                <tbody>
                    @foreach($memberStats['by_sales'] ?? [] as $item)
                        <tr class="border-t"><td class="px-4 py-2">{{ $item['name'] }}</td><td class="px-4 py-2">{{ $item['count'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- 积分分析 -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">积分分析</h2>
            <div class="grid grid-cols-5 gap-4 mb-4">
                <div class="bg-green-50 p-4 rounded-lg"><div class="text-2xl font-bold text-green-600">{{ number_format($pointStats['total_earned'] ?? 0) }}</div><div class="text-sm text-gray-600">累计发放</div></div>
                <div class="bg-red-50 p-4 rounded-lg"><div class="text-2xl font-bold text-red-600">{{ number_format($pointStats['total_spent'] ?? 0) }}</div><div class="text-sm text-gray-600">累计消耗</div></div>
                <div class="bg-blue-50 p-4 rounded-lg"><div class="text-2xl font-bold text-blue-600">{{ number_format($pointStats['total_refund'] ?? 0) }}</div><div class="text-sm text-gray-600">累计退还</div></div>
                <div class="bg-purple-50 p-4 rounded-lg"><div class="text-2xl font-bold text-purple-600">{{ number_format($pointStats['total_balance'] ?? 0) }}</div><div class="text-sm text-gray-600">当前可用</div></div>
                <div class="bg-orange-50 p-4 rounded-lg"><div class="text-2xl font-bold text-orange-600">{{ number_format($pointStats['total_frozen'] ?? 0) }}</div><div class="text-sm text-gray-600">冻结积分</div></div>
            </div>
            <table class="w-full text-sm">
                <thead><tr class="bg-gray-50"><th class="px-4 py-2 text-left">会员</th><th class="px-4 py-2 text-left">手机号</th><th class="px-4 py-2 text-right">积分变化</th></tr></thead>
                <tbody>
                    @foreach($pointStats['top_members'] ?? [] as $item)
                        <tr class="border-t"><td class="px-4 py-2">{{ $item['name'] }}</td><td class="px-4 py-2">{{ $item['phone'] }}</td><td class="px-4 py-2 text-right">{{ number_format($item['total']) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- 兑换分析 -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">兑换分析</h2>
            <div class="grid grid-cols-5 gap-4 mb-4">
                <div class="bg-blue-50 p-4 rounded-lg"><div class="text-2xl font-bold text-blue-600">{{ $redemptionStats['total'] ?? 0 }}</div><div class="text-sm text-gray-600">订单总数</div></div>
                <div class="bg-green-50 p-4 rounded-lg"><div class="text-2xl font-bold text-green-600">{{ $redemptionStats['completed'] ?? 0 }}</div><div class="text-sm text-gray-600">已核销</div></div>
                <div class="bg-red-50 p-4 rounded-lg"><div class="text-2xl font-bold text-red-600">{{ $redemptionStats['cancelled'] ?? 0 }}</div><div class="text-sm text-gray-600">已取消</div></div>
                <div class="bg-orange-50 p-4 rounded-lg"><div class="text-2xl font-bold text-orange-600">{{ $redemptionStats['rejected'] ?? 0 }}</div><div class="text-sm text-gray-600">已拒绝</div></div>
                <div class="bg-purple-50 p-4 rounded-lg"><div class="text-2xl font-bold text-purple-600">{{ $redemptionStats['completion_rate'] ?? 0 }}%</div><div class="text-sm text-gray-600">核销率</div></div>
            </div>
            <table class="w-full text-sm">
                <thead><tr class="bg-gray-50"><th class="px-4 py-2 text-left">商品</th><th class="px-4 py-2 text-right">兑换次数</th></tr></thead>
                <tbody>
                    @foreach($redemptionStats['top_products'] ?? [] as $item)
                        <tr class="border-t"><td class="px-4 py-2">{{ $item['name'] }}</td><td class="px-4 py-2 text-right">{{ $item['count'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- 跟进分析 -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">跟进分析</h2>
            <div class="grid grid-cols-5 gap-4 mb-4">
                <div class="bg-blue-50 p-4 rounded-lg"><div class="text-2xl font-bold text-blue-600">{{ $followUpStats['total'] ?? 0 }}</div><div class="text-sm text-gray-600">跟进总次数</div></div>
                <div class="bg-yellow-50 p-4 rounded-lg"><div class="text-2xl font-bold text-yellow-600">{{ $followUpStats['today_pending'] ?? 0 }}</div><div class="text-sm text-gray-600">今日待跟进</div></div>
                <div class="bg-red-50 p-4 rounded-lg"><div class="text-2xl font-bold text-red-600">{{ $followUpStats['overdue'] ?? 0 }}</div><div class="text-sm text-gray-600">逾期未跟进</div></div>
                <div class="bg-purple-50 p-4 rounded-lg"><div class="text-2xl font-bold text-purple-600">{{ $followUpStats['high_intention'] ?? 0 }}</div><div class="text-sm text-gray-600">高意向客户</div></div>
                <div class="bg-green-50 p-4 rounded-lg"><div class="text-2xl font-bold text-green-600">{{ $followUpStats['deals'] ?? 0 }}</div><div class="text-sm text-gray-600">已成交</div></div>
            </div>
            <table class="w-full text-sm">
                <thead><tr class="bg-gray-50"><th class="px-4 py-2 text-left">销售</th><th class="px-4 py-2 text-right">跟进次数</th><th class="px-4 py-2 text-right">高意向客户</th></tr></thead>
                <tbody>
                    @foreach($followUpStats['by_sales'] ?? [] as $i => $item)
                        <tr class="border-t"><td class="px-4 py-2">{{ $item['name'] }}</td><td class="px-4 py-2 text-right">{{ $item['count'] }}</td><td class="px-4 py-2 text-right">{{ $followUpStats['high_intention_by_sales'][$i]['count'] ?? 0 }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- 案例素材分析 -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold mb-4">案例素材分析</h2>
            <div class="grid grid-cols-5 gap-4 mb-4">
                <div class="bg-blue-50 p-4 rounded-lg"><div class="text-2xl font-bold text-blue-600">{{ $showcaseStats['total'] ?? 0 }}</div><div class="text-sm text-gray-600">案例总数</div></div>
                <div class="bg-green-50 p-4 rounded-lg"><div class="text-2xl font-bold text-green-600">{{ $showcaseStats['authorized'] ?? 0 }}</div><div class="text-sm text-gray-600">已授权</div></div>
                <div class="bg-purple-50 p-4 rounded-lg"><div class="text-2xl font-bold text-purple-600">{{ $showcaseStats['wechat_usable'] ?? 0 }}</div><div class="text-sm text-gray-600">朋友圈可用</div></div>
                <div class="bg-orange-50 p-4 rounded-lg"><div class="text-2xl font-bold text-orange-600">{{ $showcaseStats['article_usable'] ?? 0 }}</div><div class="text-sm text-gray-600">公众号可用</div></div>
                <div class="bg-pink-50 p-4 rounded-lg"><div class="text-2xl font-bold text-pink-600">{{ $showcaseStats['xiaohongshu_usable'] ?? 0 }}</div><div class="text-sm text-gray-600">小红书可用</div></div>
            </div>
            <table class="w-full text-sm">
                <thead><tr class="bg-gray-50"><th class="px-4 py-2 text-left">项目类型</th><th class="px-4 py-2 text-right">案例数</th></tr></thead>
                <tbody>
                    @foreach($showcaseStats['by_project'] ?? [] as $type => $count)
                        <tr class="border-t"><td class="px-4 py-2">{{ $type }}</td><td class="px-4 py-2 text-right">{{ $count }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
