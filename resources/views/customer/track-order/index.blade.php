@extends('layouts.customer')

@section('content')

@php
    $steps = [
        [
            'title' => 'Pending',
            'subtitle' => 'Order received at ' . $order->created_at->format('H:i'),
            'icon' => 'fa-clock',
        ],
        [
            'title' => 'Accepted',
            'subtitle' => 'Kitchen accepted at ' . ($order->updated_at?->format('H:i') ?? ''),
            'icon' => 'fa-check',
        ],
        [
            'title' => 'Preparing',
            'subtitle' => 'Our chefs are crafting your meal.',
            'icon' => 'fa-utensils',
        ],
        [
            'title' => 'Completed',
            'subtitle' => 'Estimated in 5 mins.',
            'icon' => 'fa-bell',
        ],
        [
            'title' => 'Finished',
            'subtitle' => 'The final step.',
            'icon' => 'fa-flag-checkered',
        ],
    ];

    $statusOrder = [
        'pending' => 0,
        'accepted' => 1,
        'preparing' => 2,
        'completed' => 3,
        'finished' => 4,
        'cancelled' => 0,
    ];

    $currentStep = $statusOrder[$order->status] ?? 0;
    $itemCount = $order->items->sum('quantity');
@endphp

<div class="track-page">

    <div class="track-card">

        <div class="track-header" id="track-header" data-order-id="{{ $order->id }}" data-order-status="{{ $order->status }}">
            <div>
                <span class="track-label">ORDER NUMBER</span>
                <h1 id="order-number">#{{ $order->id }}</h1>
            </div>

            <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                <span class="status-badge status-{{ $order->status }}" id="header-status-badge">
                    {{ ucfirst($order->status) }}
                </span>
                <div>
                    <span class="track-label">ESTIMATED TIME</span>
                    <p id="estimated-time">
                        @if($order->status === 'pending')
                            10 នាទី
                        @elseif($order->status === 'preparing')
                            15 នាទី
                        @else
                            5 នាទី
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="track-body">

            <div class="track-summary">
                <div class="summary-box">
                    <div class="summary-icon">
                        <i class="fa-solid fa-box"></i>
                    </div>
                    <div class="summary-title">Items Ordered</div>
                    <div class="summary-value" id="item-count">{{ $itemCount }} items</div>
                </div>

                <div class="summary-box">
                    <div class="summary-icon">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div class="summary-title">Total Bill</div>
                    <div class="summary-value" id="order-total">${{ number_format($order->total, 2) }}</div>
                </div>

                <div class="summary-box">
                    <div class="summary-icon">
                        <i class="fa-solid fa-shop"></i>
                    </div>
                    <div class="summary-title">Dining Option</div>
                    <div class="summary-value">Dine-In</div>
                </div>
            </div>

            <div class="section-title">
                <h3>Order Progress</h3>
                <span id="live-indicator"><span class="pulse-dot"></span> Live Tracking</span>
            </div>

            <ol class="progress-list" id="progress-list">
                @foreach($steps as $index => $step)
                    @php
                        $state = 'disabled';
                        if ($index < $currentStep) {
                            $state = 'served';
                        } elseif ($index === $currentStep) {
                            $state = 'active';
                        }

                        $time = '';
                        if ($index === 0) {
                            $time = $order->created_at->format('h:i A');
                        } elseif ($index <= $currentStep) {
                            $time = $order->updated_at?->format('h:i A') ?: '';
                        }
                    @endphp

                    <li class="progress-step {{ $state }}" data-step-index="{{ $index }}">
                        <div class="step-marker">
                            <span class="step-icon">
                                <i class="fa-solid {{ $step['icon'] }}"></i>
                            </span>
                        </div>

                        <div class="step-details">
                            <strong>{{ $step['title'] }}</strong>
                            <p>{{ $step['subtitle'] }}</p>
                            <span class="step-time" @if(empty($time)) style="display:none;" @endif>{{ $time }}</span>
                        </div>
                    </li>
                @endforeach
            </ol>

            <div class="contact-cta">
                <div style="flex:1">
                    <strong>Need something?</strong>
                    <div style="color:#64748B; font-size:14px">Our team is here to assist you.</div>
                </div>
                <div>
                    <button id="contact-staff">
                        <i class="fa-solid fa-bell-concierge" style="margin-right: 8px;"></i>CONTACT STAFF
                    </button>
                </div>
            </div>

        </div>

    </div>

</div>

<script>
    (function() {
        const orderId = document.getElementById('track-header').dataset.orderId;
        const statusUrl = '{{ url("/order-status") }}' + '/' + orderId;
                const statusOrder = { pending: 0, accepted: 1, preparing: 2, completed: 3, finished: 4, cancelled: 0 };

        function updateEstimatedTime(status) {
            const el = document.getElementById('estimated-time');
            if (!el) return;
            if (status === 'pending') el.textContent = '10 នាទី';
            else if (status === 'preparing') el.textContent = '15 នាទី';
            else el.textContent = '5 នាទី';
        }

        function applyProgress(status) {
            const current = statusOrder[status] ?? 0;
            const steps = document.querySelectorAll('#progress-list .progress-step');
            steps.forEach(li => {
                const idx = parseInt(li.dataset.stepIndex, 10);
                li.classList.remove('served', 'active', 'disabled');
                if (idx < current) li.classList.add('served');
                else if (idx === current) li.classList.add('active');
                else li.classList.add('disabled');
            });

            // Dynamically update upper-right header badge class and text
            const badge = document.getElementById('header-status-badge');
            if (badge) {
                badge.className = 'status-badge status-' + status;
                badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
            }
        }

        function formatTime(iso) {
            if (!iso) return '';
            const d = new Date(iso);
            if (Number.isNaN(d.getTime())) return '';
            const h = d.getHours();
            const m = d.getMinutes().toString().padStart(2, '0');
            const ampm = h >= 12 ? 'PM' : 'AM';
            const hh = ((h + 11) % 12) + 1;
            return hh + ':' + m + ' ' + ampm;
        }

        async function fetchStatus() {
            try {
                const res = await fetch(statusUrl, { cache: 'no-store' });
                if (!res.ok) return;
                const data = await res.json();

                const header = document.getElementById('track-header');
                if (header) header.dataset.orderStatus = data.status;

                const itemCountEl = document.getElementById('item-count');
                if (itemCountEl && data.items_count) itemCountEl.textContent = data.items_count + ' items';

                const totalEl = document.getElementById('order-total');
                if (totalEl && data.total) totalEl.textContent = '$' + parseFloat(data.total).toFixed(2);

                const createdAt = data.created_at;
                const updatedAt = data.updated_at;

                const steps = document.querySelectorAll('#progress-list .progress-step');
                const current = statusOrder[data.status] ?? 0;
                steps.forEach(li => {
                    const idx = parseInt(li.dataset.stepIndex, 10);
                    const timeEl = li.querySelector('.step-time');
                    if (!timeEl) return;

                    let timeStr = '';
                    if (idx === 0) timeStr = formatTime(createdAt);
                    else if (idx <= current) timeStr = formatTime(updatedAt);

                    if (timeStr) {
                        timeEl.textContent = timeStr;
                        timeEl.style.display = 'inline-flex';
                    } else {
                        timeEl.style.display = 'none';
                    }
                });

                updateEstimatedTime(data.status);
                applyProgress(data.status);
            } catch (e) {
                // Network errors fail silently
            }
        }

        setTimeout(fetchStatus, 1000);
        setInterval(fetchStatus, 5000);
    })();
</script>
@endsection
