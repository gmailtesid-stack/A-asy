<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with('employee')->latest()->paginate(20);
        return view('hr.attendances.index', compact('attendances'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date'        => 'required|date',
            'clock_in'    => 'required',
        ]);

        Attendance::create($request->all());

        return back()->with('success', 'Absensi berhasil dicatat.');
    }
}
