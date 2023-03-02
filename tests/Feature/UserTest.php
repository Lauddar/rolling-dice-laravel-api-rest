<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Response;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     * Test login route
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

    /**
     * @test
     * Test that login returns a valid acces token for the user.
     */
    public function testLoginAccesToken()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['user', 'acces_token']);
        $this->assertNotNull($response['acces_token']);
    }

    /**
     * @test
     * Test a login with an invalid email.
     */
    public function testLoginInvalidEmail()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => 'invalid@email.com',
            'password' => 'password'
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     * Test a login with an invalid pasword.
     */
    public function testLoginInvalidPassword()
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'invalidpassword'
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     * Test if the store() method creates a new user.
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

    /**
     * @test
     * Test if the store() method creates a new user with 'anonymus' name if not given.
     */
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

    /**
     * @test
     * Test if the store() method validates that email must be unique.
     */
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

    /**
     * @test
     * Test if the store() method validates email format.
     */
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

    /**
     * @test
     * Test if the store() method validates password format.
     */
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

    /**
     * @test
     * Test if the store() method validates password confirmation.
     */
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

    /**
     * @test
     * Test that update() method update's user's nickname
     */
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


    /**
     * @test
     * Test that update() validates nickname is not empty.
     */
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

    /**
     * @test
     * Test that update() validates nickname is unique.
     */
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

    /**
     * @test
     * Test that index() method returns all users only for Admin role.
     */
    public function testPlayersIndexAdmin()
    {
        $user = User::factory()->create()->assignRole(['Admin']);
        $token = $user->createToken('TestToken')->accessToken;

        User::factory()->count(3)->create(['success_rate' => fake()->randomFloat(2, 0, 100)]);

        $players = User::count();

        $response = $this->actingAs($user)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/players');

        $response->assertStatus(Response::HTTP_OK)->assertJsonCount($players, 'players')->assertJsonStructure(['players']);
    }

    /**
     * @test
     * Test that index() method is forbidden for Player role.
     */
    public function testPlayersIndexPlayer()
    {
        $user = User::factory()->create()->assignRole(['Player']);
        $token = $user->createToken('TestToken')->accessToken;

        User::factory()->count(3)->create(['success_rate' => fake()->randomFloat(2, 0, 100)]);

        $players = User::count();

        $response = $this->actingAs($user)->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/players');

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
