<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    /**
     * Menampilkan daftar user
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $users = User::all();
        
        return Inertia::render('UserManagement/Index', [
            'users' => $users
        ]);
    }

    /**
     * Menampilkan form tambah user
     *
     * @return \Inertia\Response
     */
    public function create()
    {
        return Inertia::render('UserManagement/Create');
    }

    /**
     * Menyimpan user baru
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:administrasi,guru,murid',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return to_route('user-management.index')->with('message', 'User berhasil ditambahkan');
    }

    /**
     * Menampilkan form edit user
     *
     * @param  \App\Models\User  $user
     * @return \Inertia\Response
     */
    public function edit(User $user)
    {
        return Inertia::render('UserManagement/Edit', [
            'user' => $user->only(['id', 'name', 'email', 'role'])
        ]);
    }

    /**
     * Update data user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|in:administrasi,guru,murid',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        // Update password jika diisi
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
            
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return to_route('user-management.index')->with('message', 'User berhasil diperbarui');
    }

    /**
     * Menghapus user
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $user->delete();

        return to_route('user-management.index')->with('message', 'User berhasil dihapus');
    }
} 