<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OutletController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Outlet::class);
        $outlets = Outlet::latest()->paginate(10);
        return view('outlets.index', compact('outlets'));
    }

    public function create()
    {
        $this->authorize('create', Outlet::class);
        return view('outlets.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Outlet::class);

        $request->validate([
            'name'    => 'required|string|max:255',
            'code'    => 'required|string|unique:outlets,code|max:10',
            'address' => 'required|string',
            'phone'   => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Outlet::create($request->all());

        return redirect()->route('outlets.index')->with('success', 'Outlet berhasil ditambahkan.');
    }

    public function edit(Outlet $outlet)
    {
        $this->authorize('update', $outlet);
        return view('outlets.edit', compact('outlet'));
    }

    public function update(Request $request, Outlet $outlet)
    {
        $this->authorize('update', $outlet);

        $request->validate([
            'name'    => 'required|string|max:255',
            'code'    => ['required', 'string', 'max:10', Rule::unique('outlets')->ignore($outlet->id)],
            'address' => 'required|string',
            'phone'   => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $outlet->update($request->all());

        return redirect()->route('outlets.index')->with('success', 'Outlet berhasil diperbarui.');
    }

    public function destroy(Outlet $outlet)
    {
        $this->authorize('delete', $outlet);
        
        if ($outlet->users()->exists()) {
            return back()->with('error', 'Outlet tidak bisa dihapus karena masih memiliki user.');
        }

        $outlet->delete();
        return back()->with('success', 'Outlet berhasil dihapus.');
    }
}
