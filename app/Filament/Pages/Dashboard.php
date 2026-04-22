<?php

namespace App\Filament\Pages;

use App\Services\DashboardService;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = '数据概览';

    protected static ?string $navigationLabel = '数据概览';

    protected static ?int $sort = -1;

    public array $dashboardData = [];

    public function mount(): void
    {
        $service = app(DashboardService::class);
        $this->dashboardData = $service->getDashboardData();
    }
}