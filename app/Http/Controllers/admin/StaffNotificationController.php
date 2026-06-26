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

    public function markAsRead(StaffNotification $notification)
    {
        if ($notification->status !== 'read') {
            $notification->update(['status' => 'read']);
        }

        return redirect()->back()->with('success', 'Notification marked as read.');
    }
}
