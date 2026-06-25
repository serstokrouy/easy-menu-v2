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

        // Create QR directory
        $directory = public_path('storage/qrcodes');

        if (!File::exists($directory)) {
            File::makeDirectory(
                $directory,
                0755,
                true
            );
        }

        // QR URL
        $url = route(
            'menu.table',
            $table->id
        );

        $filename = "table_{$table->id}.svg";

        QrCode::format('svg')
            ->size(300)
            ->generate(
                $url,
                $directory . '/' . $filename
            );

        $table->update([
            'qr_code' => 'storage/qrcodes/' . $filename,
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

        // Check if the QR code column is empty using the correct column name 'qr_code'
        if (empty($table->qr_code)) {
            // Create QR directory matching the store method
            $directory = public_path('storage/qrcodes');
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
            }

            // QR URL matching the store method
            $url = route('menu.table', $table->id);
            $filename = "table_{$table->id}.svg";

            // Save directly to disk using the file path inside generate()
            QrCode::format('svg')
                ->size(300)
                ->generate($url, $directory . '/' . $filename);

            // Update the database with the matching relative path pattern
            $table->update([
                'qr_code' => 'storage/qrcodes/' . $filename,
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
        if (
            $table->qr_code &&
            File::exists(
                public_path($table->qr_code)
            )
        ) {
            File::delete(
                public_path($table->qr_code)
            );
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
