<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use App\Filament\Widgets\EquipmentWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEquipment extends ListRecords
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EquipmentWidget::class
        ];
    }



    protected function getTableBulkActions(): array
    {
        // Disable all bulk actions
        return [];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
