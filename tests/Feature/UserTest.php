<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

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
    }

    /** @test */
    public function testLoginAccesToken()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['user', 'acces_token']);
        $this->assertNotNull($response['acces_token']);
    }

    public function testLoginInvalidEmail()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => 'invalid@email.com',
            'password' => 'password'
        ]);

        $response->assertStatus(401); // Returns unauthorized
    }

    public function testLoginInvalidPassword()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'invalidpassword'
        ]);

        $response->assertStatus(401); // Returns unauthorized
    }

    /**
     * Test if the store() method creates a new user.
     *
     * @return void
     */
    public function testStore()
    {
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
    }

    public function testStoreAnonymousNickNameIfNull()
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

    public function testUpdateWithValidData()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->accessToken;
        $newNickname = 'newNickname';

        $response = $this->actingAs($user)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put("/api/players/{$user->id}", ['nickname' => $newNickname]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Nickname updated succesfully.',
                'user' => [
                    'nickname' => $newNickname
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nickname' => $newNickname
        ]);
    }

    public function testUpdateEmptyField()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->accessToken;
        $oldNickname = $user->nickname;
        $newNickname = '';

        $response = $this->actingAs($user)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put("/api/players/{$user->id}", ['nickname' => $newNickname]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Operation failed.',
                'user' => [
                    'nickname' => $oldNickname,
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'nickname' => $oldNickname
        ]);
    }

    public function testUpdateDuplicatedNickname()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $newNickname = $user1->nickname;

        $token = $user2->createToken('TestToken')->accessToken;

        $response = $this->actingAs($user2)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put("/api/players/{$user2->id}", ['nickname' => $newNickname]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Nickname cannot be updated because it is already taken.',
            ]);
    }
}
