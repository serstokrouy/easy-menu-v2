@extends('layouts.app')

@section('content')

<div class="category-page">
    @if(session('success'))
    <div class="toast success" id="toast">
        <i class="fa-solid fa-circle-check"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if(session('error'))
        <div class="toast error" id="toast">
            <i class="fa-solid fa-circle-xmark"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    <!-- Header -->
    <div class="page-header">


        <div>
            <h1>Categories</h1>
            <p>Manage your menu categories efficiently.</p>
        </div>

        <button class="btn-primary" onclick="openCreateModal()">
            <i class="fa-solid fa-plus"></i>
            Add Category
        </button>

    </div>

    <!-- Stats -->
    <div class="category-stats">

        <div class="stat-box">
            <div>
                <span>Total Categories</span>
                <h2>{{ $categories->count() }}</h2>
            </div>

            <div class="stat-icon">
                <i class="fa-solid fa-layer-group"></i>
            </div>
        </div>

        <div class="stat-box">
            <div>
                <span>Active Categories</span>
                <h2>{{ $categories->where('status', true)->count() }}</h2>
            </div>

            <div class="stat-icon success">
                <i class="fa-solid fa-check"></i>
            </div>
        </div>

    </div>

    <!-- Filter -->
    <div class="table-card">

        <div class="table-header">

            <form action="{{ route('categories.index') }}" method="GET">

                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input
                        type="text"
                        name="search"
                        placeholder="Search category..."
                        value="{{ request('search') }}"
                    >
                </div>

            </form>

        </div>

        <!-- Table -->
        <div class="table-responsive">

            <table>

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($categories as $category)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <strong>{{ $category->name }}</strong>
                            </td>

                            <td>
                                {{ Str::limit($category->description, 50) }}
                            </td>

                            <td>

                                @if($category->status)

                                    <span class="badge success">
                                        Active
                                    </span>

                                @else

                                    <span class="badge danger">
                                        Inactive
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $category->created_at->format('d M Y') }}
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <button
                                        type="button"
                                        class="btn-icon edit"
                                        onclick='openEditModal(
                                            {{ $category->id }},
                                            @json($category->name),
                                            @json($category->description),
                                            {{ $category->status }}
                                        )'
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <form
                                        action="{{ route('categories.destroy', $category) }}"
                                        method="POST"
                                        onsubmit="return confirm('Delete this category?')"
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

                            <td colspan="6">

                                <div class="empty-state">

                                    <i class="fa-solid fa-folder-open"></i>

                                    <p>
                                        No categories found.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="pagination-wrapper">
            {{ $categories->links() }}
        </div>

    </div>

</div>

<!-- Category Modal -->
<div class="modal" id="categoryModal">

    <div class="modal-content">

        <div class="modal-header">
            <h2 id="modalTitle">Add Category</h2>

            <button type="button" class="close-btn" onclick="closeModal()">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form id="categoryForm" method="POST">

            @csrf

            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div class="form-group">
                <label>Category Name</label>

                <input
                    type="text"
                    name="name"
                    id="categoryName"
                    required
                >
            </div>

            <div class="form-group">
                <label>Description</label>

                <textarea
                    name="description"
                    id="categoryDescription"
                    rows="4"
                ></textarea>
            </div>

            <div class="form-group">
                <label>Status</label>

                <select
                    name="status"
                    id="categoryStatus"
                >
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
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

    document.getElementById('modalTitle').textContent = 'Add Category';

    document.getElementById('categoryForm').action =
        "{{ route('categories.store') }}";

    document.getElementById('formMethod').value = 'POST';

    document.getElementById('categoryForm').reset();

    document.getElementById('categoryModal')
        .classList.add('show');
}

function openEditModal(id, name, description, status) {

    document.getElementById('modalTitle').textContent =
        'Edit Category';

    document.getElementById('categoryForm').action =
        `/categories/${id}`;

    document.getElementById('formMethod').value = 'PUT';

    document.getElementById('categoryName').value = name;

    document.getElementById('categoryDescription').value =
        description ?? '';

    document.getElementById('categoryStatus').value = status;

    document.getElementById('categoryModal')
        .classList.add('show');
}

function closeModal() {

    document.getElementById('categoryModal')
        .classList.remove('show');
}

document.getElementById('categoryModal')
    .addEventListener('click', function (e) {

        if (e.target === this) {
            closeModal();
        }
    });

</script>
@endsection
