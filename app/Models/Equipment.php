<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    public const STATUS_AVAILABLE = 'available';
    public const STATUS_MAINTENANCE = 'maintenance';
    public const STATUS_DAMAGED = 'damaged';
    public const STATUS_RETIRED = 'retired';

    protected $fillable = [
        'name',
        'type',
        'size',
        'is_available',
        'status',
        'maintenance_notes',
        'last_maintenance_at',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'last_maintenance_at' => 'datetime',
    ];

    public static function statusOptions(): array
    {
        return [
            self::STATUS_AVAILABLE => 'Available',
            self::STATUS_MAINTENANCE => 'Maintenance',
            self::STATUS_DAMAGED => 'Damaged',
            self::STATUS_RETIRED => 'Retired',
        ];
    }

    public function rentalManagements()
    {
        return $this->belongsToMany(RentalManagement::class, 'rental_equipment', 'equipment_id', 'rental_management_id')
            ->withTimestamps()
            ->withPivot('returned');
    }
}
