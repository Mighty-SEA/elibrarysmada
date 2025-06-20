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
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        
        $query = User::query()->orderBy('name');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%")
                  ->orWhere('jurusan', 'like', "%{$search}%");
            });
        }
        
        $users = $query->paginate(10)->withQueryString();
        
        return Inertia::render('UserManagement/Index', [
            'users' => $users,
            'filters' => [
                'search' => $search
            ]
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
            'tahun_angkatan' => 'required|numeric|digits:4',
            'jenis_kelamin' => 'nullable|string|max:20',
            'jurusan' => 'nullable|string|max:100',
            'nomor_telepon' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['name', 'username', 'role', 'tahun_angkatan', 'jenis_kelamin', 'jurusan', 'nomor_telepon']);
        $data['password'] = Hash::make($request->password);
        
        // Generate ID with format: tahun_angkatan + sequential number
        $tahunAngkatan = $request->tahun_angkatan;
        $latestUser = User::where('id', 'like', $tahunAngkatan . '%')
            ->orderBy('id', 'desc')
            ->first();
            
        if ($latestUser) {
            $lastId = (int) substr($latestUser->id, 4); // Extract the numeric part
            $newId = $lastId + 1;
        } else {
            $newId = 1;
        }
        
        $data['id'] = $tahunAngkatan . str_pad($newId, 3, '0', STR_PAD_LEFT); // Format: YYYY001, YYYY002, etc.
        
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
            'tahun_angkatan' => 'required|numeric|digits:4',
            'jenis_kelamin' => 'nullable|string|max:20',
            'jurusan' => 'nullable|string|max:100',
            'nomor_telepon' => 'nullable|string|max:20',
            'foto_profil' => 'nullable|image|max:2048',
        ]);
        
        $data = $request->only(['name', 'username', 'role', 'tahun_angkatan', 'jenis_kelamin', 'jurusan', 'nomor_telepon']);
        
        // Check if tahun_angkatan has changed
        if ($user->tahun_angkatan != $request->tahun_angkatan) {
            // Generate new ID with new tahun_angkatan
            $tahunAngkatan = $request->tahun_angkatan;
            $latestUser = User::where('id', 'like', $tahunAngkatan . '%')
                ->where('id', '!=', $user->id)
                ->orderBy('id', 'desc')
                ->first();
                
            if ($latestUser) {
                $lastId = (int) substr($latestUser->id, 4); // Extract the numeric part
                $newId = $lastId + 1;
            } else {
                $newId = 1;
            }
            
            $data['id'] = $tahunAngkatan . str_pad($newId, 3, '0', STR_PAD_LEFT); // Format: YYYY001, YYYY002, etc.
            
            // We need to create a new user and delete the old one since we're changing the primary key
            if ($request->filled('password')) {
                $request->validate([
                    'password' => ['required', 'confirmed', Password::defaults()],
                ]);
                $data['password'] = Hash::make($request->password);
            } else {
                $data['password'] = $user->password;
            }
            
            if ($request->hasFile('foto_profil')) {
                $data['foto_profil'] = $request->file('foto_profil')->store('profile_photos', 'public');
            } else {
                $data['foto_profil'] = $user->foto_profil;
            }
            
            // Create new user with new ID
            $newUser = User::create($data);
            
            // Delete old user
            $oldId = $user->id;
            $user->delete();
            
            return to_route('user-management.index')->with('message', 'User berhasil diperbarui dengan ID baru');
        } else {
            // Normal update without changing ID
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