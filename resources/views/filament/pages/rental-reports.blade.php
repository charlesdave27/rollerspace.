<x-filament-panels::page>
  <div class="space-y-6">
    {{-- Filters Form --}}
    <div class="bg-white rounded-lg shadow p-6">
      {{ $this->form }}

      <div class="mt-4">
        <x-filament::button wire:click="$refresh">
          Generate Report
        </x-filament::button>
      </div>
    </div>

    {{-- Summary Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      @php $stats = $this->getReportStats() @endphp

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['total_rentals']) }}</div>
        <div class="text-gray-600">Total Rentals</div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="text-2xl font-bold text-green-600">₱{{ number_format($stats['total_revenue'], 2) }}</div>
        <div class="text-gray-600">Total Revenue</div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="text-2xl font-bold text-purple-600">₱{{ number_format($stats['average_rental_value'], 2) }}</div>
        <div class="text-gray-600">Average Rental Value</div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="text-2xl font-bold text-orange-600">{{ number_format($stats['active_rentals']) }}</div>
        <div class="text-gray-600">Active Rentals</div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="text-2xl font-bold text-red-600">{{ number_format($stats['overdue_rentals']) }}</div>
        <div class="text-gray-600">Overdue Rentals</div>
      </div>

      <div class="bg-white p-6 rounded-lg shadow">
        <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_points_awarded']) }}</div>
        <div class="text-gray-600">Points Awarded</div>
      </div>
    </div>

    {{-- Detailed Table --}}
    <div class="bg-white rounded-lg shadow">
      {{ $this->table }}
    </div>
  </div>
</x-filament-panels::page>
