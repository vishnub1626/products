<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    const API_ENDPOINT = '/api/v1/signup';

    /**
     * @test
     */
    public function a_user_can_signup_using_email_and_password()
    {
        $this->withoutExceptionHandling();

        $this->json('POST', self::API_ENDPOINT, [
            'name' => 'Vishnu B',
            'email' => 'vishnu@example.com',
            'password' => 'braindamage'
        ])->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'name', 'email'
                ]
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Vishnu B',
            'email' => 'vishnu@example.com',
        ]);
    }

    /**
     * @test
     */
    public function a_422_response_is_returned_if_no_email()
    {
        $this->json('POST', self::API_ENDPOINT, [
            'name' => 'Vishnu B',
            'email' => '',
            'password' => 'secretsauce'
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
    public function a_422_response_is_returned_if_no_password()
    {
        $this->json('POST', self::API_ENDPOINT, [
            'name' => 'Vishnu B',
            'email' => 'vishnu@example.com',
            'password' => ''
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
    public function a_422_response_is_returned_on_invalid_email()
    {
        $this->json('POST', self::API_ENDPOINT, [
            'name' => 'Vishnu B',
            'email' => 'invalidemail',
            'password' => 'secretsauce'
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => [
                        'The email must be a valid email address.'
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function a_422_response_if_password_is_not_a_minimum_of_8_characters()
    {
        $this->json('POST', self::API_ENDPOINT, [
            'name' => 'Vishnu B',
            'email' => 'vishnu@example.com',
            'password' => 'secret'
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'password' => [
                        "The password must be at least 8 characters."
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function email_must_be_unique()
    {
        factory(User::class)->create([
            'email' => 'vishnu@example.com'
        ]);

        $this->json('POST', self::API_ENDPOINT, [
            'name' => 'Vishnu B',
            'email' => 'vishnu@example.com',
            'password' => 'secretsauce'
        ])->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'email' => [
                        "The email has already been taken."
                    ]
                ]
            ]);
    }

    /**
     * @test
     */
    public function signup_returns_a_response_with_api_token()
    {
        $response = $this->json('POST', self::API_ENDPOINT, [
            'name' => 'Vishnu B',
            'email' => 'vishnu@example.com',
            'password' => 'secretsauce'
        ])->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['api_token', 'name', 'email']
            ]);

        $token = explode('|', $response->json()['data']['api_token'])[1];

        $this->assertDatabaseHas('personal_access_tokens', [
            'token' => hash('sha256', $token),
        ]);
    }
}
