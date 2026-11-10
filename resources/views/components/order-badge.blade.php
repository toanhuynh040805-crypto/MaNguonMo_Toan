@props(['status'])
@php
    $statusColor = match($status) {
        'pending' => 'warning',
        'confirmed' => 'info',
        'shipping' => 'primary',
        'completed' => 'success',
        'cancelled' => 'secondary',
        default => 'light',
    };
@endphp
<span class="badge rounded-pill text-bg-{{ $statusColor }} px-3 py-2">{{ \App\Models\Order::STATUSES[$status] ?? $status }}</span>
