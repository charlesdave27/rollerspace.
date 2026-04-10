<div class="p-4 rounded-xl space-y-2 border shadow-sm">
  <!-- Status or default message -->
  <div id="default-message" class="text-gray-500">
    Scan a Loyalty Member QR code to view details.
  </div>

  <!-- Loyalty member info -->
  <div id="user-info" style="display: none;" class="space-y-2">
    <div><strong>Member ID:</strong> <span id="user-id"></span></div>
    <div><strong>Name:</strong> <span id="user-name"></span></div>
    <div><strong>Loyalty Points:</strong> <span id="loyalty-points"></span></div>
  </div>
</div>

<script>
  function updateUserDisplay(loyaltyMemberId, name, loyaltyPoints) {
    console.log('Updating LoyaltyMember display with:', {
      loyaltyMemberId,
      name,
      loyaltyPoints
    });

    const defaultMessage = document.getElementById('default-message');
    const userInfo = document.getElementById('user-info');

    if (defaultMessage) defaultMessage.style.display = 'none';
    if (userInfo) userInfo.style.display = 'block';

    document.getElementById('user-id').textContent = loyaltyMemberId ?? '';
    document.getElementById('user-name').textContent = name ?? '';
    document.getElementById('loyalty-points').textContent = loyaltyPoints ?? 0;
  }

  function clearUserDisplay(message = 'Scan a Loyalty Member QR code to view details.') {
    console.log('Clearing LoyaltyMember display');

    const defaultMessage = document.getElementById('default-message');
    const userInfo = document.getElementById('user-info');

    if (defaultMessage) {
      defaultMessage.style.display = 'block';
      defaultMessage.textContent = message;
    }
    if (userInfo) {
      userInfo.style.display = 'none';
    }

    // Reset values
    document.getElementById('user-id').textContent = '';
    document.getElementById('user-name').textContent = '';
    document.getElementById('loyalty-points').textContent = '';
  }

  function showLoadingMessage() {
    clearUserDisplay('Loading loyalty member data...');
  }

  // Quick manual test
  function testUserDisplay() {
    updateUserDisplay(101, 'Sample Member', 250);
  }
</script>
