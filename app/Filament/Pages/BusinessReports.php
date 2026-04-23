<?php

namespace App\Filament\Pages;

use App\Models\SalesRep;
use App\Models\Doctor;
use App\Services\ReportService;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Tables\Actions\Action;

class BusinessReports extends Page
{
    use InteractsWithForms;

    protected static ?string $title = '经营分析报表';
    protected static ?string $navigationLabel = '经营分析';
    protected static ?int $navigationSort = 99;
    protected string $view = 'filament.pages.business-reports';

    public ?string $startDate = null;
    public ?string $endDate = null;
    public ?int $salesRepId = null;
    public ?int $doctorId = null;
    public ?string $projectType = null;

    public array $memberStats = [];
    public array $pointStats = [];
    public array $redemptionStats = [];
    public array $followUpStats = [];
    public array $showcaseStats = [];

    public function mount(): void
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->loadStats();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('筛选条件')->schema([
                DatePicker::make('startDate')->label('开始日期'),
                DatePicker::make('endDate')->label('结束日期'),
                Select::make('salesRepId')
                    ->label('销售')
                    ->options(SalesRep::pluck('name', 'id'))
                    ->nullable()
                    ->placeholder('全部'),
                Select::make('doctorId')
                    ->label('医生')
                    ->options(Doctor::pluck('name', 'id'))
                    ->nullable()
                    ->placeholder('全部'),
                Select::make('projectType')
                    ->label('项目类型')
                    ->options([
                        '祛痘' => '祛痘',
                        '痘坑' => '痘坑',
                        '疤痕' => '疤痕',
                        '光子嫩肤' => '光子嫩肤',
                        '黄金微针' => '黄金微针',
                        '眼整形' => '眼整形',
                    ])
                    ->nullable()
                    ->placeholder('全部'),
            ])->columns(4),
        ]);
    }

    public function loadStats(): void
    {
        $service = app(ReportService::class)->setDateRange($this->startDate, $this->endDate);
        if ($this->salesRepId) $service->setSalesRepId($this->salesRepId);
        if ($this->doctorId) $service->setDoctorId($this->doctorId);
        if ($this->projectType) $service->setProjectType($this->projectType);

        $this->memberStats = $service->getMemberStats();
        $this->pointStats = $service->getPointStats();
        $this->redemptionStats = $service->getRedemptionStats();
        $this->followUpStats = $service->getFollowUpStats();
        $this->showcaseStats = $service->getShowcaseStats();
    }

    public function exportPoints(): \Symfony\Component\HttpFoundation\Response
    {
        $service = app(ReportService::class)->setDateRange($this->startDate, $this->endDate);
        $data = $service->getPointStats()['recent'];
        $path = $service->exportToCsv($data, 'points_' . date('Ymd'));
        return response()->download($path)->deleteFileAfterSend();
    }

    public function exportRedemptions(): \Symfony\Component\HttpFoundation\Response
    {
        $service = app(ReportService::class)->setDateRange($this->startDate, $this->endDate);
        $data = $service->getRedemptionStats()['recent'];
        $path = $service->exportToCsv($data, 'redemptions_' . date('Ymd'));
        return response()->download($path)->deleteFileAfterSend();
    }

    public function exportFollowUps(): \Symfony\Component\HttpFoundation\Response
    {
        $service = app(ReportService::class)->setDateRange($this->startDate, $this->endDate);
        $data = $service->getFollowUpStats()['recent'];
        $path = $service->exportToCsv($data, 'followups_' . date('Ymd'));
        return response()->download($path)->deleteFileAfterSend();
    }

    public function exportShowcases(): \Symfony\Component\HttpFoundation\Response
    {
        $service = app(ReportService::class)->setDateRange($this->startDate, $this->endDate);
        $data = $service->getShowcaseStats()['recent'];
        $path = $service->exportToCsv($data, 'showcases_' . date('Ymd'));
        return response()->download($path)->deleteFileAfterSend();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('导出积分流水')->action('exportPoints'),
            Action::make('导出兑换订单')->action('exportRedemptions'),
            Action::make('导出跟进记录')->action('exportFollowUps'),
            Action::make('导出案例素材')->action('exportShowcases'),
        ];
    }
}
