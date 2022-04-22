<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login_redirects_successfully()
    {
        factory(User::class)->create([
            'email' => 'test@laravel.com',
            'password' => bcrypt('test_phase_squad')
        ]);

        $response = $this->post('/login', [
            'email' => 'test@laravel.com',
            'password' => 'test_phase_squad'
        ]);

        $response->assertStatus(302);

        $response->assertRedirect('/home');
    }




}
