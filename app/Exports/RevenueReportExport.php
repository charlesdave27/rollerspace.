<?php

namespace App\Exports;

use App\Models\RentalManagement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RevenueReportExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
  protected $filters;

  public function __construct(array $filters = [])
  {
    $this->filters = $filters;
  }

  public function query()
  {
    $query = RentalManagement::query()
      ->with(['loyaltyMember', 'rentalPackage', 'equipment', 'reward'])
      ->where(function ($query) {
        $query->whereNotNull('price_paid')
          ->orWhereNotNull('reward_id');
      });

    // Apply filters
    if (isset($this->filters['from'])) {
      $query->whereDate('created_at', '>=', $this->filters['from']);
    }

    if (isset($this->filters['until'])) {
      $query->whereDate('created_at', '<=', $this->filters['until']);
    }

    if (isset($this->filters['has_package']) && $this->filters['has_package']) {
      $query->whereNotNull('rental_package_id');
    }

    if (isset($this->filters['has_equipment']) && $this->filters['has_equipment']) {
      $query->whereNotNull('equipment_id');
    }

    if (isset($this->filters['loyalty_members']) && $this->filters['loyalty_members']) {
      $query->whereNotNull('loyalty_member_id');
    }

    if (isset($this->filters['walk_ins']) && $this->filters['walk_ins']) {
      $query->whereNull('loyalty_member_id');
    }

    if (isset($this->filters['redeemed_rewards']) && $this->filters['redeemed_rewards']) {
      $query->whereNotNull('reward_id');
    }

    if (isset($this->filters['returned']) && $this->filters['returned'] !== '') {
      $query->where('returned', $this->filters['returned']);
    }

    if (isset($this->filters['month']) && $this->filters['month'] !== '') {
      $query->whereMonth('created_at', $this->filters['month']);
    }

    return $query->orderBy('created_at', 'desc');
  }

  public function headings(): array
  {
    return [
      'Rental ID',
      'Customer Type',
      'Customer Name',
      'Package',
      'Equipment',
      'Revenue (₱)',
      'Points Earned',
      'Claimed Reward',
      'Duration',
      'Rental Date',
      'Return Status',
    ];
  }

  public function map($rental): array
  {
    return [
      $rental->id,
      $rental->loyaltyMember ? 'Member' : 'Walk-in',
      $rental->name ?? 'N/A',
      $rental->rentalPackage->name ?? 'None',
      $rental->equipment->name ?? 'None',
      $rental->price_paid ?? 0,
      $rental->points ?? 0,
      $rental->reward->name ?? 'none',
      $rental->rentalPackage->duration ?? $rental->reward->duration ?? 'Unlimited',
      $rental->created_at->format('M j, Y g:i A'),
      $rental->returned ? 'Returned' : 'Not Returned',
    ];
  }

  public function styles(Worksheet $sheet)
  {
    return [
      1 => ['font' => ['bold' => true]],
    ];
  }

  public function columnWidths(): array
  {
    return [
      'A' => 10, // Rental ID
      'B' => 15, // Customer Type
      'C' => 20, // Customer Name
      'D' => 20, // Package
      'E' => 20, // Equipment
      'F' => 15, // Revenue
      'G' => 15, // Points
      'H' => 20, // Reward
      'I' => 15, // Duration
      'J' => 20, // Date
      'K' => 15, // Status
    ];
  }
}
