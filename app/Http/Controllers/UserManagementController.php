<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
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
        $users = User::orderBy('name')->paginate(10);
        
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
            'username' => 'required|string|max:255|unique:users,username',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:administrasi,guru,murid',
            'jenis_kelamin' => 'nullable|string|max:20',
            'jurusan' => 'nullable|string|max:100',
            'nomor_telepon' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'username', 'role', 'jenis_kelamin', 'jurusan', 'nomor_telepon']);
        $data['password'] = Hash::make($request->password);
        if ($request->hasFile('foto_profil')) {
            $data['foto_profil'] = $request->file('foto_profil')->store('profile_photos', 'public');
        }
        User::create($data);
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
            'user' => $user->only(['id', 'name', 'username', 'role', 'jenis_kelamin', 'jurusan', 'nomor_telepon', 'foto_profil'])
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
            'username' => 'required|string|max:255|unique:users,username,'.$user->id,
            'role' => 'required|in:administrasi,guru,murid',
            'jenis_kelamin' => 'nullable|string|max:20',
            'jurusan' => 'nullable|string|max:100',
            'nomor_telepon' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|max:2048',
        ]);
        $data = $request->only(['name', 'username', 'role', 'jenis_kelamin', 'jurusan', 'nomor_telepon']);
        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
            $data['password'] = Hash::make($request->password);
        }
        if ($request->hasFile('foto_profil')) {
            $data['foto_profil'] = $request->file('foto_profil')->store('profile_photos', 'public');
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