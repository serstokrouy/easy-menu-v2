@extends('layouts.app')

@section('content')

<div class="category-page">
@if(session('success'))
    <div class="toast success">
        <i class="fa-solid fa-circle-check"></i>
        {{ session('success') }}
    </div>
@endif

<!-- Header -->
<div class="page-header">

    <div>
        <h1>Items</h1>
        <p>Manage restaurant menu items.</p>
    </div>

    <button
        class="btn-primary"
        onclick="openCreateModal()"
    >
        <i class="fa-solid fa-plus"></i>
        Add Item
    </button>

</div>

<!-- Stats -->
<div class="category-stats">

    <div class="stat-box">

        <div>
            <span>Total Items</span>
            <h2>{{ $items->total() }}</h2>
        </div>

        <div class="stat-icon">
            <i class="fa-solid fa-utensils"></i>
        </div>

    </div>

    <div class="stat-box">

        <div>
            <span>Available</span>
            <h2>
                {{ $items->where('is_available', true)->count() }}
            </h2>
        </div>

        <div class="stat-icon success">
            <i class="fa-solid fa-check"></i>
        </div>

    </div>

</div>

<!-- Table -->
<div class="table-card">

    <div class="table-header">

        <form method="GET">

            <div class="search-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input
                    type="text"
                    name="search"
                    placeholder="Search item..."
                    value="{{ request('search') }}"
                >

            </div>

        </form>

    </div>

    <div class="table-responsive">

        <table>

            <thead>

                <tr>
                    <th>#</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

            </thead>

            <tbody>

                @forelse($items as $item)

                    <tr>

                        <td>
                            {{ $loop->iteration }}
                        </td>

                        <td>

                            @if($item->image)

                                <img
                                    src="{{ asset('storage/' . $item->image) }}"
                                    class="item-thumb"
                                    alt="{{ $item->name }}"
                                >

                            @else

                                <img
                                    src="{{ asset('images/no-image.png') }}"
                                    class="item-thumb"
                                >

                            @endif

                        </td>

                        <td>
                            <strong>
                                {{ $item->name }}
                            </strong>
                        </td>

                        <td>
                            {{ $item->category->name }}
                        </td>

                        <td>
                            ${{ number_format($item->price, 2) }}
                        </td>

                        <td>

                            @if($item->is_available)

                                <span class="badge success">
                                    Available
                                </span>

                            @else

                                <span class="badge danger">
                                    Unavailable
                                </span>

                            @endif

                        </td>

                        <td>

                            <div class="action-buttons">

                                <button
                                    type="button"
                                    class="btn-icon edit"

                                    onclick='openEditModal(
                                        {{ $item->id }},
                                        @json($item->name),
                                        @json($item->description),
                                        {{ $item->category_id }},
                                        {{ $item->price }},
                                        {{ $item->is_available ? 1 : 0 }}
                                    )'
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <form
                                    action="{{ route('items.destroy', $item) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete this item?')"
                                >

                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn-icon delete"
                                    >
                                        <i class="fa-solid fa-trash"></i>
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7">

                            <div class="empty-state">

                                <i class="fa-solid fa-burger"></i>

                                <p>
                                    No items found.
                                </p>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    <div class="pagination-wrapper">

        {{ $items->links() }}

    </div>

</div>


</div>

<!-- MODAL -->

<div class="modal" id="itemModal">

<div class="modal-content">

    <div class="modal-header">

        <h2 id="modalTitle">
            Add Item
        </h2>

        <button
            type="button"
            class="close-btn"
            onclick="closeModal()"
        >
            <i class="fa-solid fa-xmark"></i>
        </button>

    </div>

    <form
        id="itemForm"
        method="POST"
        enctype="multipart/form-data"
    >

        @csrf

        <input
            type="hidden"
            id="formMethod"
            name="_method"
            value="POST"
        >

        <div class="form-group">

            <label>Item Name</label>

            <input
                type="text"
                name="name"
                id="itemName"
                required
            >

        </div>

        <div class="form-group">

            <label>Description</label>

            <textarea
                name="description"
                id="itemDescription"
                rows="3"
            ></textarea>

        </div>

        <div class="form-grid-layout">
            <!-- Row 1: The Labels -->
            <label for="itemCategory" class="grid-label">Category</label>
            <label for="itemPrice" class="grid-label">Price</label>
            <label for="itemStatus" class="grid-label">Status</label>

            <!-- Row 2: The Inputs -->
            <select name="category_id" id="itemCategory" required>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <input type="number" step="0.01" min="0" name="price" id="itemPrice" required>

            <select name="is_available" id="itemStatus" required>
                <option value="1">Available</option>
                <option value="0">Unavailable</option>
            </select>
        </div>

        <div class="form-group">

            <label>Image</label>

            <input
                type="file"
                name="image"
                accept="image/*"
            >

        </div>

        <div class="modal-footer">

            <button
                type="button"
                class="btn-secondary"
                onclick="closeModal()"
            >
                Cancel
            </button>

            <button
                type="submit"
                class="btn-primary"
            >
                Save
            </button>

        </div>

    </form>

</div>

</div>

@endsection

@section('scripts')

<script>

function openCreateModal() {

    itemForm.reset();

    modalTitle.textContent =
        'Add Item';

    formMethod.value =
        'POST';

    itemForm.action =
        "{{ route('items.store') }}";

    itemModal.classList.add(
        'show'
    );
}

function openEditModal(
    id,
    name,
    description,
    category,
    price,
    status
) {

    modalTitle.textContent =
        'Edit Item';

    formMethod.value =
        'PUT';

    itemForm.action =
        `/items/${id}`;

    itemName.value =
        name;

    itemDescription.value =
        description ?? '';

    itemCategory.value =
        category;

    itemPrice.value =
        price;

    itemStatus.value =
        status;

    itemModal.classList.add(
        'show'
    );
}

function closeModal() {

    itemModal.classList.remove(
        'show'
    );
}

itemModal?.addEventListener(
    'click',
    function(e) {

        if (
            e.target === this
        ) {
            closeModal();
        }
    }
);

</script>

@endsection
