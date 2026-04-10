<div>
  <button type="button" onclick="openQRScanner()" class="qr-btn">
    <svg xmlns="http://www.w3.org/2000/svg" class="qr-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M3 7V5a2 2 0 012-2h2m10 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2m-10 0H5a2 2 0 01-2-2v-2" />
    </svg>
    Scan QR Code
  </button>

  <dialog id="qrModal" class="qr-modal">
    <div class="qr-container">
      <div class="qr-title">
        <svg xmlns="http://www.w3.org/2000/svg" class="qr-title-icon" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M3 7V5a2 2 0 012-2h2m10 0h2a2 2 0 012 2v2m0 10v2a2 2 0 01-2 2h-2m-10 0H5a2 2 0 01-2-2v-2" />
        </svg>
        <span>Scan QR Code</span>
      </div>
      <div id="reader" class="qr-reader"></div>
      <div id="qr-result" class="qr-result"></div>
      <div id="loading-spinner" class="qr-spinner" aria-hidden="true">
        <svg class="qr-spinner-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="spinner-track" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
          </circle>
          <path class="spinner-head" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
      </div>
      <div class="qr-actions">
        <button type="button" onclick="retryQRScanner()" class="qr-retry-btn" id="retryBtn"
          style="display: none;">Retry</button>
        <button type="button" onclick="openFileUpload()" class="qr-upload-btn" id="uploadBtn">
          <svg xmlns="http://www.w3.org/2000/svg" class="upload-icon" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
          </svg>
          Upload QR
        </button>
        <button type="button" onclick="closeQRScanner(event)" class="qr-close-btn">Close</button>
      </div>

      <!-- Hidden file input -->
      <input type="file" id="qrFileInput" accept="image/*" style="display: none;" onchange="handleFileUpload(event)">
    </div>
  </dialog>


  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
  <script>
    let scanner;
    let hasRendered = false;
    let scanHandled = false;

    function setLoading(isLoading) {
      const spinner = document.getElementById('loading-spinner');
      if (spinner) spinner.classList.toggle('hidden', !isLoading);
    }

    function showMessage(message, type = 'success') {
      const result = document.getElementById('qr-result');
      if (!result) return;
      let icon = '';
      if (type === 'success') {
        icon =
          `<svg class=\"msg-icon success\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M5 13l4 4L19 7\" /></svg>`;
      } else if (type === 'error') {
        icon =
          `<svg class=\"msg-icon error\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M6 18L18 6M6 6l12 12\" /></svg>`;
      } else if (type === 'info') {
        icon =
          `<svg class=\"msg-icon info\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\"><path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M13 16h-1v-4h-1m1-4h.01\" /></svg>`;
      }
      result.innerHTML = `${icon}<span class="msg-text">${message}</span>`;
    }

    async function openQRScanner() {
      const modal = document.getElementById('qrModal');
      modal.showModal();
      scanHandled = false;
      showMessage('Initializing camera...', 'info');
      setLoading(true);

      // Check if camera is available
      if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        showMessage('Camera not supported in this browser', 'error');
        setLoading(false);
        return;
      }

      try {
        // Test camera access first
        const stream = await navigator.mediaDevices.getUserMedia({
          video: {
            facingMode: 'environment' // Use back camera if available
          }
        });

        // Stop the test stream
        stream.getTracks().forEach(track => track.stop());

        showMessage('Align the QR code within the box', 'info');
        setLoading(false);

        if (!hasRendered) {
          // Try different camera configurations
          const configs = [{
              fps: 10,
              qrbox: {
                width: 250,
                height: 250
              },
              aspectRatio: 1.0,
              supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA]
            },
            {
              fps: 5,
              qrbox: {
                width: 200,
                height: 200
              },
              aspectRatio: 1.0
            },
            {
              fps: 10,
              qrbox: {
                width: 300,
                height: 300
              }
            }
          ];

          let configIndex = 0;
          let renderSuccess = false;

          while (!renderSuccess && configIndex < configs.length) {
            try {
              scanner = new Html5QrcodeScanner("reader", configs[configIndex]);
              await scanner.render(onScanSuccess, onScanError);
              renderSuccess = true;
              hasRendered = true;
              console.log(`QR Scanner initialized with config ${configIndex + 1}`);
            } catch (renderError) {
              console.warn(`Config ${configIndex + 1} failed:`, renderError);
              configIndex++;

              if (configIndex < configs.length) {
                // Clear any partial initialization
                const readerElement = document.getElementById("reader");
                if (readerElement) {
                  readerElement.innerHTML = "";
                }
              }
            }
          }

          if (!renderSuccess) {
            throw new Error('Failed to initialize QR scanner with any configuration');
          }
        }
      } catch (error) {
        console.error('Camera access error:', error);
        showMessage('Camera access denied or not available. Please check permissions.', 'error');
        setLoading(false);

        // Show retry button
        const retryBtn = document.getElementById('retryBtn');
        if (retryBtn) {
          retryBtn.style.display = 'inline-block';
        }
      }
    }

    function retryQRScanner() {
      // Reset state
      hasRendered = false;
      scanHandled = false;

      // Clear previous scanner
      if (scanner) {
        scanner.clear().then(() => {
          const readerElement = document.getElementById("reader");
          if (readerElement) {
            readerElement.innerHTML = "";
          }
        });
        scanner = null;
      }

      // Hide retry button
      const retryBtn = document.getElementById('retryBtn');
      if (retryBtn) {
        retryBtn.style.display = 'none';
      }

      // Try again
      openQRScanner();
    }

    function openFileUpload() {
      const fileInput = document.getElementById('qrFileInput');
      if (fileInput) {
        fileInput.click();
      }
    }

    async function handleFileUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      // Validate file type
      if (!file.type.startsWith('image/')) {
        showMessage('Please select an image file', 'error');
        return;
      }

      // Validate file size (max 10MB)
      if (file.size > 10 * 1024 * 1024) {
        showMessage('File size too large. Please select an image under 10MB', 'error');
        return;
      }

      showMessage('Processing uploaded image...', 'info');
      setLoading(true);

      try {
        // Use Html5Qrcode to scan the uploaded file
        const html5Qrcode = new Html5Qrcode("reader");

        // First, clear any existing scanner
        if (scanner) {
          await scanner.clear();
          const readerElement = document.getElementById("reader");
          if (readerElement) {
            readerElement.innerHTML = "";
          }
        }

        // Scan the uploaded file
        const result = await html5Qrcode.scanFile(file, true);

        if (result) {
          // Process the scanned result
          await processQRResult(result);
        } else {
          showMessage('No QR code found in the uploaded image', 'error');
          setLoading(false);
        }
      } catch (error) {
        console.error('File upload scan error:', error);

        if (error.includes('No QR code found') || error.includes('No MultiFormat Readers')) {
          showMessage('No QR code found in the uploaded image. Please try a different image.', 'error');
        } else {
          showMessage('Error processing uploaded image: ' + error, 'error');
        }
        setLoading(false);
      }
    }

    async function processQRResult(decodedText) {
      if (scanHandled) return;
      scanHandled = true;

      showMessage('QR Code Scanned Successfully!', 'success');
      setLoading(true);

      // Match /loyalty-member/{id} instead of /user/{id}
      const urlMatch = decodedText.match(/\/loyalty-member\/(\d+)/);
      let loyaltyMemberId = null;

      if (urlMatch) {
        loyaltyMemberId = urlMatch[1];
        await fetchUserData(loyaltyMemberId);
      } else {
        try {
          const qrData = JSON.parse(decodedText);
          if (qrData.loyalty_member_id && qrData.name) {
            updateFilamentFields(qrData.loyalty_member_id, qrData.name);
            updateUserDisplay(qrData.user_id, qrData.name, qrData.loyalty_points || 0);
            setLoading(false);
          } else if (qrData.loyalty_member_id) {
            await fetchUserData(qrData.loyalty_member_id);
          }
        } catch (e) {
          if (/^\d+$/.test(decodedText)) {
            loyaltyMemberId = decodedText;
            await fetchUserData(loyaltyMemberId);
          } else {
            showMessage('Invalid QR code format', 'error');
            setLoading(false);
            return;
          }
        }
      }

      setTimeout(() => {
        closeQRScanner();
      }, 1200);
    }

    function closeQRScanner(e) {
      if (e && typeof e.preventDefault === 'function') e.preventDefault();
      if (scanner) {
        scanner.clear().then(() => {
          document.getElementById("reader").innerHTML = "";
          hasRendered = false;
        });
      }
      document.getElementById('qrModal').close();
      const result = document.getElementById('qr-result');
      if (result) result.innerHTML = '';
      scanHandled = false;
      setLoading(false);

      // Hide retry button
      const retryBtn = document.getElementById('retryBtn');
      if (retryBtn) {
        retryBtn.style.display = 'none';
      }

      // Reset file input
      const fileInput = document.getElementById('qrFileInput');
      if (fileInput) {
        fileInput.value = '';
      }
    }

    async function onScanSuccess(decodedText, decodedResult) {
      if (scanHandled) return;
      scanHandled = true;
      showMessage('QR Code Scanned Successfully!', 'success');
      setLoading(true);

      // Match /loyalty-member/{id} instead of /user/{id}
      const urlMatch = decodedText.match(/\/loyalty-member\/(\d+)/);
      let loyaltyMemberId = null;

      if (urlMatch) {
        loyaltyMemberId = urlMatch[1];
        await fetchUserData(loyaltyMemberId);
      } else {
        try {
          const qrData = JSON.parse(decodedText);
          if (qrData.loyalty_member_id && qrData.name) {
            updateFilamentFields(qrData.loyalty_member_id, qrData.name);
            updateUserDisplay(qrData.user_id, qrData.name, qrData.loyalty_points || 0);
            setLoading(false);
          } else if (qrData.loyalty_member_id) {
            await fetchUserData(qrData.loyalty_member_id);
          }
        } catch (e) {
          if (/^\d+$/.test(decodedText)) {
            loyaltyMemberId = decodedText;
            await fetchUserData(loyaltyMemberId);
          } else {
            showMessage('Invalid QR code format', 'error');
            setLoading(false);
            return;
          }
        }
      }

      setTimeout(() => {
        closeQRScanner();
      }, 1200);
    }

    async function fetchUserData(loyaltyMemberId) {
      showMessage('Loading user data...', 'info');
      setLoading(true);

      const defaultMessage = document.getElementById('default-message');
      if (defaultMessage) defaultMessage.textContent = 'Loading user data...';

      // Use the correct variable
      const apiUrl = `/admin/api/loyalty-member/${loyaltyMemberId}`;

      try {
        const response = await fetch(apiUrl);
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const data = await response.json();

        if (data.success) {
          updateUserDisplay(data.user.id, data.user.name, data.user.loyalty_points);
          updateFilamentFields(data.user.id, data.user.name);
          showMessage('User loaded successfully!', 'success');
        } else {
          clearUserDisplay();
          showMessage('User not found: ' + (data.message || 'Unknown error'), 'error');
        }
      } catch (error) {
        clearUserDisplay();
        showMessage('Error loading user data: ' + error.message, 'error');
      } finally {
        setLoading(false);
      }
    }


    function updateFilamentFields(loyaltyMemberId, userName) {
      const possibleUserIdSelectors = [
        'input[name="data[loyalty_member_id]"]',
        'input[name="loyalty_member_id"]',
        'input[id*="loyalty_member_id"]',
        'input[wire\\:model*="loyalty_member_id"]'
      ];
      const possibleNameSelectors = [
        'input[name="data[name]"]',
        'input[name="name"]',
        'input[id*="name"]',
        'input[wire\\:model*="name"]'
      ];
      let loyaltyMemberIdInput = null;
      for (const selector of possibleUserIdSelectors) {
        loyaltyMemberIdInput = document.querySelector(selector);
        if (loyaltyMemberIdInput) break;
      }
      if (loyaltyMemberIdInput) {
        loyaltyMemberIdInput.value = loyaltyMemberId;
        loyaltyMemberIdInput.dispatchEvent(new Event('input', {
          bubbles: true
        }));
      }
      let userNameInput = null;
      for (const selector of possibleNameSelectors) {
        userNameInput = document.querySelector(selector);
        if (userNameInput) break;
      }
      if (userNameInput) {
        userNameInput.value = userName;
        userNameInput.dispatchEvent(new Event('input', {
          bubbles: true
        }));
      }
      setTimeout(() => {
        if (window.Alpine) window.Alpine.initTree(document.body);
      }, 100);
    }



    function onScanError(errorMessage) {
      console.error('QR Scan Error:', errorMessage);

      // Handle specific error types
      if (errorMessage.includes('No MultiFormat Readers')) {
        showMessage('');
      } else if (errorMessage.includes('Permission denied')) {
        showMessage('Camera permission denied. Please allow camera access.', 'error');
      } else if (errorMessage.includes('NotAllowedError')) {
        showMessage('Camera access blocked. Please check browser settings.', 'error');
      } else if (errorMessage.includes('NotFoundError')) {
        showMessage('No camera found. Please connect a camera device.', 'error');
      } else if (errorMessage.includes('NotReadableError')) {
        showMessage('Camera is being used by another application.', 'error');
      } else {
        showMessage('QR Scan Error: ' + errorMessage, 'error');
      }

      // Don't show error for every scan attempt, only for actual errors
      if (!errorMessage.includes('No QR code found')) {
        setTimeout(() => {
          showMessage('');
        }, 2000);
      }
    }
  </script>

  <style>
    .hidden {
      display: none;
    }

    .qr-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      background-color: #342eaa;
      color: #fff;
      font-weight: 600;
      border-radius: 10px;
      box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      transition: all 200ms ease;
      border: none;
      cursor: pointer;
    }

    .qr-icon {
      width: 20px;
      height: 20px;
    }

    .qr-modal {
      width: 370px;
      border: 0;
      border-radius: 16px;
      padding: 0;
      background: #fff;
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .qr-container {
      padding: 24px;
    }

    .qr-title {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      font-size: 20px;
      font-weight: 800;
      margin-bottom: 8px;
      color: #4338ca;
    }

    .qr-title-icon {
      width: 24px;
      height: 24px;
      color: #6366f1;
    }

    .qr-reader {
      width: 300px;
      margin: 0 auto;
      border-radius: 8px;
      border: 1px solid #e5e7eb;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
      overflow: hidden;
    }

    .qr-result {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      text-align: center;
      margin-top: 16px;
      font-weight: 600;
      min-height: 24px;
    }

    .msg-icon {
      width: 20px;
      height: 20px;
    }

    .msg-icon.success {
      color: #16a34a;
    }

    .msg-icon.error {
      color: #dc2626;
    }

    .msg-icon.info {
      color: #2563eb;
    }

    .msg-text {
      color: #111827;
    }

    .qr-spinner {
      display: none;
      justify-content: center;
      margin-top: 8px;
    }

    .qr-spinner:not(.hidden) {
      display: flex;
    }

    .qr-spinner-icon {
      width: 24px;
      height: 24px;
      color: #4f46e5;
      animation: spin 1s linear infinite;
    }

    .spinner-track {
      opacity: 0.25;
    }

    .spinner-head {
      opacity: 0.75;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .qr-actions {
      text-align: center;
      margin-top: 16px;
      display: flex;
      gap: 8px;
      justify-content: center;
    }

    .qr-retry-btn {
      padding: 8px 14px;
      background: #342eaa;
      color: #fff;
      border-radius: 8px;
      font-weight: 500;
      border: none;
      cursor: pointer;
      transition: background 150ms ease;
    }

    .qr-retry-btn:hover {
      background: #2a2470;
    }

    .qr-upload-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      background: #059669;
      color: #fff;
      border-radius: 8px;
      font-weight: 500;
      border: none;
      cursor: pointer;
      transition: background 150ms ease;
    }

    .qr-upload-btn:hover {
      background: #047857;
    }

    .upload-icon {
      width: 16px;
      height: 16px;
    }

    .qr-close-btn {
      padding: 8px 14px;
      background: #e5e7eb;
      color: #374151;
      border-radius: 8px;
      font-weight: 500;
      border: none;
      cursor: pointer;
      transition: background 150ms ease;
    }

    .qr-close-btn:hover {
      background: #d1d5db;
    }

    .qr-info-wrap {
      margin-top: 24px;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .qr-default {
      color: #6b7280;
      font-size: 16px;
    }


    /* Dark mode overrides */
    .dark .qr-modal {
      background: #111827;
      /* gray-900 */
    }

    .dark .qr-container {
      color: #e5e7eb;
      /* gray-200 text */
    }

    .dark .qr-title {
      color: #c7d2fe;
      /* indigo-200 */
    }

    .dark .qr-title-icon {
      color: #a5b4fc;
      /* indigo-300 */
    }

    .dark .qr-reader {
      border-color: #374151;
      /* gray-700 */
      box-shadow: none;
    }

    .dark .qr-result .msg-text {
      color: #e5e7eb;
      /* gray-200 */
    }

    .dark .qr-close-btn {
      background: #374151;
      /* gray-700 */
      color: #e5e7eb;
      /* gray-200 */
    }

    .dark .qr-close-btn:hover {
      background: #4b5563;
      /* gray-600 */
    }

    .dark .qr-retry-btn {
      background: #4f46e5;
      /* indigo-600 */
    }

    .dark .qr-retry-btn:hover {
      background: #4338ca;
      /* indigo-700 */
    }

    .dark .qr-upload-btn {
      background: #10b981;
      /* emerald-500 */
    }

    .dark .qr-upload-btn:hover {
      background: #059669;
      /* emerald-600 */
    }

    .dark .qr-default {
      color: #9ca3af;
      /* gray-400 */
    }
  </style>
</div>
