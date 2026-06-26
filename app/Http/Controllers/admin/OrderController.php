<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Item;
use App\Models\Order;
use App\Models\Table;
use App\Models\OrderItem;


class OrderController extends Controller
{
    public function index(Request $request)
    {
        $ordersQuery = Order::with([
            'table',
            'items.item'
        ])
        ->latest();

        if (! $request->boolean('all')) {
            $ordersQuery->whereDate('created_at', Carbon::today());
        }

        $orders = $ordersQuery->paginate(10)->withQueryString();

        $showAll = $request->boolean('all');

        return view(
            'admin.orders.index',
            compact('orders', 'showAll')
        );
    }

    public function show(Order $order)
    {
        $order->load(['table', 'items.item.category']);

        return view(
            'admin.orders.order-detail',
            compact('order')
        );
    }

    public function store(
        Request $request,
        Table $table
    ) {

        $request->validate([
            'items' => 'required|array'
        ]);

        $total = 0;

        foreach (
            $request->items
            as $cartItem
        ) {

            $item = Item::findOrFail(
                $cartItem['item_id']
            );

            $total +=
                $item->price *
                $cartItem['quantity'];
        }

        $order = Order::create([
            'table_id' => $table->id,
            'total' => $total,
            'status' => 'pending',
        ]);

        foreach (
            $request->items
            as $cartItem
        ) {

            $item = Item::findOrFail(
                $cartItem['item_id']
            );

            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $item->id,
                'quantity' => $cartItem['quantity'],
                'price' => $item->price,
            ]);
        }

        $table->update([ 'status' => 'occupied' ]);

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
        ]);
    }

    public function updateStatus(Order $order, Request $request)
    {
        try {

            $validated = $request->validate([
                'status' => 'required|string|in:pending,cancelled,accepted,preparing,completed,finished',
            ]);

            $order->status = $validated['status'];
            $order->save();

            $table = $order->table;

            if ($table) {
                $activeOrderExists = $table->orders()
                    ->whereNotIn('status', ['finished', 'cancelled'])
                    ->where('id', '!=', $order->id)
                    ->exists();

                if (in_array($order->status, ['pending', 'accepted', 'preparing', 'completed'])) {
                    $table->update(['status' => 'occupied']);
                } elseif (in_array($order->status, ['finished', 'cancelled'])) {
                    if (! $activeOrderExists) {
                        $table->update(['status' => 'available']);
                    }
                }
            }

            return response()->json([
                'success' => true
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'message' => $e->getMessage(),
                'request' => $request->all(),
            ], 422);

        }
    }

    public function tableInvoice(Table $table)
    {
        $table->load(['orders.items.item']);

        return view(
            'admin.orders.table-invoice',
            compact('table')
        );
    }
}
