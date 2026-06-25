@extends('layouts.customer')

@section('content')

<div class="track-page">

    <div class="track-card">

        <div style="text-align:center">

            <i
                class="fa-solid fa-circle-check"
                style="
                    font-size:80px;
                    color:#22C55E;
                "
            ></i>

            <h1>
                Order Placed Successfully
            </h1>

            <p>
                Thank you for your order.
            </p>

        </div>

        <hr>

        <div class="item-row">
            <span>Order No</span>
            <strong>
                #{{ $order->id }}
            </strong>
        </div>

        <div class="item-row">
            <span>Table</span>
            <strong>
                {{ $order->table->name }}
            </strong>
        </div>

        <div class="item-row">
            <span>Status</span>

            <span class="status pending">
                Pending
            </span>
        </div>

        <div class="order-total">

            Total:
            ${{ number_format($order->total,2) }}

        </div>

        <a
            href="{{ route('track.order',$order) }}"
            class="checkout-btn"
            style="
                display:block;
                text-align:center;
                text-decoration:none;
            "
        >
            Track Order
        </a>

    </div>

</div>

@endsection
