<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles
        $roles = [
            ['name' => 'Mahasiswa', 'code' => 'mahasiswa', 'description' => 'Akses pelaporan, pencarian, AI match, dan klaim barang mahasiswa.'],
            ['name' => 'Dosen', 'code' => 'dosen', 'description' => 'Akses pelaporan formal dan pengajuan klaim barang dosen.'],
            ['name' => 'Staf Barang', 'code' => 'staf_barang', 'description' => 'Petugas operasional penerimaan, penyimpanan, QR Code, dan serah terima barang.'],
            ['name' => 'Admin', 'code' => 'admin', 'description' => 'Pengelola verifikasi laporan, tinjauan klaim, dan data operasional.'],
            ['name' => 'Super Admin', 'code' => 'super_admin', 'description' => 'Akses penuh manajemen sistem, role, permission, kategori, log, dan backup.'],
        ];

        $roleModels = [];
        foreach ($roles as $roleData) {
            $roleModels[$roleData['code']] = Role::updateOrCreate(
                ['code' => $roleData['code']],
                $roleData
            );
        }

        // 2. Permissions
        $permissions = [
            ['name' => 'Lihat Dashboard', 'code' => 'view_dashboard', 'description' => 'Mengakses dashboard role masing-masing.'],
            ['name' => 'Laporkan Barang Hilang', 'code' => 'report_lost', 'description' => 'Membuat laporan barang hilang baru.'],
            ['name' => 'Laporkan Barang Ditemukan', 'code' => 'report_found', 'description' => 'Membuat laporan barang ditemukan baru.'],
            ['name' => 'Lihat Barang Publik', 'code' => 'view_public_items', 'description' => 'Melihat daftar barang ditemukan publik.'],
            ['name' => 'Ajukan Klaim', 'code' => 'claim_item', 'description' => 'Mengajukan klaim kepemilikan barang.'],
            ['name' => 'Kelola Penyimpanan & QR', 'code' => 'manage_storage', 'description' => 'Mencatat lokasi penyimpanan dan cetak/scan QR Code.'],
            ['name' => 'Verifikasi Laporan', 'code' => 'verify_report', 'description' => 'Memeriksa dan menyetujui laporan barang.'],
            ['name' => 'Verifikasi Klaim', 'code' => 'verify_claim', 'description' => 'Memeriksa bukti klaim pengguna.'],
            ['name' => 'Kelola Pengguna', 'code' => 'manage_users', 'description' => 'Membuat dan mengelola akun staf/admin.'],
            ['name' => 'Kelola Sistem & Log', 'code' => 'manage_system', 'description' => 'Konfigurasi AI, backup, dan log aktivitas.'],
        ];

        $permModels = [];
        foreach ($permissions as $pData) {
            $permModels[$pData['code']] = Permission::updateOrCreate(
                ['code' => $pData['code']],
                $pData
            );
        }

        // 3. Assign Permissions
        // Mahasiswa & Dosen
        $studentPerms = ['view_dashboard', 'report_lost', 'report_found', 'view_public_items', 'claim_item'];
        foreach ($studentPerms as $code) {
            $roleModels['mahasiswa']->permissions()->syncWithoutDetaching([$permModels[$code]->id]);
            $roleModels['dosen']->permissions()->syncWithoutDetaching([$permModels[$code]->id]);
        }

        // Staf Barang
        $staffPerms = ['view_dashboard', 'manage_storage', 'view_public_items'];
        foreach ($staffPerms as $code) {
            $roleModels['staf_barang']->permissions()->syncWithoutDetaching([$permModels[$code]->id]);
        }

        // Admin
        $adminPerms = ['view_dashboard', 'verify_report', 'verify_claim', 'view_public_items', 'manage_storage'];
        foreach ($adminPerms as $code) {
            $roleModels['admin']->permissions()->syncWithoutDetaching([$permModels[$code]->id]);
        }

        // Super Admin (All)
        $roleModels['super_admin']->permissions()->sync(array_column($permModels, 'id'));
    }
}
