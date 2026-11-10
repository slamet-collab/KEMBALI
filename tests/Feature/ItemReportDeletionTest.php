<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Claim;
use App\Models\FoundItem;
use App\Models\LostItem;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemReportDeletionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');
    }

    public function test_report_owner_can_delete_lost_and_found_reports(): void
    {
        $owner = $this->createUser();
        $category = $this->createCategory();
        $lostItem = $this->createLostItem($owner, $category);
        $foundItem = $this->createFoundItem($owner, $category);
        Storage::disk('public')->put('lost-items/lost-photo.png', 'photo');
        Storage::disk('public')->put('found-items/found-photo.png', 'photo');
        $lostItem->update(['photos' => [Storage::disk('public')->url('lost-items/lost-photo.png')]]);
        $foundItem->update(['photos' => [Storage::disk('public')->url('found-items/found-photo.png')]]);

        $this->actingAs($owner)
            ->get(route('lost-items.index', ['my_reports' => 1]))
            ->assertOk()
            ->assertSee(route('lost-items.destroy', $lostItem), false)
            ->assertSee('Hapus');
        $this->get(route('found-items.index'))
            ->assertOk()
            ->assertSee(route('found-items.destroy', $foundItem), false)
            ->assertSee('Hapus');

        $this->delete(route('lost-items.destroy', $lostItem))
            ->assertRedirect(route('lost-items.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('lost_items', ['id' => $lostItem->id]);
        Storage::disk('public')->assertMissing('lost-items/lost-photo.png');

        $this->delete(route('found-items.destroy', $foundItem))
            ->assertRedirect(route('found-items.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('found_items', ['id' => $foundItem->id]);
        Storage::disk('public')->assertMissing('found-items/found-photo.png');
    }

    public function test_other_users_cannot_delete_reports(): void
    {
        $owner = $this->createUser();
        $otherUser = $this->createUser();
        $category = $this->createCategory();
        $lostItem = $this->createLostItem($owner, $category);
        $foundItem = $this->createFoundItem($owner, $category);

        $this->actingAs($otherUser)
            ->delete(route('lost-items.destroy', $lostItem))
            ->assertForbidden();

        $this->delete(route('found-items.destroy', $foundItem))
            ->assertForbidden();

        $this->assertDatabaseHas('lost_items', ['id' => $lostItem->id]);
        $this->assertDatabaseHas('found_items', ['id' => $foundItem->id]);
    }

    public function test_reports_with_claims_cannot_be_deleted(): void
    {
        $owner = $this->createUser();
        $claimant = $this->createUser();
        $category = $this->createCategory();
        $lostItem = $this->createLostItem($owner, $category);
        $foundItem = $this->createFoundItem($owner, $category);

        Claim::create([
            'claim_code' => 'CLM-TEST-0001',
            'lost_item_id' => $lostItem->id,
            'found_item_id' => $foundItem->id,
            'user_id' => $claimant->id,
            'ownership_proof' => 'Bukti kepemilikan.',
            'special_characteristics' => 'Ciri khusus.',
        ]);

        $this->actingAs($owner)
            ->delete(route('lost-items.destroy', $lostItem))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->delete(route('found-items.destroy', $foundItem))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertDatabaseHas('lost_items', ['id' => $lostItem->id]);
        $this->assertDatabaseHas('found_items', ['id' => $foundItem->id]);
    }

    private function createUser(): User
    {
        return User::factory()->create([
            'role_id' => Role::where('code', 'mahasiswa')->value('id'),
            'status' => 'active',
        ]);
    }

    private function createCategory(): Category
    {
        return Category::create([
            'name' => 'Elektronik',
            'slug' => 'elektronik',
        ]);
    }

    private function createLostItem(User $user, Category $category): LostItem
    {
        return LostItem::create([
            'item_code' => 'HL-' . uniqid(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Barang hilang',
            'primary_color' => 'Hitam',
            'description' => 'Barang hilang dengan deskripsi yang cukup.',
            'last_seen_location' => 'Perpustakaan',
            'last_seen_date' => '2026-11-09',
        ]);
    }

    private function createFoundItem(User $user, Category $category): FoundItem
    {
        return FoundItem::create([
            'item_code' => 'FD-' . uniqid(),
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Barang ditemukan',
            'primary_color' => 'Hitam',
            'description' => 'Barang ditemukan dengan deskripsi yang cukup.',
            'found_location' => 'Perpustakaan',
            'found_date' => '2026-11-09',
            'holding_option' => 'saya_simpan',
        ]);
    }
}
