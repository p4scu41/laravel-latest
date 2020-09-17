<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Testing\Assert as PHPUnit;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    protected $user;

    /**
     * @var string
     */
    protected $access_token;

    /**
     * Check the status of the api
     *
     * @return void
     */
    public function test_it_reachs_the_api()
    {
        $response = $this->get('/api/status');

        $response->assertStatus(200);
    }

    /**
     * Check the status of the api
     *
     * @return \Illuminate\Testing\TestResponse
     */
    public function test_it_can_login()
    {
        // $this->withoutExceptionHandling();

        $this->user = User::factory()->create();

        $response = $this->postJson(
            '/api/auth/login',
            ['email' => $this->user->email, 'password' => 'password']
        );
        $auth = $response->decodeResponseJson();

        $response->assertStatus(200);
        PHPUnit::assertArrayHasKey('access_token', $auth);

        $this->access_token = $auth['access_token'];

        return $response;
    }

    /**
     * Check the status of the api
     *
     * @return void
     */
    public function test_it_returns_me()
    {
        $this->test_it_can_login();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->access_token)
            ->postJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonFragment(Arr::only($this->user->toArray(), ['name', 'email']));
    }

    /**
     * Check the status of the api
     *
     * @return void
     */
    public function test_it_can_refresh_access_token()
    {
        $this->test_it_can_login();

        $response = $this->withHeader('Authorization', 'Bearer ' . $this->access_token)
            ->postJson('/api/auth/refresh');
        $auth = $response->decodeResponseJson();

        $response->assertStatus(200);
        PHPUnit::assertArrayHasKey('access_token', $auth);

        $this->access_token = $auth['access_token'];
    }
}
