<?php

namespace App\Filament\Resources\RentalManagementResource\Pages;

use App\Filament\Resources\RentalManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRentalManagement extends EditRecord
{
    protected static string $resource = RentalManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
