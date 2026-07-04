<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Karyawan;
use App\Http\Requests\Admin\StoreKaryawanRequest;
use App\Http\Requests\Admin\UpdateKaryawanRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
            $perPage = request('per_page', 15);
            $karyawans = Karyawan::with('user')->latest()->paginate($perPage);
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil dimuat',
                'data' => $karyawans->items(),
                'total' => $karyawans->total(),
                'per_page' => $karyawans->perPage(),
                'current_page' => $karyawans->currentPage(),
                'last_page' => $karyawans->lastPage(),
            ]);
        }
        return view('admin.karyawan.index');
    }

    public function create()
    {
        return view('admin.karyawan.create');
    }

    public function store(StoreKaryawanRequest $request)
    {
        $roleMapping = [
            'admin' => 'admin',
            'kasir' => 'kasir',
            'karyawan' => 'karyawan',
        ];

        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $roleMapping[$request->jabatan] ?? 'karyawan',
            'nomor_hp' => $request->nomor_hp,
        ]);

        $data = [
            'user_id' => $user->id,
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'nomor_hp' => $request->nomor_hp,
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('karyawan', 'public');
        }

        $karyawan = Karyawan::create($data);
        $karyawan->load('user');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil ditambahkan',
                'data' => $karyawan,
            ]);
        }

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function show($id)
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil dimuat',
                'data' => $karyawan,
            ]);
        }
        return redirect()->route('admin.karyawan.index');
    }

    public function edit($id)
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Data karyawan berhasil dimuat',
                'data' => $karyawan,
            ]);
        }

        return view('admin.karyawan.edit', compact('karyawan'));
    }

    public function update(UpdateKaryawanRequest $request, $id)
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);

        $roleMapping = [
            'admin' => 'admin',
            'kasir' => 'kasir',
            'karyawan' => 'karyawan',
        ];

        $userData = [
            'name' => $request->nama,
            'email' => $request->email,
            'role' => $roleMapping[$request->jabatan] ?? 'karyawan',
            'nomor_hp' => $request->nomor_hp,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $karyawan->user->update($userData);

        $data = [
            'nama' => $request->nama,
            'jabatan' => $request->jabatan,
            'nomor_hp' => $request->nomor_hp,
        ];

        if ($request->hasFile('foto')) {
            if ($karyawan->foto) {
                Storage::disk('public')->delete($karyawan->foto);
            }
            $data['foto'] = $request->file('foto')->store('karyawan', 'public');
        }

        $karyawan->update($data);
        $karyawan->load('user');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil diperbarui',
                'data' => $karyawan,
            ]);
        }

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Karyawan berhasil diperbarui');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::with('user')->findOrFail($id);

        if ($karyawan->foto) {
            Storage::disk('public')->delete($karyawan->foto);
        }

        $userId = $karyawan->user_id;
        $karyawan->delete();

        User::find($userId)?->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Karyawan berhasil dihapus',
            ]);
        }

        return redirect()->route('admin.karyawan.index')
            ->with('success', 'Karyawan berhasil dihapus');
    }
}
