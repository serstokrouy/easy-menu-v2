@extends('layouts.app')

@section('content')
<div class="order-detail-page">
    <div class="detail-header">
        <button type="button" class="icon-btn back-btn" onclick="window.history.back()">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <div class="header-content">
            <p class="subheading">Table Invoice</p>
            <h1>Table {{ $table->name }} Invoice</h1>
        </div>
        <div class="header-actions">
            <button type="button" class="icon-btn" onclick="window.print()" title="Print Invoice">
                <i class="fa-solid fa-print"></i>
            </button>
        </div>
    </div>

    <div class="invoice-card screen-only">
        <div class="invoice-summary">
            <div>
                <div class="invoice-label">Table</div>
                <strong>{{ $table->name }}</strong>
            </div>
            <div>
                <div class="invoice-label">Status</div>
                <span class="badge {{ $table->status }}">{{ ucfirst($table->status) }}</span>
            </div>
            <div>
                <div class="invoice-label">Date</div>
                <strong>{{ now()->format('d M Y H:i') }}</strong>
            </div>
        </div>

        <div class="orders-list">
            @foreach($table->orders as $order)
                <div class="invoice-order-card">
                    <div class="invoice-order-header">
                        <div>
                            <span class="order-chip">Order #{{ $order->id }}</span>
                            <span class="order-status {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        </div>
                        <div><strong>${{ number_format($order->total, 2) }}</strong></div>
                    </div>
                    <div class="invoice-order-meta">
                        <span>{{ $order->items->sum('quantity') }} items</span>
                        <span>{{ $order->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="invoice-order-items">
                        @foreach($order->items as $item)
                            <div class="invoice-item-row">
                                <span>{{ $item->item->name }} x{{ $item->quantity }}</span>
                                <strong>${{ number_format($item->price * $item->quantity, 2) }}</strong>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="invoice-total-box">
            <div class="invoice-total-row">
                <span>Orders Total</span>
                <strong>${{ number_format($table->orders->sum('total'), 2) }}</strong>
            </div>
            <div class="invoice-total-row">
                <span>Tax (10%)</span>
                <strong>${{ number_format($table->orders->sum('total') * 0.1, 2) }}</strong>
            </div>
            <div class="invoice-total-row grand-total">
                <span>Grand Total</span>
                <strong>${{ number_format($table->orders->sum('total') * 1.1, 2) }}</strong>
            </div>
        </div>
    </div>

    <div class="receipt-container print-only">
        <div class="receipt">
            <div class="receipt-header">
                <h1>EASY-MENU</h1>
                <p>#54, Preah Sihanouk Blvd, Phnom Penh</p>
                <p>Tel: +855 23 999 888</p>
            </div>

            <div class="receipt-divider"></div>

            <div class="receipt-info">
                <div class="info-row">
                    <span>Invoice</span>
                    <span>#INV-{{ now()->format('Ymd') }}-{{ $table->id }}</span>
                </div>
                <div class="info-row">
                    <span>Date</span>
                    <span>{{ now()->format('m/d/Y, h:i:s A') }}</span>
                </div>
                <div class="info-row">
                    <span>Table</span>
                    <span>{{ $table->name }}</span>
                </div>
                <div class="info-row">
                    <span>Status</span>
                    <span>{{ ucfirst($table->status) }}</span>
                </div>
                <div class="info-row">
                    <span>Payment</span>
                    <span>CASH (USD)</span>
                </div>
            </div>

            <div class="receipt-divider"></div>

            <div class="receipt-items">
                @foreach($table->orders as $order)
                    @foreach($order->items as $item)
                        <div class="receipt-item">
                            <div class="receipt-item-name">{{ $item->item->name }}</div>
                            <div class="receipt-item-price">${{ number_format($item->price * $item->quantity, 2) }}</div>
                        </div>
                        <div class="receipt-item-qty">{{ $item->quantity }} x ${{ number_format($item->price, 2) }}</div>
                    @endforeach
                @endforeach
            </div>

            <div class="receipt-divider"></div>

            <div class="receipt-totals">
                <div class="receipt-total-row">
                    <span>Subtotal (USD)</span>
                    <span>${{ number_format($table->orders->sum('total'), 2) }}</span>
                </div>
                <div class="receipt-total-row">
                    <span>Tax (10%)</span>
                    <span>${{ number_format($table->orders->sum('total') * 0.1, 2) }}</span>
                </div>
                <div class="receipt-total-row grand-total">
                    <span>Total (USD)</span>
                    <span>${{ number_format($table->orders->sum('total') * 1.1, 2) }}</span>
                </div>
            </div>

            <div class="receipt-divider"></div>

            <div class="receipt-footer">
                <p>Thank you for your business!</p>
                <p>Sabay POS System v1.0.0</p>
            </div>
        </div>
    </div>
</div>
@endsection
