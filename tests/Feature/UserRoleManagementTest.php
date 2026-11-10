<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_change_a_user_role_and_cannot_assign_super_admin(): void
    {
        $admin = $this->createUser('admin');
        $target = $this->createUser('mahasiswa');
        $staffRole = Role::where('code', 'staf_barang')->firstOrFail();
        $superAdminRole = Role::where('code', 'super_admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.users'))
            ->assertOk()
            ->assertSee(route('admin.users.update-role', $target), false)
            ->assertSee('Simpan')
            ->assertDontSee('value="' . $superAdminRole->id . '"', false);

        $this->put(route('admin.users.update-role', $target), ['role_id' => $staffRole->id])
            ->assertRedirect(route('admin.users'))
            ->assertSessionHas('success');

        $this->assertSame($staffRole->id, $target->fresh()->role_id);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'UPDATE_USER_ROLE',
        ]);

        $this->put(route('admin.users.update-role', $target), ['role_id' => $superAdminRole->id])
            ->assertSessionHasErrors('role_id');
        $this->assertSame($staffRole->id, $target->fresh()->role_id);
    }

    public function test_super_admin_can_change_roles_from_user_management_page(): void
    {
        $superAdmin = $this->createUser('super_admin');
        $target = $this->createUser('mahasiswa');
        $adminRole = Role::where('code', 'admin')->firstOrFail();

        $this->actingAs($superAdmin)
            ->get(route('superadmin.users'))
            ->assertOk()
            ->assertSee(route('superadmin.users.update-role', $target), false);
        $this->get(route('superadmin.roles'))
            ->assertOk()
            ->assertSee(route('superadmin.users'), false)
            ->assertSee('Ubah Role Pengguna');

        $this->from(route('superadmin.users'))
            ->put(route('superadmin.users.update-role', $target), ['role_id' => $adminRole->id])
            ->assertRedirect(route('superadmin.users'))
            ->assertSessionHas('success');

        $this->assertSame($adminRole->id, $target->fresh()->role_id);
    }

    public function test_users_cannot_change_their_own_role_or_edit_roles(): void
    {
        $admin = $this->createUser('admin');
        $adminRole = Role::where('code', 'admin')->firstOrFail();
        $student = $this->createUser('mahasiswa');

        $this->actingAs($admin)
            ->put(route('admin.users.update-role', $admin), ['role_id' => Role::where('code', 'mahasiswa')->value('id')])
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->actingAs($student)
            ->get(route('superadmin.users'))
            ->assertRedirect(route('dashboard.mahasiswa'));

        $this->put(route('superadmin.users.update-role', $student), ['role_id' => $adminRole->id])
            ->assertRedirect(route('dashboard.mahasiswa'));

        $this->assertSame($adminRole->id, $admin->fresh()->role_id);
        $this->assertSame(Role::where('code', 'mahasiswa')->value('id'), $student->fresh()->role_id);
    }

    public function test_admin_cannot_change_super_admin_role(): void
    {
        $admin = $this->createUser('admin');
        $superAdmin = $this->createUser('super_admin');
        $studentRole = Role::where('code', 'mahasiswa')->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.users.update-role', $superAdmin), ['role_id' => $studentRole->id])
            ->assertForbidden();

        $this->assertSame(Role::where('code', 'super_admin')->value('id'), $superAdmin->fresh()->role_id);
    }

    private function createUser(string $roleCode): User
    {
        return User::factory()->create([
            'role_id' => Role::where('code', $roleCode)->value('id'),
            'status' => 'active',
        ]);
    }
}
