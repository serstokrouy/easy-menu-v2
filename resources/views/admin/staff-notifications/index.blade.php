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
                                            <source src="{{ asset('storage/' . $notification->audio_path) }}" type="audio/webm">
                                            <source src="{{ asset('storage/' . $notification->audio_path) }}" type="audio/ogg">
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
                                    <form action="{{ route('admin.staffNotifications.markRead', $notification) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="action-btn small">Mark Read</button>
                                    </form>
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
