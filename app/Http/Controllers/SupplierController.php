<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = \App\Models\Supplier::withCount('purchaseOrders')->latest()->paginate(10);
        return view('master.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'contact_person' => 'nullable|string',
            'email'          => 'nullable|email',
            'phone'          => 'nullable|string',
            'address'        => 'nullable|string',
            'notes'          => 'nullable|string',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ]);

        $data = $request->except('logo');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('suppliers', 'public');
        }

        \App\Models\Supplier::create($data);

        return redirect()->back()->with('success', 'Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, \App\Models\Supplier $supplier)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'contact_person' => 'nullable|string',
            'email'          => 'nullable|email',
            'phone'          => 'nullable|string',
            'address'        => 'nullable|string',
            'notes'          => 'nullable|string',
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ]);

        $data = $request->except(['logo', '_method', '_token']);

        if ($request->hasFile('logo')) {
            if ($supplier->logo) {
                Storage::disk('public')->delete($supplier->logo);
            }
            $data['logo'] = $request->file('logo')->store('suppliers', 'public');
        }

        $supplier->update($data);

        return redirect()->back()->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(\App\Models\Supplier $supplier)
    {
        if ($supplier->purchaseOrders()->count() > 0) {
            return redirect()->back()->with('error', 'Supplier tidak bisa dihapus karena memiliki Purchase Order terkait.');
        }

        if ($supplier->logo) {
            Storage::disk('public')->delete($supplier->logo);
        }

        $supplier->delete();
        return redirect()->back()->with('success', 'Supplier berhasil dihapus.');
    }
}
