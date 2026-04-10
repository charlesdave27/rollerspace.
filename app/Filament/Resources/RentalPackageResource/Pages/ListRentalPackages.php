<?php

namespace App\Filament\Resources\RentalPackageResource\Pages;

use App\Filament\Resources\RentalPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRentalPackages extends ListRecords
{
    protected static string $resource = RentalPackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
