<?php

namespace App\Filament\Widgets;

use App\Models\RentalManagement;
use App\Models\User;
use App\Models\Equipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class RentalOverviewWidget extends BaseWidget
{
    public bool $hideRevenue = false;

    public function mount(): void
    {
        $this->hideRevenue = session('hide_revenue', false);
    }

    public function toggleRevenue(): void
    {
        $this->hideRevenue = !$this->hideRevenue;
        session(['hide_revenue' => $this->hideRevenue]);
    }

    protected function getStats(): array
    {
        // Current month data
        $currentMonth = now()->startOfMonth();
        $currentMonthRevenue = RentalManagement::where('created_at', '>=', $currentMonth)->sum('price_paid');
        $currentMonthRentals = RentalManagement::where('created_at', '>=', $currentMonth)->count();

        // Last month for comparison
        $lastMonth = now()->subMonth()->startOfMonth();
        $lastMonthEnd = now()->subMonth()->endOfMonth();
        $lastMonthRevenue = RentalManagement::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->sum('price_paid');
        $lastMonthRentals = RentalManagement::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();

        // Calculate percentage changes
        $revenueChange = $lastMonthRevenue > 0 ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;
        $rentalsChange = $lastMonthRentals > 0 ? (($currentMonthRentals - $lastMonthRentals) / $lastMonthRentals) * 100 : 0;

        // Active and overdue rentals
        $activeRentals = RentalManagement::where('returned', false)->count();
        $overdueRentals = RentalManagement::where('returned', false)
            ->where('deadline', '<', now())->count();

        // Weekly revenue data for the last 7 days
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $endDate = now()->subDays($i)->endOfDay();
            $dailyRevenue = RentalManagement::whereBetween('created_at', [$date, $endDate])->sum('price_paid');
            $weeklyData[] = $dailyRevenue;
        }

        // Format revenue display
        $currentMonthRevenueDisplay = $this->hideRevenue ? '••••••' : '₱' . number_format($currentMonthRevenue, 2);
        $weeklyRevenueDisplay = $this->hideRevenue ? '••••••' : '₱' . number_format(array_sum($weeklyData), 2);

        $eyeIcon = $this->hideRevenue ? 'heroicon-m-eye-slash' : 'heroicon-m-eye';

        return [
            Stat::make(now()->format('F') . ' Revenue', $currentMonthRevenueDisplay)
                ->description(($revenueChange >= 0 ? '+' : '') . number_format($revenueChange, 1) . '% from last month')
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->icon($this->hideRevenue ? 'heroicon-m-eye-slash' : 'heroicon-m-eye')
                ->extraAttributes([
                    'class' => 'cursor-pointer group',
                    'wire:click' => 'toggleRevenue',
                    'title' => $this->hideRevenue ? 'Click to show revenue' : 'Click to hide revenue',
                ]),

            Stat::make('Monthly Rentals', number_format($currentMonthRentals))
                ->description(($rentalsChange >= 0 ? '+' : '') . number_format($rentalsChange, 1) . '% from last month')
                ->descriptionIcon($rentalsChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($rentalsChange >= 0 ? 'success' : 'danger'),

            Stat::make('Weekly Revenue', $weeklyRevenueDisplay)
                ->description('Last 7 days revenue trend')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary')
                ->chart($weeklyData)
                ->icon($this->hideRevenue ? 'heroicon-m-eye-slash' : 'heroicon-m-eye'),
        ];
    }
}
