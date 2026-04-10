<?php

namespace App\Filament\Resources\RentalManagementResource\Pages;

use App\Filament\Resources\RentalManagementResource;
use App\Filament\Widgets\ActiveRentalWidget;
use App\Filament\Widgets\RentalOverviewWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalManagement extends ListRecords
{
    protected static string $resource = RentalManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ActiveRentalWidget::class
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
