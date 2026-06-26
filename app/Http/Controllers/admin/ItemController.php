<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;


class ItemController extends Controller
{
    /**
     * Display items.
     */
    public function index(Request $request)
    {
        $items = Item::with('category')

            ->when(
                $request->search,
                function ($query, $search) {

                    $query->where(
                        'name',
                        'like',
                        "%{$search}%"
                    );
                }
            )

            ->latest()
            ->paginate(10);

        $categories = Category::all();

        return view(
            'admin.items.index',
            compact(
                'items',
                'categories'
            )
        );
    }

    /**
     * Store item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' =>
                'required|exists:categories,id',

            'name' =>
                'required|string|max:255',

            'description' =>
                'nullable|string',

            'price' =>
                'required|numeric|min:0',

            'image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'is_available' =>
                'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            $disk = config('filesystems.default');

            $validated['image'] =
                $request->file('image')
                    ->store(
                        'items',
                        $disk
                    );

            // Ensure uploaded image is publicly accessible when using remote disks
            Storage::disk($disk)->setVisibility($validated['image'], 'public');
        }

        $validated['is_available'] =
            $request->boolean(
                'is_available'
            );

        Item::create($validated);

        return redirect()
            ->route('items.index')
            ->with(
                'success',
                'Item created successfully.'
            );
    }

    /**
     * Toggle item availability.
     */
    public function toggleAvailability(Request $request, Item $item)
    {
        $item->update([
            'is_available' => ! $item->is_available,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            $availableCount = Item::where('is_available', true)->count();
            $offlineCount = Item::where('is_available', false)->count();

            return response()->json([
                'is_available' => $item->is_available,
                'available_count' => $availableCount,
                'offline_count' => $offlineCount,
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Item availability updated.');
    }

    /**
     * Update item.
     */
    public function update(
        Request $request,
        Item $item
    ) {

        $validated = $request->validate([
            'category_id' =>
                'required|exists:categories,id',

            'name' =>
                'required|string|max:255',

            'description' =>
                'nullable|string',

            'price' =>
                'required|numeric|min:0',

            'image' =>
                'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',

            'is_available' =>
                'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {

            $disk = config('filesystems.default');

            if (
                $item->image &&
                Storage::disk($disk)
                    ->exists($item->image)
            ) {

                Storage::disk($disk)
                    ->delete($item->image);
            }

            $validated['image'] =
                $request->file('image')
                    ->store(
                        'items',
                        $disk
                    );
        }

        $validated['is_available'] =
            $request->boolean(
                'is_available'
            );

        $item->update($validated);

        return redirect()
            ->route('items.index')
            ->with(
                'success',
                'Item updated successfully.'
            );
    }

    /**
     * Delete item.
     */
    public function destroy(Item $item)
    {
        $disk = config('filesystems.default');

        if (
            $item->image &&
            Storage::disk($disk)
                ->exists($item->image)
        ) {

            Storage::disk($disk)
                ->delete($item->image);
        }

        $item->delete();

        return redirect()
            ->route('items.index')
            ->with(
                'success',
                'Item deleted successfully.'
            );
    }
}
