<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyLoginTest extends TestCase
{
    use RefreshDatabase;

    const API_ENDPOINT = '/api/v1/login';

    /**
     * @test
     */
    public function a_user_can_login_and_get_an_api_token()
    {
        $user = factory(User::class)->create();

        $response = $this->json('POST', self::API_ENDPOINT, [
            'email' => $user->email,
            'password' => 'password',
        ])->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['api_token', 'name', 'email']
            ]);

        $token = explode('|', $response->json()['data']['api_token'])[1];

        $this->assertDatabaseHas('personal_access_tokens', [
            'token' => hash('sha256', $token),
        ]);
    }

    /**
     * @test
     */
    public function email_is_required_for_authentication()
    {
        $this->json('POST', self::API_ENDPOINT, [
            'email' => '',
            'password' => 'password',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'The email field is required.'
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function password_is_required_for_authentication()
    {
        $this->json('POST', self::API_ENDPOINT, [
            'email' => 'vishnu@example.com',
            'password' => '',
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => [
                        'The password field is required.'
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function gives_401_on_failed_authentication_attempt()
    {
        $this->json('POST', self::API_ENDPOINT, [
            'email' => 'vishnu@example.com',
            'password' => 'password',
        ])->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.'
            ]);
    }
}
