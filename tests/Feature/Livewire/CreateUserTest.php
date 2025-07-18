<?php

namespace Tests\Feature\Livewire;

use App\Livewire\CreateUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_successfully(): void
    {
        Livewire::test(CreateUser::class)
            ->assertStatus(200);
    }

    public function test_component_exists_on_the_page(): void
    {
        $this->get('/register')
            ->assertSeeLivewire(CreateUser::class);
    }

    public function test_name_required_for_login(): void
    {
        Livewire::test(CreateUser::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors('email');
    }

    public function test_email_required_valid_format_for_login(): void
    {
        Livewire::test(CreateUser::class)
            ->set('email', '')
            ->call('save')
            ->assertHasErrors('email');
        Livewire::test(CreateUser::class)
            ->set('email', 'kjhjkk.cc')
            ->call('save')
            ->assertHasErrors('email');
    }

    function test_email_must_be_unique() : void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        Livewire::test(CreateUser::class)
            ->set('email', 'test@example.com')
            ->call('save')
            ->assertHasErrors('email');
    }

    public function test_password_required_for_login()
    {
        Livewire::test(CreateUser::class)
            ->set('password', '')
            ->call('save')
            ->assertHasErrors('password');

        Livewire::test(CreateUser::class)
            ->set('password', 'pwd')
            ->set('password_confirmation', 'pwd555')
            ->call('save')
            ->assertHasErrors(['password' => 'confirmed']);
    }

    function test_anyone_can_register() : void
    {
        $this->assertDatabaseEmpty('users');
        Livewire::test(CreateUser::class)
            ->set('name', 'fake name')
            ->set('email', 'test@example.com')
            ->set('password', 'pswd1')
            ->set('password_confirmation', 'pswd1')
            ->call('save')
            ->assertRedirect('/')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users',[
            'email' => 'test@example.com',
        ]);
    }
}
