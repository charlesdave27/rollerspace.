<div class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
    </path>
  </svg>
  <span id="current-datetime" class="font-medium"></span>
</div>

<script>
  function updateDateTime() {
    const now = new Date();
    const options = {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
      hour12: true
    };
    const formattedDateTime = now.toLocaleString('en-US', options);
    document.getElementById('current-datetime').textContent = formattedDateTime;
  }

  // Update immediately and then every second
  updateDateTime();
  setInterval(updateDateTime, 1000);
</script>
