@extends('layouts.app')

@section('content')
<div class="admin-menu-page">
    <div class="menu-header">
        <div class="menu-header-left">
            <h1>Menu Management</h1>
        </div>
    </div>

    <div class="menu-filters">
        <button class="menu-filter active" data-filter="all">All Items</button>

        @foreach($categories->take(2) as $category)
            <button class="menu-filter" data-filter="category-{{ $category->id }}">
                {{ $category->name }}
            </button>
        @endforeach

        <button class="menu-filter" data-filter="offline">Offline</button>
    </div>

    @php
        $itemList = isset($items) ? collect($items->items()) : collect();
        $availableCount = $itemList->where('is_available', true)->count();
        $offlineCount = $itemList->where('is_available', false)->count();
    @endphp

    <div class="menu-summary-grid">
        <article class="summary-card">
            <span>Total Menu Items</span>
            <h2>{{ $items->total() }}</h2>
        </article>

        <article class="summary-card success-card">
            <span>Live Now</span>
            <h2>{{ $availableCount }} Dishes</h2>
        </article>

        <article class="summary-card danger-card">
            <span>Out of Stock</span>
            <h2>{{ $offlineCount }} Items</h2>
        </article>
    </div>

    <div class="menu-list">
        @foreach($items as $item)
            <article class="menu-item-card" data-category="category-{{ $item->category_id }}" data-available="{{ $item->is_available ? 'live' : 'unavailable' }}">
                <div class="item-image-wrap">
                    @if($item->image)
                        @if($item->is_available)
                            <img src="{{ Storage::disk(config('filesystems.default'))->url($item->image) }}" alt="{{ $item->name }}">
                        @else
                            <div class="item-image-unavailable">
                                <img src="{{ Storage::disk(config('filesystems.default'))->url($item->image) }}" alt="{{ $item->name }}">
                                <span class="offline-badge">Unavailable</span>
                            </div>
                        @endif
                    @else
                        <div class="item-image-fallback">
                            <i class="fa-solid fa-bowl-food"></i>
                        </div>
                    @endif

                    <div class="item-actions">
                        <a href="{{ route('items.edit', $item) }}" class="icon-btn small">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                        <form action="{{ route('items.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this item?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="icon-btn small delete-btn">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </div>

                </div>

                <div class="item-detail">
                    <div class="item-top-row">
                        <div>
                            <h2>{{ $item->name }}</h2>
                            <p>{{ \Illuminate\Support\Str::limit($item->description, 70) }}</p>
                        </div>
                        <div class="item-price">
                            ${{ number_format($item->price, 2) }}
                        </div>
                    </div>

                    <div class="item-meta-row">
                        <span class="item-category">{{ optional($item->category)->name }}</span>

                        <div class="availability-pill {{ $item->is_available ? 'live' : 'unavailable' }}">
                            <span></span>
                            {{ $item->is_available ? 'Live' : 'Unavailable' }}
                        </div>

                        <form action="{{ route('items.toggleAvailability', $item) }}" method="POST" class="availability-action">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="availability-toggle">
                                {{ $item->is_available ? 'Available' : 'Unavailable' }}
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.menu-filter');
        const cards = document.querySelectorAll('.menu-item-card');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(btn => btn.classList.remove('active'));
                tab.classList.add('active');

                const filter = tab.dataset.filter;

                cards.forEach(card => {
                    const category = card.dataset.category;
                    const available = card.dataset.available;

                    if (filter === 'all') {
                        card.style.display = '';
                        return;
                    }

                    if (filter === 'unavailable') {
                        card.style.display = available === 'unavailable' ? '' : 'none';
                        return;
                    }

                    card.style.display = category === filter ? '' : 'none';
                });
            });
        });

        document.querySelectorAll('.availability-action').forEach(form => {
            form.addEventListener('submit', async function (event) {
                event.preventDefault();

                const button = form.querySelector('.availability-toggle');
                const card = form.closest('.menu-item-card');
                const statusPill = card.querySelector('.availability-pill');
                const offlineBadge = card.querySelector('.offline-badge');
                const currentState = card.dataset.available;
                const actionUrl = form.action;

                button.disabled = true;
                button.textContent = 'Saving...';

                try {
                    const response = await fetch(actionUrl, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    });

                    if (!response.ok) {
                        throw new Error('Unable to update availability');
                    }

                    const data = await response.json();
                    const isAvailable = data.is_available;

                    card.dataset.available = isAvailable ? 'live' : 'unavailable';
                    statusPill.innerHTML = `<span style="background: ${isAvailable ? '#10B981' : '#DC2626'}"></span>${isAvailable ? 'Live' : 'Unavailable'}`;
                    statusPill.classList.toggle('live', isAvailable);
                    statusPill.classList.toggle('unavailable', !isAvailable);

                    button.textContent = isAvailable ? 'Unavailable' : 'Available';

                    if (offlineBadge) {
                        offlineBadge.style.display = isAvailable ? 'none' : 'block';
                    }

                    const summaryLive = document.querySelector('.success-card h2');
                    const summaryOffline = document.querySelector('.danger-card h2');

                    if (summaryLive && summaryOffline && typeof data.available_count === 'number' && typeof data.offline_count === 'number') {
                        summaryLive.textContent = `${data.available_count} Dishes`;
                        summaryOffline.textContent = `${data.offline_count} Items`;
                    }

                    if (document.querySelector('.menu-filter.active')?.dataset.filter === 'unavailable' && isAvailable) {
                        card.style.display = 'none';
                    }
                } catch (error) {
                    console.error(error);
                    button.textContent = currentState === 'live' ? 'Unavailable' : 'Available';
                    alert('Failed to update availability. Please try again.');
                } finally {
                    button.disabled = false;
                }
            });
        });
    });
</script>
@endsection
