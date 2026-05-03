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

    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = Product::with('category')->latest();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $products = $query->paginate(20)->appends(['status' => $status]);

        $metrics = [
            'all'          => Product::count(),
            'live'         => Product::where('status', 'live')->count(),
            'under_review' => Product::where('status', 'under_review')->count(),
            'draft'        => Product::where('status', 'draft')->count(),
            'failed'       => Product::where('status', 'failed')->count(),
        ];

        return view('products.index', compact('products', 'metrics', 'status'));
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
            'csku'        => 'nullable|string',
            'type'        => 'required|in:simple,variant,kit',
            'status'      => 'required|in:live,draft,under_review,failed',
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
            'csku'        => $request->csku ?? $request->sku,
            'type'        => $request->type,
            'status'      => $request->status,
            'description' => $request->description,
            'price'       => $request->price,
            'cost_price'  => $request->cost_price,
            'unit'        => $request->unit,
            'image'       => $imageUrl,
            'is_active'   => $request->status === 'live',
        ]);

        return redirect()->route('products.index')
            ->with('success', 'Produk berhasil ditambahkan ke katalog!');
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
            'type'        => 'required|in:simple,variant,kit',
            'status'      => 'required|in:live,draft,under_review,failed',
            'price'       => 'required|numeric|min:0',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imageUrl = $product->image;
        if ($request->hasFile('image')) {
            if ($product->image) {
                $publicId = CloudinaryService::extractPublicId($product->image);
                if ($publicId) $this->cloudinary->delete($publicId);
            }
            $imageUrl = $this->cloudinary->upload($request->file('image')->getRealPath(), 'easy-pos/products');
        }

        $product->update(array_merge($request->except('image'), [
            'image' => $imageUrl,
            'csku'  => $request->csku ?? $product->csku,
            'is_active' => $request->status === 'live',
        ]));

        return redirect()->route('products.index')
            ->with('success', 'Data katalog produk diperbarui!');
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
