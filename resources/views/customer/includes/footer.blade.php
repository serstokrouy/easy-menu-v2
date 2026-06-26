
<div class="footer">
    <div class="bottom-nav">

        <a href="{{ route('menu.table', isset($table) ? $table : 1) }}" class="nav-item active">
            <i class="fa-solid fa-book-open"></i>
            <span>Menu</span>
        </a>

        <a href="{{ route('cart.show', isset($table) ? $table : 1) }}" class="nav-item">
            <i class="fa-solid fa-cart-shopping"></i>
            <span>Cart</span>
        </a>

        <a href="{{ route('customer.orders', isset($table) ? $table : 1) }}" class="nav-item">
            <i class="fa-solid fa-receipt"></i>
            <span>Orders</span>
        </a>
    </div>
</div>

<script>
const items = document.querySelectorAll('.nav-item');
const currentPath = window.location.pathname;

items.forEach(item=>{
    const anchor = item.getAttribute('href');

    if (anchor && currentPath.startsWith(anchor)) {
        item.classList.add('active');
    }

    item.addEventListener('click',()=>{
        items.forEach(nav=>{
            nav.classList.remove('active');
        });

        item.classList.add('active');
    });
});
</script>

