@extends('layouts.app')

@section('content')

<div class="category-page">

    <div class="page-header">
        <div>
            <h1>Staff Notifications</h1>
            <p>Messages from customers requesting help.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="toast success">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="table-card">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Order</th>
                        <th>Table</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Received</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                        <tr class="{{ $notification->status === 'new' ? 'notification-new' : '' }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>#{{ $notification->order?->id ?? 'N/A' }}</td>
                            <td>{{ $notification->table?->name ?? 'Unknown' }}</td>
                            <td style="max-width: 320px; white-space: normal;">
                                <div>{{ $notification->message }}</div>
                                @if($notification->audio_path)
                                    <div style="margin-top: 8px;">
                                        <audio controls style="width:100%;">
                                            <source src="{{ Storage::disk(config('filesystems.default'))->url($notification->audio_path) }}" type="audio/webm">
                                            <source src="{{ Storage::disk(config('filesystems.default'))->url($notification->audio_path) }}" type="audio/ogg">
                                            Your browser does not support audio playback.
                                        </audio>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $notification->status === 'new' ? 'danger' : 'success' }}">
                                    {{ ucfirst($notification->status) }}
                                </span>
                            </td>
                            <td>{{ $notification->created_at->format('d M Y, H:i') }}</td>
                            <td>
                                @if($notification->status === 'new')
                                    <button type="button" class="action-btn small mark-read-btn" data-url="{{ route('admin.staffNotifications.markRead', $notification) }}">
                                        Mark Read
                                    </button>
                                @else
                                    <span class="action-btn small disabled">Read</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fa-solid fa-bell-slash"></i>
                                    <p>No staff notifications at the moment.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="pagination-wrapper">
        {{ $notifications->links() }}
    </div>

</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        function updateHeaderCount() {
            const countEl = document.getElementById('notificationCount');
            if (!countEl) {
                return;
            }

            const current = parseInt(countEl.textContent || '0', 10);
            const next = Math.max(current - 1, 0);

            if (next <= 0) {
                countEl.style.display = 'none';
                countEl.textContent = '';
            } else {
                countEl.textContent = next;
                countEl.style.display = 'inline-flex';
            }
        }

        document.querySelectorAll('.mark-read-btn').forEach(button => {
            button.addEventListener('click', async (e) => {
                e.preventDefault();

                const url = button.dataset.url;
                if (!url || !token) {
                    return;
                }

                button.disabled = true;
                const originalText = button.textContent;
                button.textContent = 'Saving...';

                try {
                    const response = await fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                    });

                    if (!response.ok) {
                        throw new Error('Request failed');
                    }

                    const row = button.closest('tr');
                    const statusCell = row.querySelector('td:nth-child(5) .badge');
                    if (statusCell) {
                        statusCell.textContent = 'Read';
                        statusCell.classList.remove('danger');
                        statusCell.classList.add('success');
                    }

                    row.classList.remove('notification-new');

                    const readSpan = document.createElement('span');
                    readSpan.className = 'action-btn small disabled';
                    readSpan.textContent = 'Read';
                    button.parentNode.replaceChild(readSpan, button);

                    updateHeaderCount();
                } catch (error) {
                    console.error(error);
                    button.disabled = false;
                    button.textContent = originalText || 'Mark Read';
                }
            });
        });
    });
</script>
@endsection
