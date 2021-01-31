<?php

namespace Tests\Feature;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Mocking of Socialite added with assistance from:
     * https://stackoverflow.com/a/40618999/1225977
     */
    public function testAuthenticationCallbackSuccess()
    {
        $abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
        $abstractUser
            ->shouldReceive('getId')
            ->andReturn(rand())
            ->shouldReceive('getName')
            ->andReturn(Str::random(10))
            ->shouldReceive('getEmail')
            ->andReturn(Str::random(10) . '@gmail.com')
            ->shouldReceive('getAvatar')
            ->andReturn('https://en.gravatar.com/userimage');

        Socialite::shouldReceive('driver->stateless->user')->andReturn($abstractUser);

        // User is created on first login via socialite
        $this->get(route('login.callback', ['provider' => 'github']))
            ->assertStatus(Response::HTTP_CREATED);

        // User is retrieved on subsequent logins via socialite
        $this->get(route('login.callback', ['provider' => 'github']))
            ->assertOk();
    }

    public function testAuthenticationCallbackFailure()
    {
        Socialite::shouldReceive('driver->stateless->user')
            ->andthrow($this->mock(ClientException::class));
        $this->get(route('login.callback', ['provider' => 'github']))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['error' => 'Invalid credentials provided.']);
    }

    public function testAuthenticationProviderValidation()
    {
        $this->get(route('login', ['provider' => 'invalid']))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['error' => 'Please login using github.']);

        $this->get(route('login.callback', ['provider' => 'invalid']))
            ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson(['error' => 'Please login using github.']);
    }

    public function testAuthenticationRedirect()
    {
        $this->get(route('login', ['provider' => 'github']))
            ->assertRedirect();
    }
}
