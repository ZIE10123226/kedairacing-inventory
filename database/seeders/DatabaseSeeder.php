<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles
        $adminRoleId = DB::table('roles')->insertGetId([
            'name' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $karyawanRoleId = DB::table('roles')->insertGetId([
            'name' => 'karyawan',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Seed Users
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@kedairacing.com',
            'password' => Hash::make('password'),
            'role_id' => $adminRoleId,
            'is_approved' => true,
        ]);

        User::create([
            'name' => 'Karyawan 1',
            'email' => 'karyawan@kedairacing.com',
            'password' => Hash::make('password'),
            'role_id' => $karyawanRoleId,
            'is_approved' => true,
        ]);
        
        // 3. Seed Default Category & Supplier (Optional dummy data)
        $categoryId = DB::table('categories')->insertGetId([
            'name' => 'Oli',
            'description' => 'Berbagai macam oli mesin dan transmisi',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $supplierId = DB::table('suppliers')->insertGetId([
            'nama' => 'PT Makmur Motor',
            'alamat' => 'Jl. Raya Motor No. 123',
            'telepon' => '081234567890',
            'email' => 'contact@makmurmotor.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
