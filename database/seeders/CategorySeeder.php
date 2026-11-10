<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Elektronik', 'icon' => 'laptop', 'description' => 'Laptop, Smartphone, Charger, Powerbank, Tablet, Smartwatch, Earphone.'],
            ['name' => 'Dokumen', 'icon' => 'file-text', 'description' => 'Ijazah, Transkrip, Sertifikat, Kartu Ujian, berkas tugas kampus.'],
            ['name' => 'Kartu Identitas', 'icon' => 'credit-card', 'description' => 'KTM (Kartu Tanda Mahasiswa), KTP, SIM, ATM, E-Money.'],
            ['name' => 'Kunci', 'icon' => 'key', 'description' => 'Kunci motor, mobil, kunci kosan, kunci loker kampus.'],
            ['name' => 'Pakaian', 'icon' => 'shirt', 'description' => 'Jaket almamater, sweater, kemeja, topi, payung, sepatu.'],
            ['name' => 'Tas', 'icon' => 'shopping-bag', 'description' => 'Tas ransel, totebag, tas selempang, pouch, tempat pensil.'],
            ['name' => 'Buku', 'icon' => 'book-open', 'description' => 'Buku kuliah, catatan, modul, novel, binder.'],
            ['name' => 'Alat Tulis', 'icon' => 'pen-tool', 'description' => 'Pulpen, kalkulator ilmiah, penggaris, set alat gambar.'],
            ['name' => 'Aksesori', 'icon' => 'watch', 'description' => 'Kacamata, perhiasan, jam tangan, botol minum/tumbler.'],
            ['name' => 'Lainnya', 'icon' => 'package', 'description' => 'Barang umum lainnya yang ditemukan atau dilaporkan di kampus.'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'description' => $cat['description'],
                ]
            );
        }
    }
}
