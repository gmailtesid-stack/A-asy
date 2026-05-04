<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->latest()->paginate(10);
        return view('hr.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('hr.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|unique:employees',
            'name'        => 'required',
            'joined_at'   => 'required|date',
            'salary'      => 'required|numeric',
        ]);

        Employee::create($request->all());

        return redirect()->route('employees.index')->with('success', 'Karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee)
    {
        return view('hr.employees.show', compact('employee'));
    }
}
