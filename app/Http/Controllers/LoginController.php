<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('social');
    }

    public function redirectToProvider(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)
            ->stateless()
            ->redirect();
    }

    public function handleProviderCallback(string $provider): Response
    {
        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response()->view('oauth.callback', ['payload' => [
                'code' => 422,
                'error' => 'Invalid credentials provided.'
            ]], 422);
        }

        /** @var User $userCreated */
        $userCreated = User::firstOrCreate(
            [
                'email' => $user->getEmail()
            ],
            [
                'email_verified_at' => now(),
                'name' => $user->getName(),
                'status' => true,
            ]
        );
        $userCreated->providers()->updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $user->getId(),
            ],
            [
                'avatar' => $user->getAvatar()
            ]
        );
        $token = $userCreated->createToken('spa-token')->plainTextToken;

        $code = ($userCreated->wasRecentlyCreated ? 201 : 200);

        return response()->view('oauth.callback', ['payload' => [
            'code' => $code,
            'token' => $token
        ]], $code);
    }
}
