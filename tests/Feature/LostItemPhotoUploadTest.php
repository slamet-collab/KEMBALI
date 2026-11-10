<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\LostItem;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LostItemPhotoUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');
    }

    public function test_user_can_upload_photos_with_a_lost_item_report(): void
    {
        $user = User::factory()->create([
            'role_id' => Role::where('code', 'mahasiswa')->value('id'),
            'status' => 'active',
        ]);
        $category = Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);

        $response = $this->actingAs($user)->post(route('lost-items.store'), [
            'identity_no' => '21060999',
            'phone' => '081234567890',
            'category_id' => $category->id,
            'title' => 'Jam tangan hilang',
            'primary_color' => 'Hitam',
            'description' => 'Jam tangan hitam dengan tali kulit.',
            'last_seen_location' => 'Perpustakaan Pusat',
            'last_seen_date' => '2026-11-09',
            'photo_files' => [
                $this->fakePng('jam-tangan.png'),
            ],
        ]);

        $response->assertRedirect(route('lost-items.index'));

        $lostItem = LostItem::firstOrFail();
        $photoPath = str_replace('/storage/', '', $lostItem->photos[0]);

        Storage::disk('public')->assertExists($photoPath);
        $this->assertStringStartsWith('/storage/lost-items/', $lostItem->photos[0]);
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

        $response = $this->actingAs($user)->from(route('lost-items.create'))->post(route('lost-items.store'), [
            'category_id' => $category->id,
            'title' => 'Jam tangan hilang',
            'primary_color' => 'Hitam',
            'description' => 'Jam tangan hitam dengan tali kulit.',
            'last_seen_location' => 'Perpustakaan Pusat',
            'last_seen_date' => '2026-11-09',
            'photo_files' => [
                $this->fakePng('jam-tangan.png', 5 * 1024 * 1024 + 1),
            ],
        ]);

        $response->assertRedirect(route('lost-items.create'));
        $response->assertSessionHasErrors('photo_files.0');
        $this->assertDatabaseCount('lost_items', 0);
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
