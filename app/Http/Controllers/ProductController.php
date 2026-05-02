<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    private CloudinaryService $cloudinary;

    public function __construct(CloudinaryService $cloudinary)
    {
        $this->cloudinary = $cloudinary;
        $this->authorizeResource(Product::class);
    }

    public function index()
    {
        $products = Product::with('category')->latest()->paginate(20);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sku'         => 'required|string|unique:products,sku',
            'price'       => 'required|numeric|min:0',
            'cost_price'  => 'required|numeric|min:0',
            'unit'        => 'required|string',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $this->cloudinary->upload($request->file('image')->getRealPath(), 'easy-pos/products');
        }

        Product::create([
            'category_id' => $request->category_id,
            'name'        => $request->name,
            'sku'         => $request->sku,
            'description' => $request->description,
            'price'       => $request->price,
            'cost_price'  => $request->cost_price,
            'unit'        => $request->unit,
            'image'       => $imageUrl,
            'is_active'   => $request->boolean('is_active', true),
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan!');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageUrl = $product->image;
        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image) {
                $publicId = CloudinaryService::extractPublicId($product->image);
                if ($publicId) $this->cloudinary->delete($publicId);
            }
            $imageUrl = $this->cloudinary->upload($request->file('image')->getRealPath(), 'easy-pos/products');
        }

        $product->update(array_merge($request->except('image'), ['image' => $imageUrl]));

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil diupdate!');
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
             $publicId = CloudinaryService::extractPublicId($product->image);
             if ($publicId) $this->cloudinary->delete($publicId);
        }
        
        $product->delete();
        return back()->with('success', 'Produk dihapus.');
    }
}
