<?php

namespace App\Filament\Widgets;

use App\Models\LoyaltyMember;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LoyaltyMemberWidget extends BaseWidget
{
  protected function getStats(): array
  {
    // Total loyalty members
    $totalMembers = LoyaltyMember::count();

    // Total loyalty points across all members
    $totalPoints = LoyaltyMember::sum('loyalty_points');

    // New members this month
    $currentMonth = now()->startOfMonth();
    $newMembersThisMonth = LoyaltyMember::where('created_at', '>=', $currentMonth)->count();

    // Last month for comparison
    $lastMonth = now()->subMonth()->startOfMonth();
    $lastMonthEnd = now()->subMonth()->endOfMonth();
    $newMembersLastMonth = LoyaltyMember::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();

    // Calculate percentage change for new members
    $membersChange = $newMembersLastMonth > 0 ? (($newMembersThisMonth - $newMembersLastMonth) / $newMembersLastMonth) * 100 : 0;

    // Average points per member
    $averagePoints = $totalMembers > 0 ? round($totalPoints / $totalMembers, 1) : 0;

    // Top member with highest points
    $topMember = LoyaltyMember::orderBy('loyalty_points', 'desc')->first();
    $topMemberPoints = $topMember ? $topMember->loyalty_points : 0;

    return [
      Stat::make('Total Members', number_format($totalMembers))
        ->description('Loyalty members')
        ->descriptionIcon('heroicon-m-users')
        ->color('success'),

      Stat::make('New Members This Month', number_format($newMembersThisMonth))
        ->description(($membersChange >= 0 ? '+' : '') . number_format($membersChange, 1) . '% from last month')
        ->descriptionIcon($membersChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
        ->color($membersChange >= 0 ? 'success' : 'danger'),

    ];
  }
}
