<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManageController extends Controller
{
    // Daftar semua user + filter role & search
    public function index(Request $request)
    {
        $query = User::withCount('complaints')->latest();

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    // Form tambah user baru
    public function create()
    {
        return view('admin.users.create');
    }

    // Simpan user baru
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'phone'    => 'required|string|max:20',
            'address'  => 'nullable|string|max:255',
            'role'     => 'required|in:masyarakat,admin',
            'password' => 'required|string|min:8',
        ], [
            'email.unique'   => 'Email ini sudah terdaftar.',
            'password.min'   => 'Password minimal 8 karakter.',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'address'  => $request->address,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $request->name . '" berhasil ditambahkan.');
    }

    // Form edit user
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    // Update user
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone'    => 'required|string|max:20',
            'address'  => 'nullable|string|max:255',
            'role'     => 'required|in:masyarakat,admin',
            'password' => 'nullable|string|min:8',
        ], [
            'email.unique' => 'Email ini sudah dipakai user lain.',
            'password.min' => 'Password minimal 8 karakter.',
        ]);

        // Cegah admin mengubah role akun sendiri (biar gak nyasar jadi masyarakat)
        if ($user->id === Auth::id() && $request->role !== $user->role) {
            return back()->with('error', 'Tidak bisa mengubah role akun Anda sendiri.')->withInput();
        }

        // Cegah demote admin terakhir jadi masyarakat
        if ($user->role === 'admin' && $request->role === 'masyarakat') {
            $totalAdmin = User::where('role', 'admin')->count();
            if ($totalAdmin <= 1) {
                return back()->with('error', 'Tidak bisa mengubah role admin terakhir. Minimal harus ada 1 admin.')->withInput();
            }
        }

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'address' => $request->address,
            'role'    => $request->role,
        ];

        // Password cuma diganti kalau diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User "' . $user->name . '" berhasil diperbarui.');
    }

    // Hapus user
    public function destroy(User $user)
    {
        // Cegah hapus akun sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak bisa menghapus akun Anda sendiri.');
        }

        // Cegah hapus admin terakhir
        if ($user->role === 'admin') {
            $totalAdmin = User::where('role', 'admin')->count();
            if ($totalAdmin <= 1) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'Tidak bisa menghapus admin terakhir. Minimal harus ada 1 admin.');
            }
        }

        // Cegah hapus user yang masih punya pengaduan (karena cascade delete)
        $complaintCount = $user->complaints()->count();
        if ($complaintCount > 0) {
            return redirect()->route('admin.users.index')
                ->with('error', "User \"{$user->name}\" tidak bisa dihapus karena masih punya {$complaintCount} riwayat pengaduan.");
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}