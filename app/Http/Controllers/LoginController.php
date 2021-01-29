<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\JsonResponse;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    public function redirectToProvider(string $provider)
    {
        if ($invalid = $this->validateProvider($provider)) {
            return $invalid;
        }

        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function handleProviderCallback(string $provider) {
        if ($invalid = $this->validateProvider($provider)) {
            return $invalid;
        }

        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return response()->json(['error' => 'Invalid credentials provided.'], 422);
        }

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

        return new JsonResponse($userCreated, 200, ['Access-Token' => $token]);
    }

    protected function validateProvider(string $provider)
    {
        if (!in_array($provider, ['github'])) {
            return new JsonResponse(['error' => 'Please login using github'], 422);
        }
        return null;
    }
}
