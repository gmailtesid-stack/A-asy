<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Warehouse;
use App\Models\Outlet;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with(['outlet', 'locations', 'inventories'])->get();
        $outlets = Outlet::all();
        return view('warehouses.index', compact('warehouses', 'outlets'));
    }

    public function show(Warehouse $warehouse)
    {
        $warehouse->load('locations');
        $locations = $warehouse->locations;
        return view('master.locations.index', compact('warehouse', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'outlet_id'  => 'required|exists:outlets,id',
            'name'       => 'required|string|max:255',
            'address'    => 'nullable|string',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('photo');

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('warehouses', 'public');
        }

        Warehouse::create($data);

        return redirect()->back()->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'outlet_id'  => 'required|exists:outlets,id',
            'name'       => 'required|string|max:255',
            'address'    => 'nullable|string',
            'latitude'   => 'nullable|numeric',
            'longitude'  => 'nullable|numeric',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_active'  => 'nullable|boolean',
        ]);

        $data = $request->except(['photo', '_method', '_token']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($warehouse->photo) {
                Storage::disk('public')->delete($warehouse->photo);
            }
            $data['photo'] = $request->file('photo')->store('warehouses', 'public');
        }

        $warehouse->update($data);

        return redirect()->back()->with('success', 'Data gudang berhasil diperbarui.');
    }

    public function destroy(Warehouse $warehouse)
    {
        // Guard: jangan hapus jika masih ada inventaris aktif
        if ($warehouse->inventories()->where('quantity', '>', 0)->count() > 0) {
            return redirect()->back()->with('error', 'Gudang tidak dapat dihapus karena masih memiliki stok barang.');
        }

        if ($warehouse->photo) {
            Storage::disk('public')->delete($warehouse->photo);
        }

        $warehouse->delete();

        return redirect()->back()->with('success', 'Gudang berhasil dihapus.');
    }
}
