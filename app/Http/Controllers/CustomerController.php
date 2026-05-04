<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('outlet')->latest()->paginate(20);
        return view('crm.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('crm.customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'phone' => 'nullable|unique:customers',
        ]);

        Customer::create($request->all());

        return redirect()->route('customers.index')->with('success', 'Pelanggan berhasil ditambahkan.');
    }
}
