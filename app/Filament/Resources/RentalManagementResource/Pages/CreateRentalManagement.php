<?php

namespace App\Filament\Resources\RentalManagementResource\Pages;

use App\Filament\Resources\RentalManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRentalManagement extends CreateRecord
{
    protected static string $resource = RentalManagementResource::class;
    protected static bool $canCreateAnother = false;

    public function getTitle(): string
    {
        return '';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Convert deadline_hours to actual deadline timestamp
        if (isset($data['deadline_hours'])) {
            $data['deadline'] = now()->addHours((int) $data['deadline_hours']);
            unset($data['deadline_hours']);
        }

        // Populate legacy equipment_id from non-dehydrated fields
        // Prefer new repeater 'equipments' if present; fallback to older multi-selects
        $repeaterItems = (array)($this->data['equipments'] ?? []);
        $primaryEquipmentIdFromRepeater = collect($repeaterItems)
            ->pluck('equipment_id')
            ->filter()
            ->first();

        $skates = (array)($this->data['skate_ids'] ?? []);
        $helmets = (array)($this->data['helmet_ids'] ?? []);
        $pads = (array)($this->data['pad_ids'] ?? []);

        $primaryEquipmentId = $primaryEquipmentIdFromRepeater
            ?? collect([$skates[0] ?? null, $helmets[0] ?? null, $pads[0] ?? null])
            ->filter()
            ->first();
        if ($primaryEquipmentId) {
            $data['equipment_id'] = $primaryEquipmentId;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record; // The created rental

        // Update user points after creating a rental
        $loyaltyMember = $record->loyalty_member_id ? \App\Models\LoyaltyMember::find($record->loyalty_member_id) : null;
        if ($loyaltyMember && $record->rental_package_id) {
            $rentalPackage = \App\Models\RentalPackage::find($record->rental_package_id);
            if ($rentalPackage) {
                $loyaltyMember->loyalty_points += $rentalPackage->points_rewarded;
                $loyaltyMember->save();
            }
        }

        // Deduct points if a reward was selected
        if ($record->reward_id) {
            $loyaltyMember = $record->loyalty_member_id ? \App\Models\LoyaltyMember::find($record->loyalty_member_id) : null;
            $reward = \App\Models\Reward::find($record->reward_id);
            if ($loyaltyMember && $reward && $loyaltyMember->loyalty_points >= $reward->required_points) {
                $loyaltyMember->loyalty_points -= $reward->required_points;
                $loyaltyMember->save();
            }
        }

        // Collect selected equipment IDs from form state
        $repeaterItems = collect((array) ($this->data['equipments'] ?? []));
        $selectedFromRepeater = $repeaterItems->pluck('equipment_id')->filter();

        $selectedIds = collect([
            ...$selectedFromRepeater,
            ...((array) ($this->data['skate_ids'] ?? [])),
            ...((array) ($this->data['helmet_ids'] ?? [])),
            ...((array) ($this->data['pad_ids'] ?? [])),
        ])->filter()->unique()->values();

        if ($record->equipment_id) {
            $selectedIds->push($record->equipment_id);
        }

        if ($selectedIds->isNotEmpty()) {
            $record->equipments()->syncWithoutDetaching($selectedIds->all());
            foreach ($selectedIds as $equipmentId) {
                $equipment = \App\Models\Equipment::find($equipmentId);
                if ($equipment) {
                    $equipment->is_available = false;
                    $equipment->save();
                }
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        // Automatically download receipt after creation
        return route('receipt.pdf', $this->record->id);
    }
}
