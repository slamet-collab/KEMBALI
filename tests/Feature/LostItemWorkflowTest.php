<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FoundItem;
use App\Models\ItemMatch;
use App\Models\LostItem;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LostItemWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected Role $mahasiswaRole;
    protected Role $adminRole;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->mahasiswaRole = Role::where('code', 'mahasiswa')->firstOrFail();
        $this->adminRole = Role::where('code', 'admin')->firstOrFail();
        $this->category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);
    }

    /**
     * Pengujian 1: Kontak & Pengisian NIM/NIP serta No Telepon
     */
    public function test_user_can_submit_contact_and_updates_user_profile(): void
    {
        $student = User::factory()->create([
            'role_id' => $this->mahasiswaRole->id,
            'identity_no' => null,
            'phone' => null,
        ]);

        $response = $this->actingAs($student)->post(route('lost-items.store'), [
            'category_id' => $this->category->id,
            'title' => 'Kalkulator Casio FX-991EX',
            'primary_color' => 'Hitam',
            'description' => 'Kalkulator saintifik tertinggal di lab komputer.',
            'last_seen_location' => 'Lab Komputer Gedung B',
            'last_seen_date' => '2026-11-10',
            'identity_no' => '21060199',
            'phone' => '081298765432',
        ]);

        $response->assertRedirect(route('lost-items.index'));
        $response->assertSessionHas('created_item');

        $student->refresh();
        $this->assertEquals('21060199', $student->identity_no);
        $this->assertEquals('081298765432', $student->phone);
    }

    /**
     * Pengujian 2: Penyimpanan laporan ke tabel lost_items
     */
    public function test_lost_item_report_is_stored_in_database(): void
    {
        $student = User::factory()->create([
            'role_id' => $this->mahasiswaRole->id,
            'identity_no' => '21060200',
            'phone' => '081211112222',
        ]);

        $this->actingAs($student)->post(route('lost-items.store'), [
            'category_id' => $this->category->id,
            'title' => 'Flashdisk Sandisk 64GB',
            'brand' => 'Sandisk',
            'primary_color' => 'Merah Hitam',
            'description' => 'Ada stiker nama Andi di bodi flashdisk.',
            'last_seen_location' => 'Perpustakaan Lantai 1',
            'last_seen_date' => '2026-11-09',
            'last_seen_time' => '09:00',
            'identity_no' => '21060200',
            'phone' => '081211112222',
        ]);

        $this->assertDatabaseHas('lost_items', [
            'user_id' => $student->id,
            'category_id' => $this->category->id,
            'title' => 'Flashdisk Sandisk 64GB',
            'brand' => 'Sandisk',
            'primary_color' => 'Merah Hitam',
            'last_seen_location' => 'Perpustakaan Lantai 1',
            'status' => 'DIPUBLIKASIKAN',
        ]);

        $otherStudent = User::factory()->create(['role_id' => $this->mahasiswaRole->id]);
        $this->actingAs($otherStudent)
            ->get(route('lost-items.index'))
            ->assertOk()
            ->assertSee('Flashdisk Sandisk 64GB');
    }

    /**
     * Pengujian 3: Menampilkan laporan pada daftar & pencarian
     */
    public function test_owner_can_see_pending_report_in_lost_items_index(): void
    {
        $student = User::factory()->create([
            'role_id' => $this->mahasiswaRole->id,
            'identity_no' => '21060201',
            'phone' => '081233334444',
        ]);

        $item = LostItem::create([
            'item_code' => 'HL-2026-0099',
            'user_id' => $student->id,
            'category_id' => $this->category->id,
            'title' => 'Jaket Parasut Biru',
            'primary_color' => 'Biru',
            'description' => 'Jaket parasut warna biru tua ukuran L.',
            'last_seen_location' => 'Kantin Kampus',
            'last_seen_date' => '2026-11-08',
            'status' => 'MENUNGGU_VERIFIKASI',
        ]);

        // Access as owner
        $response = $this->actingAs($student)->get(route('lost-items.index'));
        $response->assertOk();
        $response->assertSee('Jaket Parasut Biru');
        $response->assertSee('Anda (Pelapor)');

        // Access detail
        $detailResponse = $this->actingAs($student)->get(route('lost-items.show', $item->id));
        $detailResponse->assertOk();
        $detailResponse->assertSee('Jaket Parasut Biru');
        $detailResponse->assertSee('081233334444');
    }

    /**
     * Pengujian 4: Aturan Visibilitas & Privasi Kontak Pelapor
     */
    public function test_other_users_cannot_see_unverified_report_and_cannot_see_private_contact(): void
    {
        $reporter = User::factory()->create([
            'role_id' => $this->mahasiswaRole->id,
            'name' => 'Budi Raharjo',
            'identity_no' => '21060300',
            'phone' => '081288889999',
            'email' => 'budirahardjo@kembali.test',
        ]);

        $otherStudent = User::factory()->create([
            'role_id' => $this->mahasiswaRole->id,
        ]);

        $unverifiedItem = LostItem::create([
            'item_code' => 'HL-2026-0100',
            'user_id' => $reporter->id,
            'category_id' => $this->category->id,
            'title' => 'Headset Sony WH-1000XM4',
            'primary_color' => 'Hitam',
            'description' => 'Tertinggal di kelas B201.',
            'last_seen_location' => 'Gedung B Ruang 201',
            'last_seen_date' => '2026-11-07',
            'status' => 'MENUNGGU_VERIFIKASI',
        ]);

        $approvedItem = LostItem::create([
            'item_code' => 'HL-2026-0101',
            'user_id' => $reporter->id,
            'category_id' => $this->category->id,
            'title' => 'Botol Minum Corkcicle',
            'primary_color' => 'Tosca',
            'description' => 'Botol minum warna tosca.',
            'last_seen_location' => 'Gazebo Kampus',
            'last_seen_date' => '2026-11-06',
            'status' => 'DIPUBLIKASIKAN',
        ]);

        // Other student searches/views index: can see approved item, NOT unverified item
        $response = $this->actingAs($otherStudent)->get(route('lost-items.index'));
        $response->assertOk();
        $response->assertSee('Botol Minum Corkcicle');
        $response->assertDontSee('Headset Sony WH-1000XM4');

        // Other student views detail of approved item: contact numbers should NOT be shown
        $detailResponse = $this->actingAs($otherStudent)->get(route('lost-items.show', $approvedItem->id));
        $detailResponse->assertOk();
        $detailResponse->assertSee('Privasi Dilindungi');
        $detailResponse->assertDontSee('081288889999');
        $detailResponse->assertDontSee('21060300');
    }

    /**
     * Pengujian 5: Validasi Input Wajib, Tanggal, dan Nomor Telepon
     */
    public function test_validation_errors_for_empty_fields_and_invalid_formats(): void
    {
        $student = User::factory()->create([
            'role_id' => $this->mahasiswaRole->id,
        ]);

        // Empty title
        $resp1 = $this->actingAs($student)->from(route('lost-items.create'))->post(route('lost-items.store'), [
            'category_id' => $this->category->id,
            'title' => '',
            'primary_color' => 'Hitam',
            'description' => 'Deskripsi valid lebih dari 10 karakter.',
            'last_seen_location' => 'Perpustakaan',
            'last_seen_date' => '2026-11-10',
            'identity_no' => '21060500',
            'phone' => '081234567890',
        ]);
        $resp1->assertSessionHasErrors('title');

        // Invalid date
        $resp2 = $this->actingAs($student)->from(route('lost-items.create'))->post(route('lost-items.store'), [
            'category_id' => $this->category->id,
            'title' => 'Laptop Asus ROG',
            'primary_color' => 'Hitam',
            'description' => 'Deskripsi valid lebih dari 10 karakter.',
            'last_seen_location' => 'Perpustakaan',
            'last_seen_date' => 'bukan-tanggal-valid',
            'identity_no' => '21060500',
            'phone' => '081234567890',
        ]);
        $resp2->assertSessionHasErrors('last_seen_date');

        // Invalid phone number: wrong format
        $resp3 = $this->actingAs($student)->from(route('lost-items.create'))->post(route('lost-items.store'), [
            'category_id' => $this->category->id,
            'title' => 'Laptop Asus ROG',
            'primary_color' => 'Hitam',
            'description' => 'Deskripsi valid lebih dari 10 karakter.',
            'last_seen_location' => 'Perpustakaan',
            'last_seen_date' => '2026-11-10',
            'identity_no' => '21060500',
            'phone' => '12345abc',
        ]);
        $resp3->assertSessionHasErrors('phone');

        // Invalid phone number: exceeds 13 characters
        $resp4 = $this->actingAs($student)->from(route('lost-items.create'))->post(route('lost-items.store'), [
            'category_id' => $this->category->id,
            'title' => 'Laptop Asus ROG',
            'primary_color' => 'Hitam',
            'description' => 'Deskripsi valid lebih dari 10 karakter.',
            'last_seen_location' => 'Perpustakaan',
            'last_seen_date' => '2026-11-10',
            'identity_no' => '21060500',
            'phone' => '08123456789012', // 14 characters
        ]);
        $resp4->assertSessionHasErrors('phone');

        // Invalid NIM/NIP: contains letters
        $resp5 = $this->actingAs($student)->from(route('lost-items.create'))->post(route('lost-items.store'), [
            'category_id' => $this->category->id,
            'title' => 'Laptop Asus ROG',
            'primary_color' => 'Hitam',
            'description' => 'Deskripsi valid lebih dari 10 karakter.',
            'last_seen_location' => 'Perpustakaan',
            'last_seen_date' => '2026-11-10',
            'identity_no' => 'NIM123456',
            'phone' => '081234567890',
        ]);
        $resp5->assertSessionHasErrors('identity_no');
    }

    /**
     * Pengujian AI Matcher: Tetap aman meski AI matcher dipanggil
     */
    public function test_ai_matching_runs_on_lost_item_creation(): void
    {
        $student = User::factory()->create([
            'role_id' => $this->mahasiswaRole->id,
            'identity_no' => '21060901',
            'phone' => '081277778888',
        ]);

        $finder = User::factory()->create([
            'role_id' => $this->mahasiswaRole->id,
        ]);

        // Existing found item in same category & color
        $found = FoundItem::create([
            'item_code' => 'FD-2026-0001',
            'user_id' => $finder->id,
            'category_id' => $this->category->id,
            'title' => 'Kunci Motor Honda Vario',
            'primary_color' => 'Hitam',
            'description' => 'Ditemukan di parkiran Gedung A.',
            'found_location' => 'Parkiran Gedung A',
            'found_date' => '2026-11-09',
            'status' => 'DIPUBLIKASIKAN',
            'holding_option' => 'satpam',
        ]);

        $this->actingAs($student)->post(route('lost-items.store'), [
            'category_id' => $this->category->id,
            'title' => 'Kunci Motor Vario Hitam',
            'primary_color' => 'Hitam',
            'description' => 'Kunci motor hilang di sekitar parkiran.',
            'last_seen_location' => 'Parkiran Gedung A',
            'last_seen_date' => '2026-11-09',
            'identity_no' => '21060901',
            'phone' => '081277778888',
        ]);

        $lostItem = LostItem::where('title', 'Kunci Motor Vario Hitam')->firstOrFail();
        $this->assertDatabaseHas('matches', [
            'lost_item_id' => $lostItem->id,
            'found_item_id' => $found->id,
        ]);
    }
}
