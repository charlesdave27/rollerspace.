<div class="space-y-6">
  <!-- Customer Information -->
  <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
        Customer Information
      </h3>
      <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer Type</dt>
          <dd class="mt-1 text-sm text-gray-900 dark:text-white">
            {{ $record->loyaltyMember ? 'Loyalty Member' : 'Walk-in Customer' }}
          </dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer Name</dt>
          <dd class="mt-1 text-sm text-gray-900 dark:text-white">
            {{ $record->name ?? 'N/A' }}
          </dd>
        </div>
        @if ($record->loyaltyMember)
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member ID</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
              {{ $record->loyaltyMember->id }}
            </dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Points</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
              {{ $record->loyaltyMember->points ?? 0 }}
            </dd>
          </div>
        @endif
      </dl>
    </div>
  </div>

  <!-- Rental Information -->
  <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
        Rental Information
      </h3>
      <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rental ID</dt>
          <dd class="mt-1 text-sm text-gray-900 dark:text-white">
            #{{ $record->id }}
          </dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rental Date</dt>
          <dd class="mt-1 text-sm text-gray-900 dark:text-white">
            {{ $record->created_at->format('M j, Y g:i A') }}
          </dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Return Status</dt>
          <dd class="mt-1">
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $record->returned ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
              {{ $record->returned ? 'Returned' : 'Not Returned' }}
            </span>
          </dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
          <dd class="mt-1 text-sm text-gray-900 dark:text-white">
            {{ $record->updated_at->format('M j, Y g:i A') }}
          </dd>
        </div>
      </dl>
    </div>
  </div>

  <!-- Package & Equipment -->
  <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
        Package & Equipment
      </h3>
      <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Package</dt>
          <dd class="mt-1 text-sm text-gray-900 dark:text-white">
            {{ $record->rentalPackage->name ?? 'None' }}
          </dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Equipment</dt>
          <dd class="mt-1 text-sm text-gray-900 dark:text-white">
            {{ $record->equipment->name ?? 'None' }}
          </dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Duration</dt>
          <dd class="mt-1 text-sm text-gray-900 dark:text-white">
            {{ $record->rentalPackage->duration ?? ($record->reward->duration ?? 'Unlimited') }}
          </dd>
        </div>
        @if ($record->rentalPackage)
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Package Price</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
              ₱{{ number_format($record->rentalPackage->price ?? 0, 2) }}
            </dd>
          </div>
        @endif
        @if ($record->equipment)
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Equipment Price</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
              ₱{{ number_format($record->equipment->price ?? 0, 2) }}
            </dd>
          </div>
        @endif
      </dl>
    </div>
  </div>

  <!-- Financial Information -->
  <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
        Financial Information
      </h3>
      <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Revenue</dt>
          <dd class="mt-1 text-sm font-semibold text-green-600 dark:text-green-400">
            ₱{{ number_format($record->price_paid ?? 0, 2) }}
          </dd>
        </div>
        <div>
          <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Points Earned</dt>
          <dd class="mt-1 text-sm text-gray-900 dark:text-white">
            {{ $record->points ?? 0 }} points
          </dd>
        </div>
        @if ($record->reward)
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Claimed Reward</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
              {{ $record->reward->name }}
            </dd>
          </div>
          <div>
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Reward Value</dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
              ₱{{ number_format($record->reward->value ?? 0, 2) }}
            </dd>
          </div>
        @endif
      </dl>
    </div>
  </div>

  <!-- Additional Notes -->
  @if ($record->notes)
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white mb-4">
          Additional Notes
        </h3>
        <p class="text-sm text-gray-900 dark:text-white">
          {{ $record->notes }}
        </p>
      </div>
    </div>
  @endif
</div>
