<?php

namespace App\Filament\Widgets;

use App\Models\RentalManagement;
use App\Models\User;
use App\Models\Equipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

namespace App\Filament\Widgets;

use App\Models\RentalManagement;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ActiveRentalWidget extends BaseWidget
{
    public bool $hidetodayRevenue = false;

    public function mount(): void
    {
        $this->hidetodayRevenue = session('hide_revenue', false);
    }

    public function toggleRevenue(): void
    {
        $this->hidetodayRevenue = !$this->hidetodayRevenue;
        session(['hide_revenue' => $this->hidetodayRevenue]);
    }
    protected function getStats(): array
    {
        // Today’s start and end
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        // Revenue and rentals for today
        $todayRevenue = RentalManagement::whereBetween('created_at', [$todayStart, $todayEnd])->sum('price_paid');
        $todayRentals = RentalManagement::whereBetween('created_at', [$todayStart, $todayEnd])->count();

        $currentRevenueDisplay = $this->hidetodayRevenue ? '••••••' : '₱' . number_format($todayRevenue, 2);

        // Active and overdue rentals (still useful to display)
        $activeRentals = RentalManagement::where('returned', false)->count();
        $overdueRentals = RentalManagement::where('returned', false)
            ->where('deadline', '<', now())->count();

        return [

            Stat::make('Active Rentals', number_format($activeRentals))
                ->description($overdueRentals . ' overdue')
                ->descriptionIcon('heroicon-m-clock')

                ->color($overdueRentals > 0 ? 'warning' : 'success'),
            Stat::make('Today\'s Rentals', number_format($todayRentals))
                ->description('Rentals created today')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('info'),

            Stat::make('Today\'s Revenue', $currentRevenueDisplay)
                ->description('Revenue collected today')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->icon($this->hidetodayRevenue ? 'heroicon-m-eye-slash' : 'heroicon-m-eye')
                ->extraAttributes([
                    'class' => 'cursor-pointer group',
                    'wire:click' => 'toggleRevenue',
                    'title' => $this->hidetodayRevenue ? 'Click to show revenue' : 'Click to hide revenue',
                ]),
        ];
    }
}
