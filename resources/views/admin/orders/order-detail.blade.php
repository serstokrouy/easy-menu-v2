@extends('layouts.app')

@section('content')
<div class="order-detail-page">
    <!-- Header -->
    <div class="detail-header">
        <button type="button" class="icon-btn back-btn" onclick="window.history.back()">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <div class="header-content">
            <p class="subheading">Order Details</p>
            <h1>Order #{{ $order->id }}</h1>
        </div>
        <div class="header-actions">
            <button type="button" class="icon-btn" onclick="window.print()" title="Print Order">
                <i class="fa-solid fa-print"></i>
            </button>
        </div>
    </div>

    <div class="detail-grid">
        <!-- Order Summary Card -->
        <div class="detail-card order-summary">
            <div class="card-header">
                <h2>Order Summary</h2>
                <span class="badge {{ $order->status }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <div class="summary-items">
                <div class="summary-row">
                    <span>Order Number</span>
                    <strong>#{{ $order->id }}</strong>
                </div>

                <div class="summary-row">
                    <span>Table</span>
                    <strong>{{ $order->table->name }}</strong>
                </div>

                <div class="summary-row">
                    <span>Created At</span>
                    <strong>{{ $order->created_at->format('d M Y, H:i') }}</strong>
                </div>

                <div class="summary-row">
                    <span>Items Count</span>
                    <strong>{{ $order->items->count() }}</strong>
                </div>

                <div class="summary-row total-amount">
                    <span>Total Amount</span>
                    <strong>${{ number_format($order->total, 2) }}</strong>
                </div>
            </div>

            <div class="status-section">
                <h3>Change Status</h3>
                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="status-form" data-order-id="{{ $order->id }}">
                    @csrf
                    @method('PATCH')

                    <div class="status-buttons">
                        @foreach(['pending' => 'Pending', 'accepted' => 'Accepted', 'preparing' => 'Preparing', 'completed' => 'Completed', 'finished' => 'Finished', 'cancelled' => 'Cancelled'] as $statusValue => $statusLabel)
                            <button type="button" class="status-btn {{ $order->status == $statusValue ? 'active' : '' }}" data-status="{{ $statusValue }}">
                                @if($statusValue === 'pending')
                                    <i class="fa-solid fa-clock"></i>
                                @elseif($statusValue === 'accepted')
                                    <i class="fa-solid fa-check"></i>
                                @elseif($statusValue === 'preparing')
                                    <i class="fa-solid fa-fire"></i>
                                @elseif($statusValue === 'completed')
                                    <i class="fa-solid fa-check-circle"></i>
                                @elseif($statusValue === 'finished')
                                    <i class="fa-solid fa-flag-checkered"></i>
                                @else
                                    <i class="fa-solid fa-ban"></i>
                                @endif
                                {{ $statusLabel }}
                            </button>
                        @endforeach
                    </div>

                    <input type="hidden" name="status" id="statusInput" value="{{ $order->status }}">
                    <button type="submit" class="btn-primary full-width">Update Status</button>
                </form>
            </div>
        </div>

        <!-- Order Items -->
        <div class="detail-card order-items">
            <div class="card-header">
                <h2>Items Ordered</h2>
            </div>

            <div class="items-list">
                @foreach($order->items as $item)
                    <div class="item-row">
                        <div class="item-info">
                            <div class="item-image">
                                @if($item->item->image)
                                    <img src="{{ asset('storage/' . $item->item->image) }}" alt="{{ $item->item->name }}">
                                @else
                                    <div class="item-image-fallback">
                                        <i class="fa-solid fa-bowl-food"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="item-details">
                                <h4>{{ $item->item->name }}</h4>
                                <p>{{ optional($item->item->category)->name }}</p>
                            </div>
                        </div>

                        <div class="item-quantity">
                            <span class="qty-badge">x{{ $item->quantity }}</span>
                        </div>

                        <div class="item-price">
                            <div class="unit-price">${{ number_format($item->price, 2) }}</div>
                            <div class="total-price">${{ number_format($item->price * $item->quantity, 2) }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="items-footer">
                <div class="total-row">
                    <span>Subtotal</span>
                    <strong>${{ number_format($order->total, 2) }}</strong>
                </div>
                <div class="total-row grand-total">
                    <span>Grand Total</span>
                    <strong>${{ number_format($order->total, 2) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Receipt (Hidden on Screen, Shown on Print) -->
    <div class="receipt-container">
        <div class="receipt">
            <!-- Header -->
            <div class="receipt-header">
                <h1>Easy Menu</h1>
                <p>#54, Preah Sihanouk Blvd, Phnom Penh</p>
                <p>Tel: +855 23 999 888</p>
            </div>

            <div class="receipt-divider"></div>

            <!-- Invoice Info -->
            <div class="receipt-info">
                <div class="info-row">
                    <span>Invoice No:</span>
                    <span>INV-{{ date('Ymd') }}-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="info-row">
                    <span>Date/Time:</span>
                    <span>{{ $order->created_at->format('m/d/Y, h:i:s A') }}</span>
                </div>
                <div class="info-row">
                    <span>Manager:</span>
                    <span>Admin</span>
                </div>
                <div class="info-row">
                    <span>Customer:</span>
                    <span>General Customer</span>
                </div>
                <div class="info-row">
                    <span>Payment:</span>
                    <span>CASH (USD)</span>
                </div>
            </div>

            <div class="receipt-divider"></div>

            <!-- Items -->
            <div class="receipt-items">
                @foreach($order->items as $item)
                    <div class="receipt-item">
                        <div class="receipt-item-name">
                            {{ $item->item->name }}
                            <span class="receipt-item-qty">x{{ $item->quantity }}</span>
                        </div>
                        <div class="receipt-item-price">
                            ${{ number_format($item->price * $item->quantity, 2) }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="receipt-divider"></div>

            <!-- Totals -->
            <div class="receipt-totals">
                <div class="receipt-total-row">
                    <span>Subtotal (USD):</span>
                    <span>${{ number_format($order->total, 2) }}</span>
                </div>
                <div class="receipt-total-row">
                    <span>Tax (10%):</span>
                    <span>${{ number_format($order->total * 0.1, 2) }}</span>
                </div>
                <div class="receipt-total-row grand-total">
                    <span>Total (USD):</span>
                    <span>${{ number_format($order->total * 1.1, 2) }}</span>
                </div>
                <div class="receipt-total-row">
                    <span>Total (KHR):</span>
                    <span>៛{{ number_format($order->total * 1.1 * 4110, 0) }}</span>
                </div>
                <div class="receipt-total-row">
                    <span>Received (USD):</span>
                    <span>${{ number_format($order->total * 1.1 + 1, 2) }}</span>
                </div>
                <div class="receipt-total-row">
                    <span>Change (USD):</span>
                    <span>${{ number_format(1, 2) }}</span>
                </div>
            </div>

            <div class="receipt-divider"></div>

            <!-- Footer -->
            <div class="receipt-footer">
                <p>Thank you for your order!</p>
                <p>Please visit us again!</p>
                <p style="font-size: 0.8em; margin-top: 10px;">Easy POS System v1.0.0</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const statusForm = document.querySelector('.status-form');
        const statusButtons = document.querySelectorAll('.status-btn');
        const statusInput = document.getElementById('statusInput');
        const currentStatus = '{{ $order->status }}';

        statusButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                statusButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                statusInput.value = this.dataset.status;
            });
        });

        if (statusForm) {
            statusForm.addEventListener('submit', async function (event) {
                event.preventDefault();

                const newStatus = statusInput.value;
                const submitButton = this.querySelector('.btn-primary');
                const originalText = submitButton.textContent;

                submitButton.disabled = true;
                submitButton.textContent = 'Updating...';

                try {
                    const response = await fetch(this.action, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ status: newStatus }),
                    });

                    if (!response.ok) {
                        throw new Error('Failed to update status');
                    }

                    const data = await response.json();

                    const badgeElement = document.querySelector('.card-header .badge');
                    badgeElement.className = `badge ${newStatus}`;
                    badgeElement.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);

                    submitButton.textContent = 'Status Updated!';

                    if (newStatus === 'finished') {
                        announceOrderCompletion('Order #{{ $order->id }}, Table {{ $order->table->name }} is finished. Please proceed to the next order.');
                    }

                    setTimeout(() => {
                        submitButton.textContent = originalText;
                        submitButton.disabled = false;
                    }, 3000);
                } catch (error) {
                    console.error('Error:', error);
                    alert('Failed to update order status. Please try again.');
                    submitButton.textContent = originalText;
                    submitButton.disabled = false;
                }
            });
        }

        function announceOrderCompletion(message) {
            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(message);
                utterance.rate = 1.0;
                utterance.pitch = 1.0;
                speechSynthesis.speak(utterance);
            }
        }
    });
</script>

<style media="print">
    .detail-header,
    .status-section,
    .header-actions {
        display: none !important;
    }

    .detail-grid {
        grid-template-columns: 1fr !important;
    }

    .detail-card {
        page-break-inside: avoid;
        box-shadow: none !important;
        border: 1px solid #E2E8F0;
    }
</style>

@endsection

