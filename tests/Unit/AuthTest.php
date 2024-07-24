<?php

namespace Tests\Unit;

use App\Models\Task;
use App\Models\User;
// use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    /**
     * A basic test example.
     */
    protected User $user;
    protected AuthService $authService;

    public function test_that_true_is_true(): void
    {
        $this->assertTrue(true);
    }

    protected function setUp(): void
    {

        parent::setUp();
        
        $this->user = new User();

        $this->authService = new AuthService($this->user);

    }

    public function test_sign_up_user_success()
    {

        $data = [

            'name' => 'Test User',

            'email' => 'test@example.com',

            'password' => '12345678'

        ];

        $response = $this->authService->signUpUser($data);

        $responseData = $response->getData(true);

        $this->assertEquals(201, $response->status());

        $this->assertEquals('success', $responseData['status']);

        $this->assertArrayHasKey('token', $responseData);

        $this->assertDatabaseHas('users', ['email' => $data['email']]);

    }

    public function test_sign_in_user_success()
    {

        $user = User::factory()->create(['password' => '12345678']);

        $data = [

            'email' => $user->email,

            'password' => '12345678'

        ];

        $response = $this->authService->signInUser($data);

        $responseData = $response->getData(true);

        $this->assertEquals(200, $response->status());

        $this->assertEquals('success', $responseData['status']);

        $this->assertArrayHasKey('token', $responseData);

    }

    public function test_sign_in_user_invalid_credentials()
    {

        $data = [

            'email' => 'nonexistent@example.com',

            'password' => 'wrongpassword'

        ];

        $response = $this->authService->signInUser($data);

        $responseData = $response->getData(true);


        $this->assertEquals(401, $response->status());

        $this->assertEquals('error', $responseData['status']);

        $this->assertEquals('Invalid credentials!', $responseData['message']);

    }

    public function test_sign_in_user_validation_error()
    {
        
        $data = [
            
            'email' => '',
            
            'password' => ''
            
        ];

        $response = $this->authService->signInUser($data);
        
        $responseData = $response->getData(true);

        $this->assertEquals(422, $response->status());
        
        $this->assertEquals('error', $responseData['status']);
        
        $this->assertArrayHasKey('email', $responseData['message']);
        
        $this->assertArrayHasKey('password', $responseData['message']);
        
    }
}
