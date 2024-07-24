<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    protected User $user;
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    protected function setUp(): void
    {

        parent::setUp();

        $this->user = User::factory()->create();

    }

    public function test_user_can_sign_up()
    {

        $data = [

            'name' => 'Test User',

            'email' => 'test@example.com',

            'password' => '12345678',

            'password_confirmation' => '12345678'

        ];

        $response = $this->postJson('/api/v1/auth/signup', $data);

        $response->assertStatus(201)

                 ->assertJson([

                     'status' => 'success',

                     'message' => 'User created successfully',

                     'token' => true

                 ]);

        $this->assertDatabaseHas('users', ['email' => $data['email']]);

    }

    public function test_user_can_sign_in()
    {

        $user = User::factory()->create(['password' => '12345678']);

        $data = [

            'email' => $user->email,

            'password' => '12345678'

        ];

        $response = $this->postJson('/api/v1/auth/signin', $data);

        $response->assertStatus(200)

                 ->assertJson([

                     'status' => 'success',

                     'message' => 'Signed in successfully',

                     'token' => true
                     
                 ]);

    }

    public function test_sign_in_with_invalid_credentials()
    {

        $data = [

            'email' => 'invalidemail@example.com',

            'password' => 'wrongpassword'

        ];

        $response = $this->postJson('/api/v1/auth/signin', $data);

        $response->assertStatus(401)

                 ->assertJson([

                     'status' => 'error',

                     'message' => 'Invalid credentials!',

                 ]);

    }

    public function test_sign_in_validation_errors()
    {

        $data = [

            'email' => '',

            'password' => ''

        ];

        $response = $this->postJson('/api/v1/auth/signin', $data);

        $response->assertStatus(422)

                 ->assertJson([

                     'status' => 'error',

                     'message' => [

                         'email' => ['The email field is required.'],

                         'password' => ['The password field is required.']

                     ]

                 ]);

    }
}
