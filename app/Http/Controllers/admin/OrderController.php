<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use App\Models\Table;
use App\Models\OrderItem;


class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with([
            'table',
            'items.item'
        ])
        ->latest()
        ->paginate(10);

        return view(
            'admin.orders.index',
            compact('orders')
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

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
        ]);
    }

    public function updateStatus(
        Order $order,
        Request $request
    ) {

        $order->update([
            'status' =>
                $request->status
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => $order->status,
                'message' => 'Order status updated successfully.',
            ]);
        }

        return back();
    }
}
