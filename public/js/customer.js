/* ==========================
   EASY MENU CUSTOMER JS
========================== */

const CART_KEY = 'easymenu_cart';
const EXPIRE_KEY = 'easymenu_expire';

/* ==========================
   CART STORAGE
========================== */

function getCart() {
    return JSON.parse(
        localStorage.getItem(CART_KEY)
    ) || [];
}

function saveCart(cart) {

    localStorage.setItem(
        CART_KEY,
        JSON.stringify(cart)
    );

    localStorage.setItem(
        EXPIRE_KEY,
        Date.now() +
        (12 * 60 * 60 * 1000)
    );

    updateCartBadge();
}

/* ==========================
   CART EXPIRE
========================== */

function checkCartExpiration() {

    const expire =
        localStorage.getItem(
            EXPIRE_KEY
        );

    if (
        expire &&
        Date.now() >
        parseInt(expire)
    ) {

        localStorage.removeItem(
            CART_KEY
        );

        localStorage.removeItem(
            EXPIRE_KEY
        );
    }
}

/* ==========================
   ADD TO CART
========================== */

function addToCart(
    itemId,
    itemName,
    price,
    image
) {

    let cart =
        getCart();

    const existing =
        cart.find(
            item =>
                String(item.item_id) === String(itemId)
        );

    if (existing) {

        existing.quantity++;

    } else {

        cart.push({

            item_id: itemId,

            name: itemName,

            price: parseFloat(price),

            image: image,

            quantity: 1

        });

    }

    saveCart(cart);

    renderCart();

    showToast(
        `${itemName} added`
    );
}

/* ==========================
   QUANTITY
========================== */

function increaseQuantity(
    itemId
) {

    console.log('increaseQuantity called with:', itemId);

    let cart =
        getCart();

    const item =
        cart.find(
            i =>
                String(i.item_id) === String(itemId)
        );

    if (!item) {
        console.log('Item not found:', itemId, 'Cart items:', cart);
        return;
    }

    item.quantity++;

    console.log('New quantity:', item.quantity);

    saveCart(cart);

    renderCart();
}

function decreaseQuantity(
    itemId
) {

    console.log('decreaseQuantity called with:', itemId);

    let cart =
        getCart();

    const item =
        cart.find(
            i =>
                String(i.item_id) === String(itemId)
        );

    if (!item) {
        console.log('Item not found:', itemId, 'Cart items:', cart);
        return;
    }

    item.quantity--;

    console.log('New quantity:', item.quantity);

    if (
        item.quantity <= 0
    ) {

        cart =
            cart.filter(
                i =>
                    String(i.item_id) !== String(itemId)
            );
    }

    saveCart(cart);

    renderCart();
}

/* ==========================
   REMOVE ITEM
========================== */

function removeItem(
    itemId
) {

    let cart =
        getCart();

    cart =
        cart.filter(
            item =>
                String(item.item_id) !== String(itemId)
        );

    saveCart(cart);

    renderCart();
}

/* ==========================
   CART BADGE
========================== */

function updateCartBadge() {

    const cart = getCart();

    const count = cart.reduce(
        (total, item) => total + item.quantity,
        0
    );

    const cartCount =
        document.getElementById('cartCount');

    const cartTotal =
        document.getElementById('cartTotal');

    const total = cart.reduce(
        (sum, item) =>
            sum + (item.price * item.quantity),
        0
    );

    if (cartCount) {

        cartCount.textContent = count;

        if (count <= 0) {
            cartCount.style.display = 'flex';
        } else {
            cartCount.style.display = 'flex';
        }
    }

    if (cartTotal) {
        cartTotal.textContent =
            '$' + total.toFixed(2);
    }
}
/* ==========================
   RENDER CART
========================== */

function renderCart() {

    const container =
        document.getElementById(
            'cartItems'
        );

    if (!container)
        return;

    const cart =
        getCart();

    if (
        cart.length === 0
    ) {

        container.innerHTML = `
            <div class="cart-empty">
                <i class="fa-solid fa-cart-shopping"></i>
                <h3>Your cart is empty</h3>
                <p>Browse the menu and tap + to add your favorite dishes.</p>
            </div>
        `;

        calculateTotal();

        return;
    }

    container.innerHTML =
        cart.map(
            item => `
            <div class="cart-item">
                <img src="${item.image}" class="cart-image" alt="${item.name}">
                <div class="cart-item-main">
                    <div class="cart-info">
                        <h4>${item.name}</h4>
                        <p>$${item.price.toFixed(2)} each</p>
                    </div>
                    <div class="cart-quantity">
                        <button onclick="decreaseQuantity(${item.item_id})">-</button>
                        <span class="quantity-value">${item.quantity}</span>
                        <button onclick="increaseQuantity(${item.item_id})">+</button>
                    </div>
                </div>
                <div class="cart-item-actions">
                    <div class="cart-item-total">
                        $${(item.price * item.quantity).toFixed(2)}
                    </div>
                    <button class="remove-btn" onclick="removeItem(${item.item_id})">
                        Remove
                    </button>
                </div>
            </div>
        `
        ).join('');

    calculateTotal();
}

/* ==========================
   TOTAL
========================== */

function calculateTotal() {

    const cart =
        getCart();

    const total =
        cart.reduce(
            (
                sum,
                item
            ) =>
                sum +
                (
                    item.price *
                    item.quantity
                ),
            0
        );

    const totalElement =
        document.getElementById(
            'orderTotal'
        );

    if (
        totalElement
    ) {

        totalElement.textContent =
            '$' +
            total.toFixed(2);
    }

    const checkoutBtn = document.querySelector('.checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.disabled = total <= 0;
    }

    return total;
}

/* ==========================
   CART INIT
========================== */

document.addEventListener('DOMContentLoaded', function () {
    checkCartExpiration();
    updateCartBadge();
    renderCart();
});

/* ==========================
   CART DRAWER
========================== */

function openCart() {

    document
        .getElementById(
            'cartDrawer'
        )
        .classList.add(
            'show'
        );
}

function closeCart() {

    document
        .getElementById(
            'cartDrawer'
        )
        .classList.remove(
            'show'
        );
}

/* ==========================
   CHECKOUT
========================== */

async function checkout() {

    const cart =
        getCart();

    if (
        cart.length === 0
    ) {

        alert(
            'Cart is empty'
        );

        return;
    }

    try {

        const response =
            await fetch(
                `/checkout/${TABLE_ID}`,
                {
                    method: 'POST',

                    headers: {
                        'Content-Type':
                            'application/json',

                        'X-CSRF-TOKEN':
                            document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .content,
                    },

                    body: JSON.stringify({
                        items: cart
                    })
                }
            );

        const data =
            await response.json();

        if (
            data.success
        ) {

            localStorage.removeItem(
                CART_KEY
            );

            localStorage.removeItem(
                EXPIRE_KEY
            );

            window.location.href =
                `/track-order/${data.order_id}`;
        }

    } catch (error) {

        console.error(
            error
        );

        alert(
            'Checkout failed'
        );
    }
}

/* ==========================
   SEARCH
========================== */

const menuSearchInput =
    document.getElementById('searchInput');

menuSearchInput?.addEventListener(
    'input',
    function () {

        const value =
            this.value
                .toLowerCase();

        document
            .querySelectorAll(
                '.food-card'
            )
            .forEach(
                card => {

                    const text =
                        card.textContent
                            .toLowerCase();

                    card.style.display =
                        text.includes(value)
                            ? ''
                            : 'none';
                }
            );
    }
);

/* ==========================
   CATEGORY
========================== */

document
    .querySelectorAll(
        '.category'
    )
    .forEach(
        button => {

            button.addEventListener(
                'click',
                function () {

                    document
                        .querySelectorAll(
                            '.category'
                        )
                        .forEach(
                            btn =>
                                btn.classList.remove(
                                    'active'
                                )
                        );

                    this.classList.add(
                        'active'
                    );

                    const category =
                        this.dataset.category;

                    document
                        .querySelectorAll(
                            '.card-container'
                        )
                        .forEach(
                            card => {

                                card.style.display =
                                    category === 'all' ||
                                    card.dataset.category === category
                                        ? ''
                                        : 'none';
                            }
                        );
                }
            );
        }
    );

/* ==========================
   TOAST
========================== */

function showToast(
    message
) {

    const toast =
        document.createElement(
            'div'
        );

    toast.innerText =
        message;

    toast.style.cssText = `
        position:fixed;
        top:20px;
        right:20px;
        background:#22C55E;
        color:white;
        padding:12px 18px;
        border-radius:12px;
        z-index:99999;
    `;

    document.body.appendChild(
        toast
    );

    setTimeout(
        () =>
            toast.remove(),
        2500
    );
}

/* ==========================
   INIT
========================== */

document.addEventListener(
    'DOMContentLoaded',
    () => {

        checkCartExpiration();

        updateCartBadge();

        renderCart();
    }
);

window.addToCart = addToCart;
window.increaseQuantity = increaseQuantity;
window.decreaseQuantity = decreaseQuantity;
window.openCart = openCart;
window.closeCart = closeCart;
