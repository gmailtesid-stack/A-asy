<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Supplier;

class MasterDataController extends Controller
{
    public function index()
    {
        $stats = [
            'products'   => Product::count(),
            'brands'     => Brand::count(),
            'categories' => Category::count(),
            'suppliers'  => Supplier::count(),
        ];

        return view('master.index', compact('stats'));
    }
}
