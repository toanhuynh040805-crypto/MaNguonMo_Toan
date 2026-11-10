@props(['histories', 'showActor' => false])
@if($histories->isEmpty())
    <p class="text-muted small mb-0">Chưa có lịch sử chi tiết (đơn cũ tạo trước khi bổ sung chức năng này).</p>
@else
    <ol class="order-timeline">
        @foreach($histories as $history)
            <li>
                <span class="timeline-indicator" aria-hidden="true"></span>
                <div class="d-flex flex-wrap justify-content-between gap-2">
                    <strong>{{ $history->status_label }}</strong>
                    <time class="small text-muted" datetime="{{ $history->created_at->toIso8601String() }}">{{ $history->created_at->format('d/m/Y H:i:s') }}</time>
                </div>
                @if($history->from_status)
                    <p class="small text-muted mb-1">Từ {{ \App\Models\Order::STATUSES[$history->from_status] ?? $history->from_status }}</p>
                @else
                    <p class="small text-muted mb-1">Khởi tạo đơn hàng</p>
                @endif
                @if($showActor && $history->actor)
                    <div class="small text-muted">Thao tác bởi: {{ $history->actor->name }}</div>
                @endif
            </li>
        @endforeach
    </ol>
@endif
