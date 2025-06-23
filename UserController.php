<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Menampilkan semua data user
    public function index()
    {
        // $user = User::all();
        $user = User::with('bookings')
            ->orderBy('id', 'desc')
            ->get();

        return view('dashboard.page.user.index', compact('user'));
    }

    // Menampilkan form untuk menambah user
    public function create()
    {
        return view('dashboard.page.user.create');
    }

    // Menyimpan data user baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user.index');
    }

    // Menampilkan form untuk edit user
    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);

            return view('dashboard.page.user.edit', compact('user'));
        } catch (\Throwable $th) {
            return redirect()->route('user.index')->withErrors('User tidak ditemukan.');
        }

    }

    // Menyimpan perubahan data user
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->username = $request->username;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return redirect()->route('user.index');
    }

    // Menghapus user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('user.index');
    }
}
