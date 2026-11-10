<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Claim;
use App\Models\FoundItem;
use App\Models\HandoverRecord;
use App\Models\ItemMatch;
use App\Models\ItemStorage;
use App\Models\LostItem;
use App\Models\Notification;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $roleMhs = Role::where('code', 'mahasiswa')->first();
        $roleDosen = Role::where('code', 'dosen')->first();
        $roleStaf = Role::where('code', 'staf_barang')->first();
        $roleAdmin = Role::where('code', 'admin')->first();
        $roleSuper = Role::where('code', 'super_admin')->first();

        // 1. Create Users
        $mhs = User::updateOrCreate(
            ['identity_no' => '21060123'],
            [
                'email' => 'andi.pratama.kembali@gmail.com',
                'name' => 'Andi Pratama',
                'identity_no' => '21060123',
                'phone' => '081234567891',
                'role_id' => $roleMhs->id,
                'status' => 'active',
                'password' => Hash::make('password'),
            ]
        );

        $mhs2 = User::updateOrCreate(
            ['identity_no' => '21060188'],
            [
                'email' => 'budi.santoso.kembali@gmail.com',
                'name' => 'Budi Santoso',
                'identity_no' => '21060188',
                'phone' => '081299887766',
                'role_id' => $roleMhs->id,
                'status' => 'active',
                'password' => Hash::make('password'),
            ]
        );

        $dosen = User::updateOrCreate(
            ['identity_no' => '198503102010121003'],
            [
                'email' => 'hendra.kusuma.kembali@gmail.com',
                'name' => 'Dr. Ir. Hendra Kusuma, M.T.',
                'identity_no' => '198503102010121003',
                'phone' => '081234567892',
                'role_id' => $roleDosen->id,
                'status' => 'active',
                'password' => Hash::make('password'),
            ]
        );

        $staf = User::updateOrCreate(
            ['identity_no' => 'STAF-001'],
            [
                'email' => 'staf.barang.kembali@gmail.com',
                'name' => 'Slamet Riyadi (Staf Lost & Found)',
                'identity_no' => 'STAF-001',
                'phone' => '081234567893',
                'role_id' => $roleStaf->id,
                'status' => 'active',
                'password' => Hash::make('password'),
            ]
        );

        $admin = User::updateOrCreate(
            ['identity_no' => 'ADM-001'],
            [
                'email' => 'admin.operasional.kembali@gmail.com',
                'name' => 'Ahmad Fauzi (Admin Operasional)',
                'identity_no' => 'ADM-001',
                'phone' => '081234567894',
                'role_id' => $roleAdmin->id,
                'status' => 'active',
                'password' => Hash::make('password'),
            ]
        );

        $superadmin = User::updateOrCreate(
            ['identity_no' => 'SA-001'],
            [
                'email' => 'superadmin.kembali@gmail.com',
                'name' => 'Super Administrator KEMBALI',
                'identity_no' => 'SA-001',
                'phone' => '081234567895',
                'role_id' => $roleSuper->id,
                'status' => 'active',
                'password' => Hash::make('password'),
            ]
        );

        // Fetch Categories
        $catElektronik = Category::where('slug', 'elektronik')->first();
        $catTas = Category::where('slug', 'tas')->first();
        $catKunci = Category::where('slug', 'kunci')->first();
        $catAksesori = Category::where('slug', 'aksesori')->first();
        $catDokumen = Category::where('slug', 'dokumen')->first();

        // 2. Sample Lost Items
        $lost1 = LostItem::updateOrCreate(
            ['item_code' => 'HL-2026-0001'],
            [
                'user_id' => $mhs->id,
                'category_id' => $catTas->id,
                'title' => 'Tas Ransel Hitam',
                'brand' => 'Eiger',
                'primary_color' => 'Hitam',
                'description' => 'Tas ransel hitam Eiger dengan gantungan kunci karakter anime, isi buku catatan dan mukena.',
                'last_seen_location' => 'Gedung A Rektorat Lantai 2',
                'last_seen_date' => '2026-09-28',
                'last_seen_time' => '10:30:00',
                'photos' => ['https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=500&auto=format&fit=crop'],
                'status' => 'DIPUBLIKASIKAN',
                'verified_by' => $admin->id,
            ]
        );

        $lost2 = LostItem::updateOrCreate(
            ['item_code' => 'HL-2026-0002'],
            [
                'user_id' => $mhs->id,
                'category_id' => $catElektronik->id,
                'title' => 'Earphone Soundcore Anker Hitam',
                'brand' => 'Anker Soundcore',
                'primary_color' => 'Hitam Metalik',
                'description' => 'Earphone wireless bluetooth Anker warna hitam, terdapat goresan halus di bagian casing daya.',
                'last_seen_location' => 'Perpustakaan Pusat Lantai 2',
                'last_seen_date' => '2026-09-27',
                'last_seen_time' => '14:15:00',
                'photos' => ['https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=500&auto=format&fit=crop'],
                'status' => 'DIPUBLIKASIKAN',
                'verified_by' => $admin->id,
            ]
        );

        $lost3 = LostItem::updateOrCreate(
            ['item_code' => 'HL-2026-0003'],
            [
                'user_id' => $dosen->id,
                'category_id' => $catDokumen->id,
                'title' => 'Dompet Kulit Cokelat Braun Buffel',
                'brand' => 'Braun Buffel',
                'primary_color' => 'Cokelat Tua',
                'description' => 'Dompet pria kulit cokelat tua merk Braun Buffel, berisi KTP, SIM A, KTA Dosen, dan beberapa kartu ATM.',
                'last_seen_location' => 'Ruang Dosen Gedung C',
                'last_seen_date' => '2026-09-25',
                'last_seen_time' => '11:00:00',
                'photos' => ['https://images.unsplash.com/photo-1627123424574-724758594e93?w=500&auto=format&fit=crop'],
                'status' => 'DIPUBLIKASIKAN',
                'verified_by' => $admin->id,
            ]
        );

        // 3. Sample Found Items
        $found1 = FoundItem::updateOrCreate(
            ['item_code' => 'FD-2026-0001'],
            [
                'user_id' => $mhs2->id,
                'category_id' => $catElektronik->id,
                'title' => 'Earphone Wireless TWS White',
                'brand' => 'Soundcore',
                'primary_color' => 'Putih / White',
                'description' => 'Ditemukan earphone wireless bluetooth warna putih di meja belajar Perpustakaan Pusat Lantai 2.',
                'found_location' => 'Perpustakaan Pusat Lantai 2',
                'found_date' => '2026-09-27',
                'found_time' => '15:00:00',
                'photos' => ['https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=500&auto=format&fit=crop'],
                'status' => 'DITAMPUNG',
                'is_public' => true,
                'holding_option' => 'satpam',
                'holding_location_name' => 'Pos Satpam Perpustakaan',
                'verified_by' => $admin->id,
            ]
        );

        $found2 = FoundItem::updateOrCreate(
            ['item_code' => 'FD-2026-0002'],
            [
                'user_id' => $mhs2->id,
                'category_id' => $catKunci->id,
                'title' => 'Kunci Motor Honda Vario dengan Gantungan Leather',
                'brand' => 'Honda',
                'primary_color' => 'Hitam / Perak',
                'description' => 'Kunci motor Honda Vario ditemukan tergeletak di tribun Parkiran Gedung C.',
                'found_location' => 'Parkiran Gedung C',
                'found_date' => '2026-09-26',
                'found_time' => '09:10:00',
                'photos' => ['https://images.unsplash.com/photo-1582139329536-e7284fece509?w=500&auto=format&fit=crop'],
                'status' => 'DIPUBLIKASIKAN',
                'is_public' => true,
                'holding_option' => 'satpam',
                'holding_location_name' => 'Pos Satpam Rektorat',
                'verified_by' => $admin->id,
            ]
        );

        $found3 = FoundItem::updateOrCreate(
            ['item_code' => 'FD-2026-0003'],
            [
                'user_id' => $mhs2->id,
                'category_id' => $catAksesori->id,
                'title' => 'Botol Minum Tumbler Corkcicle Biru',
                'brand' => 'Corkcicle',
                'primary_color' => 'Biru Navy',
                'description' => 'Tumbler stainless biru navy merk Corkcicle ditemukan di Ruang Baca Bersama Gedung A lantai 1.',
                'found_location' => 'Ruang Baca Gedung A',
                'found_date' => '2026-09-24',
                'found_time' => '16:30:00',
                'photos' => ['https://images.unsplash.com/photo-1602143407151-7111542de6e8?w=500&auto=format&fit=crop'],
                'status' => 'DIPUBLIKASIKAN',
                'is_public' => true,
                'holding_option' => 'saya_simpan',
                'holding_location_name' => 'Disimpan oleh Penemu (Budi Santoso)',
                'verified_by' => $admin->id,
            ]
        );

        // 4. Sample Item Storage (Physical Storage recorded by Staff)
        ItemStorage::updateOrCreate(
            ['storage_code' => 'KB-2026-0001'],
            [
                'found_item_id' => $found1->id,
                'building' => 'Gedung Rektorat (Pos Satpam)',
                'room' => 'Ruang Lost & Found',
                'cupboard' => 'Lemari A',
                'shelf' => 'Rak 03',
                'qr_code_path' => 'qr/KB-2026-0001.png',
                'stored_by' => $staf->id,
                'received_at' => now()->subHours(5),
                'notes' => 'Barang fisiknya sudah diserahterimakan dari penemu ke pos satpam.',
            ]
        );

        // 5. AI Matches
        ItemMatch::updateOrCreate(
            [
                'lost_item_id' => $lost2->id,
                'found_item_id' => $found1->id,
            ],
            [
                'match_score' => 94,
                'confidence_level' => 'SANGAT_TINGGI',
                'match_reasons' => [
                    'Kategori barang identik: Elektronik / Earphone Wireless',
                    'Lokasi persis berdekatan: Perpustakaan Pusat Lantai 2',
                    'Rentang waktu sangat sesuai: Hilang 27 Sep 14:15, Ditemukan 27 Sep 15:00 (selisih 45 menit)',
                    'Fitur ciri fisik Soundcore/TWS verified via Computer Vision & NLP Text Similarity',
                ],
                'status' => 'PENDING',
            ]
        );

        ItemMatch::updateOrCreate(
            [
                'lost_item_id' => $lost3->id,
                'found_item_id' => $found3->id,
            ],
            [
                'match_score' => 65,
                'confidence_level' => 'SEDANG',
                'match_reasons' => [
                    'Kategori barang berbeda namun lokasi gedung berdekatan',
                    'Waktu penemuan dalam selisih 24 jam',
                ],
                'status' => 'PENDING',
            ]
        );

        // 6. Claims
        $claim = Claim::updateOrCreate(
            ['claim_code' => 'CLM-2026-0001'],
            [
                'lost_item_id' => $lost2->id,
                'found_item_id' => $found1->id,
                'user_id' => $mhs->id,
                'ownership_proof' => 'Saya memiliki e-nota pembelian Soundcore di Tokopedia atas nama Andi Pratama serta sisa serial number pada dus kemasan.',
                'special_characteristics' => 'Ada goresan kecil berbentuk titik di bawah port USB-C charging case.',
                'proof_photos' => ['https://images.unsplash.com/photo-1590658268037-6bf12165a8df?w=500&auto=format&fit=crop'],
                'status' => 'SIAP_DIAMBIL',
                'verified_by' => $admin->id,
                'verified_at' => now()->subHours(2),
            ]
        );

        // 7. Handover Record (Dummy for test)
        HandoverRecord::updateOrCreate(
            ['handover_code' => 'HO-2026-0001'],
            [
                'claim_id' => $claim->id,
                'found_item_id' => $found1->id,
                'claimed_by' => $mhs->id,
                'handed_over_by' => $staf->id,
                'handover_date' => now()->subHours(1),
                'recipient_identity_no' => '21060123',
                'notes' => 'Telah diserahkan di Pos Satpam Rektorat setelah verifikasi kartu mahasiswa.',
                'photos' => ['https://images.unsplash.com/photo-1606220588913-b3aacb4d2f46?w=500&auto=format&fit=crop'],
            ]
        );

        // 8. Notifications
        Notification::create([
            'user_id' => $mhs->id,
            'title' => 'Kandidat Kecocokan AI Ditemukan!',
            'message' => 'Sistem menemukan barang yang cocok (94%) dengan laporan Earphone Soundcore Anda di Perpustakaan Pusat.',
            'type' => 'success',
            'is_read' => false,
            'action_url' => '/dashboard/mahasiswa',
        ]);

        Notification::create([
            'user_id' => $mhs->id,
            'title' => 'Klaim Disetujui: Siap Diambil!',
            'message' => 'Pengajuan klaim Anda disetujui admin. Silakan ambil barang di Pos Satpam Rektorat (Kode Storage: KB-2026-0001).',
            'type' => 'info',
            'is_read' => false,
            'action_url' => '/dashboard/mahasiswa',
        ]);
    }
}
