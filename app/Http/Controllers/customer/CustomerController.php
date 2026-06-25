<?php

namespace App\Http\Controllers\customer;

use App\Http\Controllers\Controller;

use App\Models\Item;
use App\Models\Order;
use App\Models\Table;
use App\Models\Category;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Show menu by table QR
     */
    public function index(Request $request, Table $table = null, Category $category = null)
    {
        if (! $table) {
            $tableId = $request->query('table');
            if (! $tableId) {
                abort(404);
            }
            $table = Table::findOrFail($tableId);
        }

        $selectedCategory = 'all';
        if ($category) {
            $selectedCategory = $category->id;
        } elseif ($request->filled('category')) {
            $selectedCategory = $request->query('category');
        }

        $categories = Category::orderBy('name')
            ->get();

        $itemsQuery = Item::with('category')
            ->where('is_available', true)
            ->orderBy('name');

        if ($selectedCategory !== 'all') {
            if (is_numeric($selectedCategory)) {
                $itemsQuery->where('category_id', intval($selectedCategory));
            } else {
                $itemsQuery->whereHas('category', function ($query) use ($selectedCategory) {
                    $query->where('name', $selectedCategory);
                });
            }
        }

        $items = $itemsQuery->get();

        return view(
            'customer.menu.index',
            compact(
                'table',
                'categories',
                'items',
                'selectedCategory'
            )
        );
    }

    /**
     * Show cart page
     */
    public function cart(Table $table)
    {
        return view(
            'customer.cart.index',
            compact('table')
        );
    }

    /**
     * Order success page
     */
    public function success(Order $order)
    {
        $order->load([
            'table',
            'items.item'
        ]);

        return view(
            'customer.order-success.index',
            compact('order')
        );
    }

    /**
     * Track order page
     */
    public function trackOrder(Order $order)
    {
        $order->load([
            'table',
            'items.item'
        ]);

        return view(
            'customer.track-order.index',
            compact('order')
        );
    }

    /**
     * API for auto refresh status
     */
    public function status(Order $order)
    {
        $itemsCount = $order->items()->sum('quantity');

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'total' => $order->total,
            'table' => $order->table?->name,
            'items_count' => $itemsCount,
            'created_at' => $order->created_at?->toIso8601String(),
            'updated_at' => $order->updated_at?->toIso8601String(),
        ]);
    }
}
