<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Outlet;

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
        ]);

        Warehouse::create($request->all());

        return redirect()->back()->with('success', 'Gudang berhasil ditambahkan.');
    }
}
