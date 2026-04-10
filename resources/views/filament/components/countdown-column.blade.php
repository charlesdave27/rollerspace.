@php
  $timestamp = 0;
  if ($getRecord() && $getRecord()->deadline) {
      $timestamp = $getRecord()->deadline->timestamp * 1000;
  }
@endphp

<div x-data="{
    deadline: {{ $timestamp }},
    time: {{ $timestamp ? "'Loading...'" : "'Unlimited'" }},
    color: {{ $timestamp ? "'black'" : "'gray'" }},
    timer: null,
    start() {
        if (this.deadline === 0) {
            this.time = 'Unlimited';
            this.color = 'gray';
            return;
        }
        const update = () => {
            const now = new Date().getTime();
            const remaining = this.deadline - now;

            if (remaining <= 0) {
                this.time = 'Expired';
                this.color = 'red';
                clearInterval(this.timer);
                this.timer = null;
                return;
            }

            const seconds = Math.floor((remaining / 1000) % 60);
            const minutes = Math.floor((remaining / 1000 / 60) % 60);
            const hours = Math.floor((remaining / (1000 * 60 * 60)) % 24);
            const days = Math.floor(remaining / (1000 * 60 * 60 * 24));

            this.time = `${days > 0 ? days + 'd ' : ''}${String(hours).padStart(2,'0')}h ${String(minutes).padStart(2,'0')}m ${String(seconds).padStart(2,'0')}s`;

            if (remaining <= 10 * 60 * 1000) {
                this.color = 'orange';
            } else {
                this.color = 'green';
            }
        };

        update();
        this.timer = setInterval(update, 1000);
    }
}" x-init="start()" x-text="time" :style="`color: ${color}`"
  class="font-mono text-sm font-semibold"></div>
