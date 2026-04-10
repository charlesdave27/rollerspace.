<?php

namespace App\Filament\Resources\LoyaltyMemberResource\Pages;

use App\Filament\Resources\LoyaltyMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLoyaltyMember extends EditRecord
{
    protected static string $resource = LoyaltyMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
