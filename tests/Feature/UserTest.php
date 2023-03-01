<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * @test
     * Valid login
     */
    public function testLogin()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertOk();

        $user->delete();
    }

    /** @test */
    public function testAccesToken()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['user', 'acces_token']);
        $this->assertNotNull($response['acces_token']);

        $user->delete();
    }

    public function testInvalidEmail()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => 'invalid@email.com',
            'password' => 'password'
        ]);

        $response->assertStatus(401); // Returns unauthorized

        $user->delete();
    }

    public function testInvalidPassword()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'invalidpassword'
        ]);

        $response->assertStatus(401); // Returns unauthorized

        $user->delete();
    }

    /**
     * Test if the store() method creates a new user.
     *
     * @return void
     */
    public function testStore()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->makeOne();

        $userData = [
            'nickname' => 'Test',
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/api/players', $userData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'User created succesfully.',
                'user' => [
                    'nickname' => 'Test',
                    'email' => $userData['email'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);

        User::where('email', $userData['email'])->delete();
    }

    public function testAnonymousNickNameIfNull()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->makeOne();

        $userData = [
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/api/players', $userData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'User created succesfully.',
                'user' => [
                    'nickname' => 'anonymous',
                    'email' => $userData['email'],
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
        ]);

        User::where('email', $userData['email'])->delete();
    }

    public function testStoreIfEmailTaken()
    {
        $user = User::factory()->create();

        $userData = [
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/api/players', $userData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'The email is already in use',
            ]);

        User::where('email', $userData['email'])->delete();
    }

    
    public function testStoreInvalidEmailFormat()
    {
        $userData = [
            'email' => 'test',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/api/players', $userData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'The email field must be a valid email address.'
            ]);
    }

    public function testStoreInvalidPasswordFormat()
    {
        $userData = [
            'email' => 'test@test.com',
            'password' => 'p',
            'password_confirmation' => 'p',
        ];

        $response = $this->post('/api/players', $userData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'The password field must be at least 8 characters.'
            ]);
    }

    public function testStoreInvalidPasswordConfirmation()
    {
        $userData = [
            'email' => 'test@test.com',
            'password' => '12345678',
            'password_confirmation' => '12345677',
        ];

        $response = $this->post('/api/players', $userData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'The password confirmation does not match.'
            ]);
    }
}
