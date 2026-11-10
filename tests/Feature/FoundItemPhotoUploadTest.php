<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FoundItem;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FoundItemPhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');
    }

    public function test_found_item_report_page_displays_the_stepper_and_photo_dropzone(): void
    {
        $user = User::factory()->create([
            'role_id' => Role::where('code', 'mahasiswa')->value('id'),
            'status' => 'active',
        ]);

        $this->actingAs($user)
            ->get(route('found-items.create'))
            ->assertOk()
            ->assertSee('Tahapan laporan barang ditemukan')
            ->assertSee('Tarik & Lepas foto di sini, atau klik untuk memilih file', false);
    }

    public function test_user_can_upload_photos_with_a_found_item_report(): void
    {
        $user = User::factory()->create([
            'role_id' => Role::where('code', 'mahasiswa')->value('id'),
            'status' => 'active',
        ]);
        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $response = $this->actingAs($user)->post(route('found-items.store'), [
            'category_id' => $category->id,
            'title' => 'Jam tangan ditemukan',
            'primary_color' => 'Hitam',
            'description' => 'Jam tangan hitam ditemukan di perpustakaan.',
            'found_location' => 'Perpustakaan Pusat',
            'found_date' => '2026-11-09',
            'holding_option' => 'saya_simpan',
            'photo_files' => [
                $this->fakePng('jam-tangan.png'),
            ],
        ]);

        $response->assertRedirect(route('dashboard.redirect'));

        $foundItem = FoundItem::firstOrFail();
        $photoPath = str_replace('/storage/', '', $foundItem->photos[0]);

        Storage::disk('public')->assertExists($photoPath);
        $this->assertStringStartsWith('/storage/found-items/', $foundItem->photos[0]);
        $this->assertSame('MENUNGGU_VERIFIKASI', $foundItem->status);
    }

    public function test_staff_must_confirm_found_item_before_it_is_public(): void
    {
        $reporter = User::factory()->create([
            'role_id' => Role::where('code', 'mahasiswa')->value('id'),
            'status' => 'active',
        ]);
        $staff = User::factory()->create([
            'role_id' => Role::where('code', 'staf_barang')->value('id'),
            'status' => 'active',
        ]);
        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);
        $foundItem = FoundItem::create([
            'item_code' => 'FD-2026-0099',
            'user_id' => $reporter->id,
            'category_id' => $category->id,
            'title' => 'Dompet untuk konfirmasi staf',
            'primary_color' => 'Cokelat',
            'description' => 'Dompet cokelat ditemukan di perpustakaan kampus.',
            'found_location' => 'Perpustakaan',
            'found_date' => '2026-11-10',
            'status' => 'MENUNGGU_VERIFIKASI',
            'is_public' => true,
            'holding_option' => 'saya_simpan',
        ]);

        $this->get(route('public.found-items'))
            ->assertOk()
            ->assertDontSee('Dompet untuk konfirmasi staf');

        $this->actingAs($staff)
            ->get(route('staff.verifications'))
            ->assertOk()
            ->assertSee('Dompet untuk konfirmasi staf')
            ->assertSee(route('staff.verify-found', $foundItem), false);

        $this->put(route('staff.verify-found', $foundItem), ['action' => 'approve'])
            ->assertRedirect(route('staff.verifications'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('found_items', [
            'id' => $foundItem->id,
            'status' => 'DIPUBLIKASIKAN',
            'verified_by' => $staff->id,
        ]);

        $this->get(route('public.found-items'))
            ->assertOk()
            ->assertSee('Dompet untuk konfirmasi staf');
    }

    public function test_regular_users_cannot_access_found_item_verification(): void
    {
        $student = User::factory()->create([
            'role_id' => Role::where('code', 'mahasiswa')->value('id'),
            'status' => 'active',
        ]);

        $this->actingAs($student)
            ->get(route('staff.verifications'))
            ->assertRedirect(route('dashboard.mahasiswa'));
    }

    public function test_photo_upload_rejects_files_over_five_megabytes(): void
    {
        $user = User::factory()->create([
            'role_id' => Role::where('code', 'mahasiswa')->value('id'),
            'status' => 'active',
        ]);
        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $response = $this->actingAs($user)->from(route('found-items.create'))->post(route('found-items.store'), [
            'category_id' => $category->id,
            'title' => 'Jam tangan ditemukan',
            'primary_color' => 'Hitam',
            'description' => 'Jam tangan hitam ditemukan di perpustakaan.',
            'found_location' => 'Perpustakaan Pusat',
            'found_date' => '2026-11-09',
            'holding_option' => 'saya_simpan',
            'photo_files' => [
                $this->fakePng('jam-tangan.png', 5 * 1024 * 1024 + 1),
            ],
        ]);

        $response->assertRedirect(route('found-items.create'));
        $response->assertSessionHasErrors('photo_files.0');
        $this->assertDatabaseCount('found_items', 0);
    }

    private function fakePng(string $name, ?int $size = null): UploadedFile
    {
        $contents = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');

        if ($size !== null) {
            $contents .= str_repeat("\0", max(0, $size - strlen($contents)));
        }

        return UploadedFile::fake()->createWithContent($name, $contents);
    }
}
