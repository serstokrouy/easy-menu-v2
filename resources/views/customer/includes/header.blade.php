<div class="header">
    <div class="header-card">
        <div class="left-side">
            <div class="logo">
                <img src="{{ asset('assets/web-logo.png') }}" alt="Logo">
            </div>

            <div class="header-title">
                <h1 class="title">EASY-MENU</h1>
            </div>
        </div>

        <div class="center-side">
            <span class="table-badge">
                Table {{ isset($table) ? $table->id : '01' }}
            </span>
        </div>

        <div class="right-side">
           <a href="{{ route('cart.show', isset($table) ? $table : 1) }}"
            class="cart-btn">

                <i class="fa-solid fa-cart-shopping"></i>

                <span id="cartCount" class="cart-count">
                    0
                </span>

            </a>
        </div>

    </div>
</div>
