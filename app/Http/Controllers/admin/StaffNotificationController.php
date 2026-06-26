<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StaffNotification;

class StaffNotificationController extends Controller
{
    public function index()
    {
        $notifications = StaffNotification::with(['table', 'order'])
            ->latest()
            ->paginate(12);

        return view('admin.staff-notifications.index', compact('notifications'));
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => StaffNotification::where('status', 'new')->count(),
        ]);
    }

    public function markAsRead(Request $request, StaffNotification $notification)
    {
        if ($notification->status !== 'read') {
            $notification->update(['status' => 'read']);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }
}
