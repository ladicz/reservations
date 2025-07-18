<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Authentication;
use App\Livewire\TablesIndex;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully(): void
    {
        Livewire::test(Authentication::class)
            ->assertStatus(200);
    }

    public function test_component_exists_on_the_page(): void
    {
        $this->get('/login')
            ->assertSeeLivewire(Authentication::class);
    }

    public function test_email_required_valid_format_for_login(): void
    {
        Livewire::test(Authentication::class)
            ->set('email', '')
            ->call('login')
            ->assertHasErrors('email');
        Livewire::test(Authentication::class)
            ->set('email', 'kjhjkk.cc')
            ->call('login')
            ->assertHasErrors('email');
    }

    public function test_password_required_for_login(): void
    {
        Livewire::test(Authentication::class)
            ->set('password', '')
            ->call('login')
            ->assertHasErrors('password');
    }

    public function test_user_can_login_successfully(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'pwd1'
        ]);
        Livewire::test(Authentication::class)
            ->set('email', 'test@example.com')
            ->set('password', 'pwd1')
            ->call('login')
            ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_fails_with_wrong_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'pwd1'
        ]);
        Livewire::test(Authentication::class)
            ->set('email', 'test@example.com')
            ->set('password', 'pwd2')
            ->call('login')
            ->assertSee('Nesprávný email nebo heslo');

        $this->assertGuest();
    }

    public function test_auth_user_can_logout_successfully(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/logout');

        $response->assertRedirect('/');

        $this->assertGuest();
    }
}
