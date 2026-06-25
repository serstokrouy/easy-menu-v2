@extends('layouts.customer')

@section('content')

<div class="cart-page">

    <div class="cart-header-page">
        <h2>
            <i class="fa-solid fa-cart-shopping"></i>
            កន្ត្រករបស់អ្នក
        </h2>
    </div>

    <div id="cartItems" class="cart-items">
        <!-- Render from JS -->
    </div>

    <div class="cart-summary">

        <div class="summary-row">
            <span>សរុប</span>
            <strong id="orderTotal">$0.00</strong>
        </div>

        <button
            class="checkout-btn"
            onclick="checkout()">
            <i class="fa-solid fa-credit-card"></i>
            បញ្ជាទិញ
        </button>

    </div>

</div>

@endsection
