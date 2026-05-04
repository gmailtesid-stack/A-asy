<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        return redirect()->route('warehouses.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:rack,bin,zone',
        ]);

        Location::create($request->all());
        return back()->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:rack,bin,zone',
        ]);

        $location->update($request->all());
        return back()->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location)
    {
        // Check if location is used in inventories
        if (\App\Models\Inventory::where('location_id', $location->id)->count() > 0) {
            return back()->with('error', 'Lokasi tidak bisa dihapus karena masih terdapat stok barang.');
        }
        $location->delete();
        return back()->with('success', 'Lokasi berhasil dihapus.');
    }
}
