<?php

namespace Tests\Unit;

use App\Exceptions\UserEmailExistsException;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_can_create_new_user(): void
    {
        $data = [
            'email' => fake()->email(),
            'name' => fake()->name(),
            'password' => fake()->password(),
        ];

        $this->assertDatabaseEmpty('users');
        $service = new UserService();
        $service->createUser($data);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_service_cannot_create_user_with_existing_email(): void
    {
        $userEmail = fake()->email();

        $this->assertDatabaseEmpty('users');

        User::factory()->create([
            'email' => $userEmail,
        ]);

        $data = [
            'email' => $userEmail,
            'name' => fake()->name(),
            'password' => fake()->password(),
        ];

        $service = new UserService();

        $this->assertThrows(function() use($service, $data){
            $service->createUser($data);
        },UserEmailExistsException::class);

        $this->assertDatabaseCount('users', 1);
    }
}
