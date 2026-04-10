<?php

namespace App\Filament\Resources\EquipmentResource\Pages;

use App\Filament\Resources\EquipmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEquipment extends CreateRecord
{
    protected static string $resource = EquipmentResource::class;
    protected static bool $canCreateAnother = false;
    protected static ?string $modelLabel = 'Add new Skate & Gears';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return 'Add New Equipment';
    }
}
