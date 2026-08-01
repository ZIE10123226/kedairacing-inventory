<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserApprovalController extends Controller
{
    // Ambil daftar user yang belum di-approve
    public function index()
    {
        $users = User::with('role')->where('is_approved', false)->latest()->get();
        return response()->json($users);
    }

    // Setujui pendaftaran
    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_approved' => true]);

        return response()->json(['message' => 'Akun karyawan berhasil disetujui.']);
    }

    // Tolak (Hapus data yang belum disetujui)
    public function reject($id)
    {
        $user = User::findOrFail($id);
        $user->forceDelete(); // Menghapus permanen dari database

        return response()->json(['message' => 'Pendaftaran karyawan telah ditolak.']);
    }
}
