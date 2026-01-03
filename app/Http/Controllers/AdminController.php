<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard() {
        return view('admin.dashboard-admin');
    }

    // ======== CRUD USER ========
    public function users() {
        $users = User::where('role', 'user')->get();
        return view('admin.admin-users', compact('users'));
    }

    public function createUser() {
        return view('admin.admin-user-create');
    }

    public function storeUser(Request $request) {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => 'user'
        ]);

        return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id) {
        $user = User::findOrFail($id);
        return view('admin.admin-user-edit', compact('user'));
    }

    public function updateUser(Request $request, $id) {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email'
        ]);

        $user = User::findOrFail($id);
        $user->update($request->only('name', 'email'));

        return redirect()->route('admin.users')->with('success', 'User berhasil diupdate.');
    }

    public function destroyUser($id) {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus.');
    }

    // ======== CRUD OWNER ========
    public function owners() {
        $owners = User::where('role', 'owner')->get();
        return view('admin.admin-owners', compact('owners'));
    }

    public function createOwner() {
        return view('admin.admin-owner-create');
    }

    public function storeOwner(Request $request) {
        $request->validate([
            'name'     => 'required',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => 'owner'
        ]);

        return redirect()->route('admin.owners')->with('success', 'Owner berhasil ditambahkan.');
    }

    public function editOwner($id) {
        $owner = User::findOrFail($id);
        return view('admin.admin-owner-edit', compact('owner'));
    }

    public function updateOwner(Request $request, $id) {
        $request->validate([
            'name'  => 'required',
            'email' => 'required|email'
        ]);

        $owner = User::findOrFail($id);
        $owner->update($request->only('name', 'email'));

        return redirect()->route('admin.owners')->with('success', 'Owner berhasil diupdate.');
    }

    public function destroyOwner($id) {
        $owner = User::findOrFail($id);
        $owner->delete();

        return redirect()->route('admin.owners')->with('success', 'Owner berhasil dihapus.');
    }

    public function histogram() {
        $UserCount = User::where('role', 'user')->count(); //menghitung jumlah user
        $OwnerCount = User::where('role', 'owner')->count(); //menghitung jumlah owner
        return view('admin.admin-histogram', compact('UserCount', 'OwnerCount'));
    }
}
