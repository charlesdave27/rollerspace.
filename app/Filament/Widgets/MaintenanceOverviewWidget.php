<?php

namespace App\Filament\Widgets;

use App\Models\Equipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MaintenanceOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 2;

    protected function getStats(): array
    {
        $maintenance = Equipment::where('status', Equipment::STATUS_MAINTENANCE)->count();
        $damaged = Equipment::where('status', Equipment::STATUS_DAMAGED)->count();
        $needsAttention = $maintenance + $damaged;

        return [
            Stat::make('In maintenance', $maintenance)
                ->description('Repair or service')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color($maintenance > 0 ? 'warning' : 'gray'),
            Stat::make('Damaged', $damaged)
                ->description('Out of service')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($damaged > 0 ? 'danger' : 'gray'),
        ];
    }
}
