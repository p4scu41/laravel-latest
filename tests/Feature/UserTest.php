<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * JWT
     *
     * @var string
     */
    public $access_token;

    /**
     * @var array
     */
    public $user = [
        'name'     => 'New user',
        'email'    => 'user@new.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    /**
     * Check the status of the api
     *
     * @return void
     */
    public function test_it_can_register_a_user()
    {
        $this
            ->postJson('/api/users', $this->user)
            ->assertStatus(201);
    }

    /**
     * Check the status of the api
     *
     * @return void
     */
    public function test_it_can_update_a_user()
    {
        $user = factory(User::class)->create();
        $updated_data = array_merge($user->toArray(), ['name' => 'Updated name']);

        $this->getToken();

        $new_info = $this
            ->withHeader('Authorization', 'Bearer ' . $this->access_token)
            ->putJson(
                '/api/users/'.$user->id,
                $updated_data
            )
            ->assertStatus(200)
            ->decodeResponseJson();

        $this->assertEquals($updated_data['name'], $new_info['data']['name']);
    }

    /**
     * Check the status of the api
     *
     * @return void
     */
    public function test_it_can_delete_a_user()
    {
        $user = factory(User::class)->create();

        $this->getToken();

        $this
            ->withHeader('Authorization', 'Bearer ' . $this->access_token)
            ->delete('/api/users/'.$user->id)
            ->assertStatus(200);
    }

    /**
     * @return void
     */
    public function getToken()
    {
        $user = factory(User::class)->create();

        $response = $this->postJson(
            '/api/auth/login',
            ['email' => $user->email, 'password' => 'password']
        );
        $auth = $response->decodeResponseJson();

        $response->assertStatus(200);

        $this->access_token = $auth['access_token'];
    }
}
