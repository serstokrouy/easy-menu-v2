@extends('layouts.app')

@section('content')

<div class="category-page">

    @if(session('success'))
        <div class="toast success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div class="page-header">

        <div>
            <h1>Orders</h1>
            <p>Manage customer orders.</p>
        </div>

        <div class="page-actions">
            @if($showAll)
                <a href="{{ route('admin.orders.index') }}" class="action-btn">
                    Show today
                </a>
            @else
                <a href="{{ route('admin.orders.index', ['all' => 1]) }}" class="action-btn">
                    Show all days
                </a>
            @endif
        </div>

    </div>

    <div class="filter-note">
        Showing {{ $showAll ? 'all orders' : "today's orders" }}.
    </div>

    <!-- Stats -->
    <div class="category-stats">

        <div class="stat-box">

            <div>
                <span>Total Orders</span>
                <h2>{{ $orders->total() }}</h2>
            </div>

            <div class="stat-icon">
                <i class="fa-solid fa-receipt"></i>
            </div>

        </div>

        <div class="stat-box">

            <div>
                <span>Pending</span>
                <h2>
                    {{ $orders->where('status','pending')->count() }}
                </h2>
            </div>

            <div class="stat-icon warning">
                <i class="fa-solid fa-clock"></i>
            </div>

        </div>

        <div class="stat-box">

            <div>
                <span>Finished</span>
                <h2>
                    {{ $orders->where('status','finished')->count() }}
                </h2>
            </div>

            <div class="stat-icon success">
                <i class="fa-solid fa-flag-checkered"></i>
            </div>

        </div>

    </div>

    <!-- Table -->
    <div class="table-card">

        <div class="table-responsive">

            <table>

                <thead>

                    <tr>
                        <th> </th>
                        <th>Order No</th>
                        <th>Table</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody>

                    @forelse($orders as $order)

                        <tr data-status="{{ $order->status }}">

                            <td>
                                {{ $loop->iteration }}
                            </td>

                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="order-link">
                                    <strong>#{{ $order->id }}</strong>
                                </a>
                            </td>

                            <td>
                                {{ $order->table->name }}
                            </td>

                            <td>

                                @foreach($order->items as $item)

                                    <div>

                                        {{ $item->item->name }}

                                        <strong>
                                            x{{ $item->quantity }}
                                        </strong>

                                    </div>

                                @endforeach

                            </td>

                            <td>
                                ${{ number_format($order->total,2) }}
                            </td>

                            <td>

                                @if($order->status == 'pending')

                                    <span class="badge warning">
                                        Pending
                                    </span>

                                @elseif($order->status == 'accepted')

                                    <span class="badge primary">
                                        Accepted
                                    </span>

                                @elseif($order->status == 'preparing')

                                    <span class="badge info">
                                        Preparing
                                    </span>

                                @elseif($order->status == 'completed')

                                    <span class="badge success">
                                        Completed
                                    </span>

                                @elseif($order->status == 'finished')

                                    <span class="badge success">
                                        Finished
                                    </span>

                                @else

                                    <span class="badge danger">
                                        Cancelled
                                    </span>

                                @endif

                            </td>

                            <td>
                                {{ $order->created_at->format('d M Y H:i') }}
                            </td>

                            <td>

                                <form
                                    action="{{ route('admin.orders.updateStatus',$order) }}"
                                    method="POST"
                                    class="status-form"
                                    data-order-id="{{ $order->id }}"
                                >

                                    @csrf
                                    @method('PATCH')

                                    <select
                                        name="status"
                                        class="status-select"
                                    >

                                        <option
                                            value="pending"
                                            {{ $order->status == 'pending' ? 'selected' : '' }}
                                        >
                                            Pending
                                        </option>

                                        <option
                                            value="accepted"
                                            {{ $order->status == 'accepted' ? 'selected' : '' }}
                                        >
                                            Accepted
                                        </option>

                                        <option
                                            value="preparing"
                                            {{ $order->status == 'preparing' ? 'selected' : '' }}
                                        >
                                            Preparing
                                        </option>

                                        <option
                                            value="completed"
                                            {{ $order->status == 'completed' ? 'selected' : '' }}
                                        >
                                            Completed
                                        </option>

                                        <option
                                            value="finished"
                                            {{ $order->status == 'finished' ? 'selected' : '' }}
                                        >
                                            Finished
                                        </option>

                                        <option
                                            value="cancelled"
                                            {{ $order->status == 'cancelled' ? 'selected' : '' }}
                                        >
                                            Cancelled
                                        </option>

                                    </select>

                                </form>

                                <a href="{{ route('admin.orders.tableInvoice', $order->table) }}" class="action-btn small">
                                    Table Invoice
                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8">

                                <div class="empty-state">

                                    <i class="fa-solid fa-receipt"></i>

                                    <p>
                                        No orders found.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="pagination-wrapper">

            {{ $orders->links() }}

        </div>

    </div>

</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        document.querySelectorAll('.status-form').forEach(form => {
            const select = form.querySelector('.status-select');
            const orderId = form.dataset.orderId;
            const row = form.closest('tr');

            select.addEventListener('change', async function (event) {
                const newStatus = this.value;
                const actionUrl = form.action;

                select.disabled = true;
                const originalValue = form.querySelector(`option[value="${row.dataset.status}"]`)?.value;

                try {
                    const response = await fetch(actionUrl, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ status: newStatus }),
                    });

                    if (!response.ok) {
                        throw new Error('Failed to update status');
                    }

                    const data = await response.json();

                    row.dataset.status = newStatus;

                    const statusCell = row.querySelector('td:nth-child(6)');
                    let badgeClass = 'badge ';
                    let badgeText = '';

                    if (newStatus === 'pending') {
                        badgeClass += 'warning';
                        badgeText = 'Pending';
                    } else if (newStatus === 'accepted') {
                        badgeClass += 'primary';
                        badgeText = 'Accepted';
                    } else if (newStatus === 'preparing') {
                        badgeClass += 'info';
                        badgeText = 'Preparing';
                    } else if (newStatus === 'completed') {
                        badgeClass += 'success';
                        badgeText = 'Completed';
                    } else if (newStatus === 'finished') {
                        badgeClass += 'success';
                        badgeText = 'Finished';
                    } else if (newStatus === 'cancelled') {
                        badgeClass += 'danger';
                        badgeText = 'Cancelled';
                    }

                    statusCell.innerHTML = `<span class="${badgeClass}">${badgeText}</span>`;

                    const pendingCount = document.querySelectorAll('tr[data-status="pending"]').length;
                    const finishedCount = document.querySelectorAll('tr[data-status="finished"]').length;

                    const statBoxes = document.querySelectorAll('.stat-box');
                    if (statBoxes.length >= 2) {
                        statBoxes[1].querySelector('h2').textContent = pendingCount;
                        statBoxes[2].querySelector('h2').textContent = finishedCount;
                    }
                } catch (error) {
                    console.error('Error updating status:', error);
                    select.value = originalValue;
                    alert('Failed to update order status. Please try again.');
                } finally {
                    select.disabled = false;
                }
            });
        });
    });
</script>
@endsection
