<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Order;
use App\Models\StaffNotification;
use App\Models\Table;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $range = $request->query('range', 'day');
        $today = Carbon::today();

        if ($range === 'week') {
            $startDate = $today->copy()->subDays(6);
            $subtitle = 'Last 7 days';
        } elseif ($range === 'month') {
            $startDate = $today->copy()->subDays(29);
            $subtitle = 'Last 30 days';
        } else {
            $range = 'day';
            $startDate = $today;
            $subtitle = 'Today';
        }

        $orderQuery = Order::whereBetween('created_at', [
            $startDate->copy()->startOfDay(),
            $today->copy()->endOfDay(),
        ]);

        $totalRevenue = $range === 'day'
            ? Order::whereDate('created_at', $today)->sum('total')
            : $orderQuery->sum('total');

        $ordersCount = $range === 'day'
            ? Order::whereDate('created_at', $today)->count()
            : $orderQuery->count();

        $customersCount = $range === 'day'
            ? Order::whereDate('created_at', $today)
                ->distinct('table_id')
                ->count('table_id')
            : $orderQuery->distinct('table_id')->count('table_id');

        $productsCount = Item::count();

        $salesChartLabels = [];
        $salesChartData = [];

        if ($range === 'day') {
            for ($hour = 0; $hour < 24; $hour++) {
                $labelHour = $today->copy()->hour($hour)->format('H:00');
                $salesChartLabels[] = $labelHour;
                $salesChartData[] = Order::whereBetween('created_at', [
                    $today->copy()->hour($hour)->startOfHour(),
                    $today->copy()->hour($hour)->endOfHour(),
                ])->sum('total');
            }
        } else {
            $daysRange = $startDate->diffInDays($today);
            for ($days = $daysRange; $days >= 0; $days--) {
                $date = $today->copy()->subDays($days);
                $salesChartLabels[] = $date->format('M j');
                $salesChartData[] = Order::whereDate('created_at', $date)->sum('total');
            }
        }

        $statusTotals = Order::whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                $today->copy()->endOfDay(),
            ])
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $completedCount = $statusTotals['completed'] ?? 0;
        $pendingCount = $statusTotals['pending'] ?? 0;
        $cancelledCount = $statusTotals['cancelled'] ?? 0;
        $unreadNotifications = StaffNotification::where('status', 'new')->count();

        $recentOrders = Order::with('table')
            ->whereBetween('created_at', [
                $startDate->copy()->startOfDay(),
                $today->copy()->endOfDay(),
            ])
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'range',
            'subtitle',
            'totalRevenue',
            'ordersCount',
            'customersCount',
            'productsCount',
            'salesChartLabels',
            'salesChartData',
            'completedCount',
            'pendingCount',
            'cancelledCount',
            'recentOrders',
            'unreadNotifications'
        ));
    }
}
