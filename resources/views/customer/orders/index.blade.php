@extends('layouts.customer')

@section('content')

<div class="track-page">

    <div class="track-card">

        <div class="order-list-header">
            <div>
                <h1>My Orders</h1>
                <p>View recent orders and track status easily.</p>
            </div>
            <a href="{{ route('menu.table', $table) }}" class="checkout-btn new-order-btn">
                <i class="fa-solid fa-plus"></i>
                New Order
            </a>
        </div>

        @if($orders->isEmpty())
            <div class="empty-state">
                <i class="fa-solid fa-receipt"></i>
                <h3>No orders yet</h3>
                <p>Start by adding items to the cart.</p>
            </div>
        @else
            <div class="customer-orders">
                @foreach($orders as $order)
                    <a href="{{ route('track.order', $order) }}" class="order-card">
                        <div class="order-card-main">
                            <div>
                                <div class="order-label">Order #{{ $order->id }}</div>
                                <div class="order-meta">Table {{ $order->table->name ?? '-' }}</div>
                            </div>
                            <div class="order-status status-{{ $order->status }}">
                                {{ ucfirst($order->status) }}
                            </div>
                        </div>

                        <div class="order-card-details">
                            <span>{{ $order->items->sum('quantity') }} items</span>
                            <strong>${{ number_format($order->total, 2) }}</strong>
                        </div>

                        <div class="order-card-footer">
                            <span>{{ $order->created_at->format('M d, Y H:i') }}</span>
                            <span>Track</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

    </div>

</div>

@endsection
