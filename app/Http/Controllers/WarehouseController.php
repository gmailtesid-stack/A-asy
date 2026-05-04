<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Outlet;
use Illuminate\Support\Facades\Storage;

class WarehouseController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with(['outlet', 'locations'])->get();
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
            'outlet_id' => 'required|exists:outlets,id',
            'name'      => 'required|string|max:255',
            'address'   => 'nullable|string',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('photo');
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('warehouses', 'public');
            $data['photo'] = Storage::url($path);
        }

        Warehouse::create($data);

        return redirect()->back()->with('success', 'Gudang berhasil ditambahkan.');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $request->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'name'      => 'required|string|max:255',
            'address'   => 'nullable|string',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->except('photo');
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($warehouse->photo && file_exists(public_path($warehouse->photo))) {
                @unlink(public_path($warehouse->photo));
            }
            $path = $request->file('photo')->store('warehouses', 'public');
            $data['photo'] = Storage::url($path);
        }

        $warehouse->update($data);

        return redirect()->back()->with('success', 'Gudang berhasil diperbarui.');
    }

    public function destroy(Warehouse $warehouse)
    {
        if ($warehouse->inventories()->count() > 0) {
            return back()->with('error', 'Gudang tidak bisa dihapus karena masih memiliki stok barang.');
        }

        if ($warehouse->photo && file_exists(public_path($warehouse->photo))) {
            @unlink(public_path($warehouse->photo));
        }

        $warehouse->delete();

        return redirect()->back()->with('success', 'Gudang berhasil dihapus.');
    }
}
