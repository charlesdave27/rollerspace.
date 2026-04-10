<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalManagement extends Model
{
    protected $casts = [
        'deadline' => 'datetime',
        'returned' => 'boolean',
    ];

    protected $fillable = [
        'loyalty_member_id',
        'name',
        'rental_package_id',
        'reward_id',
        'equipment_id',
        'points',
        'price_paid',
        'deadline',
        'returned'
    ];

    public function loyaltyMember()
    {
        return $this->belongsTo(LoyaltyMember::class);
    }

    public function rentalPackage()
    {
        return $this->belongsTo(RentalPackage::class, 'rental_package_id');
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function equipments()
    {
        return $this->belongsToMany(Equipment::class, 'rental_equipment', 'rental_management_id', 'equipment_id')
            ->withTimestamps()
            ->withPivot('returned');
    }

    public function getEquipmentNameAttribute()
    {
        return $this->equipment->name ?? 'No equipment';
    }

    public function getEquipmentNamesAttribute()
    {
        $names = $this->equipments->pluck('name')->filter();
        if ($names->isEmpty() && $this->equipment) {
            $names = collect([$this->equipment->name]);
        }
        return $names->join(', ');
    }

    public function getRentalOrRewardNameAttribute()
    {
        return $this->rentalPackage->name
            ?? $this->reward->name
            ?? '-';
    }
}
