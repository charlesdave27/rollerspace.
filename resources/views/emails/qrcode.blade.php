<x-mail::message>
  # Your QR Code

  Scan this QR code to proceed.

  @isset($qrCodePath)
    <p>
      <img src="{{ asset('storage/' . $qrCodePath) }}" alt="QR code" width="300" height="300">
    </p>
  @endisset

  Thanks,<br>
  {{ config('app.name') }}
</x-mail::message>
