<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'description' => $request->description,
        ];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('brands', 'public');
            $data['photo'] = Storage::url($path);
        }

        \App\Models\Brand::create($data);

        return redirect()->back()->with('success', 'Brand berhasil ditambahkan.');
    }

    public function update(Request $request, \App\Models\Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => \Str::slug($request->name),
            'description' => $request->description,
        ];

        if ($request->hasFile('photo')) {
            if ($brand->photo && file_exists(public_path($brand->photo))) {
                @unlink(public_path($brand->photo));
            }
            $path = $request->file('photo')->store('brands', 'public');
            $data['photo'] = Storage::url($path);
        }

        $brand->update($data);

        return redirect()->back()->with('success', 'Brand berhasil diperbarui.');
    }

    public function destroy(\App\Models\Brand $brand)
    {
        if ($brand->photo && file_exists(public_path($brand->photo))) {
            @unlink(public_path($brand->photo));
        }
        $brand->delete();
        return redirect()->back()->with('success', 'Brand berhasil dihapus.');
    }
}
