<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_page_is_available_and_redirects_after_successful_authentication(): void
    {
        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin-' . uniqid() . '@stockgudang.test',
            'password' => bcrypt('123'),
        ]);

        $this->get(route('login'))
            ->assertOk();

        $this->post(route('login'), [
            'username' => 'Admin',
            'password' => '123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertSame('admin', auth()->user()->name);
    }

    public function test_login_user_field_offers_admin_and_staff_accounts(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('<option value="Admin"', false)
            ->assertSee('<option value="Staff"', false);
    }

    public function test_staff_can_log_in_when_staff_account_exists(): void
    {
        User::factory()->create([
            'name' => 'Staff',
            'email' => 'staff-' . uniqid() . '@stockgudang.test',
            'password' => bcrypt('staff-password'),
        ]);

        $this->post(route('login'), [
            'username' => 'Staff',
            'password' => 'staff-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertSame('Staff', auth()->user()->name);
    }

    public function test_authenticated_user_can_log_out_from_the_sidebar(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Keluar');

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
