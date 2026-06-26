@extends('layouts.customer')
@section('content')
<main class="menu-container">
    <!-- Search -->
    <div class="search-box">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="searchInput" placeholder="ស្វែងរកម្ហូប...">
    </div>

    <!-- Categories -->
    <div class="categories">
        <button class="category active" data-category="all">គ្រប់មុខ</button>
        @foreach($categories as $category)
            <button class="category"  data-category="{{ $category->id }}">
                {{ $category->name }}
            </button>
        @endforeach
    </div>

    <!-- Section Header -->
    <div class="section-header">
        <div class="left">
            <i class="fa-solid fa-star"></i>
            <span>ម្ហូបពេញនិយម</span>
        </div>

        <a href="#">
            មើលទាំងអស់
            <i class="fa-solid fa-arrow-right"></i>
        </a>
    </div>

    <!-- Products -->
    <div class="product-grid">
        @foreach($items as $item)
        <!-- Product Card -->
        <div class="card-container" data-category="{{ $item->category_id }}">
            <div class="food-card">
                    <div class="food-image-wrapper">
                        @if($item->image !== null)
                            <img src="{{ Storage::disk(config('filesystems.default'))->url($item->image) }}" alt="{{ $item->name }}"
                            class="food-image">
                        @else
                            <div class="no-image">
                                <div class="icon-wrapper">
                                    <i class="fa-solid fa-bowl-food"></i>
                                </div>
                            </div>
                        @endif

                        <span class="price-badge">${{ number_format($item->price, 2) }}</span>
                    </div>

                    <div class="food-content">
                        <h3>{{ $item->name }}</h3>

                        <p>
                            {{ $item->description }}
                        </p>


                    </div>

            </div>
                <button
                    class="add-cart-btn"
                    data-id="{{ $item->id }}"
                    data-name="{{ $item->name }}"
                    data-price="{{ $item->price }}"
                    data-image="{{ $item->image ? Storage::disk(config('filesystems.default'))->url($item->image) : '' }}"
                >
                    <i class="fa-solid fa-cart-plus"></i>
                    បន្ថែមទៅកន្ត្រក
                </button>
        </div>

        @endforeach
    </div>

    <!-- Drinks -->
    <h2 class="drink-title">
        <i class="fa-solid fa-mug-hot"></i>
        ភេសជ្ជៈពេញនិយម
    </h2>

    <div class="drink-list">

        <div class="drink-item">
            <img src="https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=500">
            <h4>កាហ្វេទឹកដោះ</h4>
            <span>$1.50</span>
        </div>

        <div class="drink-item">
            <img src="https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500">
            <h4>ម៉ូហ៊ីតូ</h4>
            <span>$1.25</span>
        </div>

        <div class="drink-item">
            <img src="https://images.unsplash.com/photo-1497534446932-c925b458314e?w=500">
            <h4>ទឹកក្រូច</h4>
            <span>$1.00</span>
        </div>

    </div>

</main>
<script>
const categoryButtons = document.querySelectorAll('.category');
const searchInput = document.querySelector('.search-box input');

categoryButtons.forEach(button => {
    button.addEventListener('click', () => {

        categoryButtons.forEach(btn =>
            btn.classList.remove('active')
        );

        button.classList.add('active');

        const selectedCategory =
            button.dataset.category;

        const foodCards =
            document.querySelectorAll('.food-card');

        foodCards.forEach(card => {

            const cardCategory =
                card.dataset.category;

            if (
                selectedCategory === 'all' ||
                cardCategory === selectedCategory
            ) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});

searchInput.addEventListener('input', () => {

    const query = searchInput.value.toLowerCase();

    document.querySelectorAll('.food-card')
        .forEach(card => {

            const name =
                card.querySelector('h3')
                    .textContent
                    .toLowerCase();

            const description =
                card.querySelector('p')
                    .textContent
                    .toLowerCase();

            if (
                name.includes(query) ||
                description.includes(query)
            ) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
});

document.querySelectorAll('.add-cart-btn')
.forEach(button => {

    button.addEventListener('click', function () {

        addToCart(
            this.dataset.id,
            this.dataset.name,
            this.dataset.price,
            this.dataset.image
        );

    });

});
</script>
@endsection


