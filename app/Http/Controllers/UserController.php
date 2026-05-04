<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['outlet', 'roles'])
            ->when(!auth()->user()->isSuperAdmin(), function($q) {
                return $q->where('outlet_id', auth()->user()->outlet_id);
            })
            ->latest()
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $outlets = Outlet::all();
        $roles = \App\Models\Role::all();
        return view('users.create', compact('outlets', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:8|confirmed',
            'role_id'   => 'required|exists:roles,id',
            'outlet_id' => 'nullable|exists:outlets,id',
            'is_active' => 'boolean',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'outlet_id' => $request->outlet_id,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('users', 'public');
            $data['photo'] = Storage::url($path);
        }

        $user = User::create($data);

        $user->roles()->sync([$request->role_id]);

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $outlets = Outlet::all();
        $roles = \App\Models\Role::all();
        return view('users.edit', compact('user', 'outlets', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password'  => 'nullable|min:8|confirmed',
            'role_id'   => 'required|exists:roles,id',
            'outlet_id' => 'nullable|exists:outlets,id',
            'is_active' => 'boolean',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'outlet_id' => $request->outlet_id,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($user->photo && file_exists(public_path($user->photo))) {
                @unlink(public_path($user->photo));
            }
            $path = $request->file('photo')->store('users', 'public');
            $data['photo'] = Storage::url($path);
        }

        $user->update($data);
        $user->roles()->sync([$request->role_id]);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if ($user->photo && file_exists(public_path($user->photo))) {
            @unlink(public_path($user->photo));
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus.');
    }
}
