<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = \App\Models\Brand::latest()->paginate(10);
        return view('master.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        \App\Models\Brand::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Brand berhasil ditambahkan.');
    }

    public function update(Request $request, \App\Models\Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $brand->update([
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Brand berhasil diperbarui.');
    }

    public function destroy(\App\Models\Brand $brand)
    {
        $brand->delete();
        return redirect()->back()->with('success', 'Brand berhasil dihapus.');
    }
}
