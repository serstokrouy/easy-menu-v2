@extends('layouts.app')

@section('content')

<div class="category-page">

    {{-- Toast Message --}}
    @if(session('success'))
        <div class="toast success">
            <i class="fa-solid fa-circle-check"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Header -->
    <div class="page-header">

        <div>
            <h1>Tables</h1>
            <p>Manage restaurant tables and QR codes.</p>
        </div>

        <button
            class="btn-primary"
            onclick="openCreateModal()"
        >
            <i class="fa-solid fa-plus"></i>
            Add Table
        </button>

    </div>

    <!-- Stats -->
    <div class="category-stats">

        <div class="stat-box">
            <div>
                <span>Total Tables</span>
                <h2>{{ $tables->total() }}</h2>
            </div>

            <div class="stat-icon">
                <i class="fa-solid fa-table"></i>
            </div>
        </div>

        <div class="stat-box">
            <div>
                <span>Available</span>
                <h2>
                    {{ $tables->where('status', 'available')->count() }}
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
                        placeholder="Search table..."
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
                        <th>Table</th>
                        <th>Seats</th>
                        <th>Status</th>
                        <th>QR</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($tables as $table)

                        <tr>

                            <td>{{ $loop->iteration }}</td>

                            <td>
                                {{ $table->name }}
                            </td>

                            <td>
                                {{ $table->capacity }}
                                Seats
                            </td>

                            <td>

                                <span
                                    class="badge
                                    {{ $table->status == 'available'
                                        ? 'success'
                                        : 'danger' }}"
                                >
                                    {{ ucfirst($table->status) }}
                                </span>

                            </td>

                            <td>

                               @if($table->qr_code)

                                <button
                                    type="button"
                                    class="btn-icon info"

                                    onclick="showQrModal(
                                        '{{ asset($table->qr_code) }}',
                                        '{{ $table->name }}'
                                    )"
                                >
                                    <i class="fa-solid fa-qrcode"></i>
                                </button>

                                @endif
                            </td>

                            <td>

                                <div class="action-buttons">

                                    <button
                                        type="button"
                                        class="btn-icon edit"

                                        onclick='openEditModal(
                                            {{ $table->id }},
                                            @json($table->name),
                                            {{ $table->capacity }},
                                            @json($table->status)
                                        )'
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </button>

                                    <form
                                        action="{{ route('tables.destroy', $table) }}"
                                        method="POST"

                                        onsubmit="
                                            return confirm(
                                                'Delete this table?'
                                            )
                                        "
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
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

                                    <i class="fa-solid fa-table"></i>

                                    <p>
                                        No tables found.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{ $tables->links() }}

    </div>

</div>

<div class="modal modal--small" id="tableModal">

    <div class="modal-content">

        <div class="modal-header">

            <h2 id="modalTitle">
                Add Table
            </h2>

            <button
                class="close-btn"
                onclick="closeModal()"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

        <form
            id="tableForm"
            method="POST"
        >

            @csrf

            <input
                type="hidden"
                id="formMethod"
                name="_method"
                value="POST"
            >

            <div class="form-group">

                <label>Table Name</label>

                <input
                    type="text"
                    name="name"
                    id="tableName"
                    required
                >

            </div>

            <div class="form-group">

                <label>Capacity</label>

                <input
                    type="number"
                    name="capacity"
                    id="tableCapacity"
                    min="1"
                    required
                >

            </div>

            <div class="form-group">

                <label>Status</label>

                <select
                    name="status"
                    id="tableStatus"
                >
                    <option value="available">
                        Available
                    </option>

                    <option value="occupied">
                        Occupied
                    </option>

                    <option value="reserved">
                        Reserved
                    </option>

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
                    class="btn-primary"
                >
                    Save
                </button>

            </div>

        </form>

    </div>

</div>

<!-- QR Preview Modal -->
<div class="modal" id="qrModal">

    <div class="modal-content qr-modal">

        <div class="modal-header">

            <h2 id="qrTitle">
                QR Code
            </h2>

            <button
                type="button"
                class="close-btn"
                onclick="closeQrModal()"
            >
                <i class="fa-solid fa-xmark"></i>
            </button>

        </div>

        <div class="qr-preview">

            <div class="qr-card" id="qrCard">

                <h2>EASY-MENU</h2>
                <p class="qr-table-name" id="qrTableName">
                    Table 01
                </p>

                <div class="qr-wrapper">
                <img
                    id="qrImage"
                    src=""
                    alt="QR Code"
                    class="qr-image"
                >

                <img
                    src="{{ asset('assets/web-logo.png') }}"
                    alt="Logo"
                    class="qr-center-logo"
                >
            </div>

                <p class="qr-instruction">
                    Scan QR Code to Order
                </p>

                <small>
                    Thank you for visiting
                </small>

            </div>

        </div>

        <div class="modal-footer">

            <button
                type="button"
                class="btn-secondary"
                onclick="closeQrModal()"
            >
                Close
            </button>

            <button
                class="btn-primary"
                onclick="printQr()"
            >
                <i class="fa-solid fa-print"></i>
                Print QR
            </button>
        </div>

    </div>

</div>
@endsection
@section('scripts')
<script>
    function showQrModal(
    qrUrl,
    tableName
) {

    document.getElementById('qrImage')
        .src = qrUrl;

    document.getElementById('qrTableName')
        .textContent = tableName;


    document.getElementById('qrModal')
        .classList.add('show');
}

function printQr() {

    const content = document.getElementById('qrCard').innerHTML;

    const win = window.open('', '_blank', 'width=500,height=700');

    win.document.write(`
        <html>
        <head>
            <title>Print QR</title>

            <style>
                body {
                    text-align: center;
                    font-family: Arial, sans-serif;
                    padding: 30px;
                    margin: 0;
                }

                .qr-wrapper {
                    position: relative;
                    width: 250px;
                    height: 250px;
                    margin: 10px auto;
                }

                .qr-image {
                    width: 100%;
                    height: 100%;
                    object-fit: contain;
                }

                .qr-center-logo {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    width: 60px;
                    height: 60px;
                    transform: translate(-50%, -50%);
                    background: white;
                    border-radius: 50%;
                    padding: 2px;
                }

                h2 {
                    margin: 10px 0;
                }
                p{
                text-transform: uppercase;
                }
                p, small {
                    margin: 5px 0;
                }
            </style>
        </head>

        <body>
            ${content}
        </body>
        </html>
    `);

    win.document.close();

    // Wait until everything loads
    win.onload = function () {

        setTimeout(() => {
            win.focus();
            win.print();
            win.close();
        }, 500);

    };
}


function closeQrModal() {

    document.getElementById('qrModal')
        .classList.remove('show');
}

// Close when clicking outside
document.getElementById('qrModal')
    ?.addEventListener('click', function (e) {

        if (e.target === this) {

            closeQrModal();

        }

});

function openCreateModal() {

    tableForm.reset();

    modalTitle.textContent = 'Add Table';

    formMethod.value = 'POST';

    tableForm.action =
        "{{ route('tables.store') }}";

    tableModal.classList.add('show');
}

function openEditModal(
    id,
    name,
    capacity,
    status
) {

    modalTitle.textContent = 'Edit Table';

    formMethod.value = 'PUT';

    tableForm.action =
        `/tables/${id}`;

    tableName.value = name;

    tableCapacity.value = capacity;

    tableStatus.value = status;

    tableModal.classList.add('show');
}

function closeModal() {

    tableModal.classList.remove('show');
}

</script>
@endsection



