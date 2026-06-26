<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Table;

class TableController extends Controller
{
        /**
     * Display a listing of tables.
     */
    public function index(Request $request)
    {
        $tables = Table::query()

            ->when($request->search, function ($query, $search) {
                $query->where(
                    'name',
                    'like',
                    "%{$search}%"
                );
            })

            ->latest()
            ->paginate(12);

        return view(
            'admin.table.index',
            compact('tables')
        );
    }

    /**
     * Store a newly created table.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255|unique:tables,name',
            'capacity' => 'required|integer|min:1',
            'status'   => 'required|in:available,occupied,reserved',
        ]);

        $table = Table::create($validated);

        // Generate QR content and store on configured disk
        $url = route('menu.table', $table->id);
        $filename = "table_{$table->id}.svg";
        $svg = QrCode::format('svg')
            ->size(300)
            ->generate($url);

        $disk = config('filesystems.default');
        $path = 'qrcodes/' . $filename;
        \Illuminate\Support\Facades\Storage::disk($disk)->put($path, $svg);

        // Ensure public visibility for QR code
        \Illuminate\Support\Facades\Storage::disk($disk)->setVisibility($path, 'public');

        $table->update([
            'qr_code' => $path,
        ]);

        return redirect()
            ->route('tables.index')
            ->with(
                'success',
                'Table created successfully.'
            );
    }

    /**
     * Update the specified table.
     */
    public function update(Request $request, Table $table)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tables,name,' . $table->id,
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved',
        ]);

        $table->update($validated);

        // Check if the QR code column is empty and create/upload to disk
        if (empty($table->qr_code)) {
            $url = route('menu.table', $table->id);
            $filename = "table_{$table->id}.svg";
            $svg = QrCode::format('svg')
                ->size(300)
                ->generate($url);

            $disk = config('filesystems.default');
            $path = 'qrcodes/' . $filename;
            \Illuminate\Support\Facades\Storage::disk($disk)->put($path, $svg);

            $table->update([
                'qr_code' => $path,
            ]);
        }

        return redirect()
            ->route('tables.index')
            ->with('success', 'Table updated successfully.');
    }

    /**
     * Remove the specified table.
     */
    public function destroy(Table $table)
    {
        if ($table->qr_code) {
            $disk = config('filesystems.default');
            if (\Illuminate\Support\Facades\Storage::disk($disk)->exists($table->qr_code)) {
                \Illuminate\Support\Facades\Storage::disk($disk)->delete($table->qr_code);
            }
        }

        $table->delete();

        return redirect()
            ->route('tables.index')
            ->with(
                'success',
                'Table deleted successfully.'
            );
    }
}
