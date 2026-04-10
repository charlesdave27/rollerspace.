<?php

namespace App\Filament\Resources\RentalPackageResource\Pages;

use App\Filament\Resources\RentalPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRentalPackage extends CreateRecord
{
    protected static string $resource = RentalPackageResource::class;
    protected static bool $canCreateAnother = false;

    public function getTitle(): string
    {
        return 'Create New Rental Package';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    public function getBreadcrumbs(): array
    {
        return [];
    }
}
