<?php

namespace App\Filament\Widgets;

use App\Models\Equipment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EquipmentWidget extends BaseWidget
{
  protected function getStats(): array
  {
    // Total equipment count
    $totalEquipment = Equipment::count();

    // Available equipment count
    $availableEquipment = Equipment::where('is_available', true)->count();

    // Unavailable equipment count
    $unavailableEquipment = Equipment::where('is_available', false)->count();
    $largeEquipment = Equipment::where('size', 'Large')->count();
    $mediumEquipment = Equipment::where('size', 'Medium')->count();
    $smallEquipment = Equipment::where('size', 'Small')->count();

    // Calculate availability percentage
    $availabilityPercentage = $totalEquipment > 0 ? round(($availableEquipment / $totalEquipment) * 100, 1) : 0;

    // Equipment by type (get the most common type)
    $equipmentByType = Equipment::selectRaw('type, COUNT(*) as count')
      ->groupBy('type')
      ->orderBy('count', 'desc')
      ->first();

    $mostCommonType = $equipmentByType ? $equipmentByType->type : 'N/A';
    $mostCommonTypeCount = $equipmentByType ? $equipmentByType->count : 0;

    return [
      Stat::make('Total Equipment', number_format($totalEquipment))
        ->description('Items in inventory')
        ->descriptionIcon('heroicon-m-archive-box')
        ->color('primary'),

      Stat::make('Available Equipment', number_format($availableEquipment))
        // ->description($availabilityPercentage . '% availability rate')
        ->description('Available')
        ->descriptionIcon('heroicon-m-check-circle')
        ->color('success'),

      Stat::make('Unavailable Equipment', number_format($unavailableEquipment))
        ->description('Currently rented')
        ->descriptionIcon('heroicon-m-x-circle')
        ->color('danger'),

      Stat::make('Large Equipment', number_format($largeEquipment))
        ->description('Large equipment')
        ->descriptionIcon('heroicon-m-arrow-trending-up')
        ->color('primary'),

      Stat::make('Medium Equipment', number_format($mediumEquipment))
        ->description('Medium equipment')
        ->descriptionIcon('heroicon-m-archive-box')
        ->color('primary'),

      Stat::make('Small Equipment', number_format($smallEquipment))
        ->description('Small equipment')
        ->descriptionIcon('heroicon-m-archive-box')
        ->color('primary'),
    ];
  }
}
