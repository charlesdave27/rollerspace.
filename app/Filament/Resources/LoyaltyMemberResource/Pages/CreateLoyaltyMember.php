<?php

namespace App\Filament\Resources\LoyaltyMemberResource\Pages;

use App\Filament\Resources\LoyaltyMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLoyaltyMember extends CreateRecord
{
    protected static string $resource = LoyaltyMemberResource::class;
    protected static bool $canCreateAnother = false;

    public function getTitle(): string
    {
        return 'Register New Member';
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
