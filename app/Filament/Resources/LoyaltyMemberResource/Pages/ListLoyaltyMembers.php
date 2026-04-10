<?php

namespace App\Filament\Resources\LoyaltyMemberResource\Pages;

use App\Filament\Resources\LoyaltyMemberResource;
use App\Filament\Widgets\LoyaltyMemberWidget;
use App\Models\LoyaltyMember;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoyaltyMembers extends ListRecords
{
    protected static string $resource = LoyaltyMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LoyaltyMemberWidget::class
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }
}
