<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
    }

    public function test_users_can_authenticate(): void
    {
        $role = Role::where('code', 'mahasiswa')->first();
        $user = User::factory()->create([
            'email' => 'testuser@kembali.test',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'login' => 'testuser@kembali.test',
            'password' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $role = Role::where('code', 'mahasiswa')->first();
        User::factory()->create([
            'email' => 'testuser@kembali.test',
            'password' => bcrypt('password123'),
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $response = $this->post('/login', [
            'login' => 'testuser@kembali.test',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('login');
    }

    public function test_users_can_logout(): void
    {
        $role = Role::where('code', 'mahasiswa')->first();
        $user = User::factory()->create([
            'role_id' => $role->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_role_based_dashboard_redirect(): void
    {
        $roleAdmin = Role::where('code', 'admin')->first();
        $admin = User::factory()->create([
            'role_id' => $roleAdmin->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertRedirect(route('dashboard.admin'));
    }

    public function test_registration_page_does_not_allow_role_selection(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertDontSee('Akun baru akan dibuat dengan role Mahasiswa.')
            ->assertDontSee('name="role_code"', false);
    }

    public function test_new_accounts_are_always_registered_as_students(): void
    {
        $response = $this->post('/register', [
            'name' => 'Pengguna Baru',
            'identity_no' => '21061001',
            'email' => 'pengguna.baru@kembali.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role_code' => 'admin',
            'terms' => '1',
        ]);

        $studentRole = Role::where('code', 'mahasiswa')->firstOrFail();
        $user = User::where('email', 'pengguna.baru@kembali.test')->firstOrFail();

        $this->assertSame($studentRole->id, $user->role_id);
        $this->assertAuthenticatedAs($user);
        $response->assertRedirect(route('dashboard.mahasiswa'));
    }

    public function test_registration_rejects_letters_in_identity_number(): void
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Pengguna Baru',
            'identity_no' => '2106AB01',
            'email' => 'nim.invalid@kembali.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => '1',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('identity_no');
        $this->assertDatabaseMissing('users', ['email' => 'nim.invalid@kembali.test']);
    }

    public function test_registration_identity_field_only_accepts_numeric_input(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('inputmode="numeric"', false)
            ->assertSee('pattern="[0-9]+"', false)
            ->assertSee('replace(/[^0-9]/g', false);
    }
}
