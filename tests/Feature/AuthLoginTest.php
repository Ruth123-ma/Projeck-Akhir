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
            'username' => 'admin',
            'password' => '123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertSame('admin', auth()->user()->name);
    }
}
