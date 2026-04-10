<?php

namespace App\Filament\Resources\RewardResource\Pages;

use App\Filament\Resources\RewardResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateReward extends CreateRecord
{
    protected static string $resource = RewardResource::class;
    protected static bool $canCreateAnother = false;

    public function getTitle(): string
    {
        return 'Add New Reward';
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
