
<div class="footer">
    <div class="bottom-nav">

        <a href="{{ route('menu.table', $table) }}" class="nav-item active">
            <i class="fa-solid fa-book-open"></i>
            <span>Menu</span>
        </a>

        <a href="{{ route('cart.show', $table ?? 1) }}" class="nav-item">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>Cart</span>
        </a>

        <a href="{{ route('orders.index') }}" class="nav-item">
            <i class="fa-solid fa-receipt"></i>
            <span>Orders</span>
        </a>
    </div>
</div>

<script>
const items = document.querySelectorAll('.nav-item');

items.forEach(item=>{
    item.addEventListener('click',()=>{
        items.forEach(nav=>{
            nav.classList.remove('active');
        });

        item.classList.add('active');
    });
});
</script>

