<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class AuthController
 * 
 * Manages OAuth2 authentication flow with Discord Socialite provider
 * and handles user session state (login, callback, logout).
 */
class AuthController extends Controller
{
    /**
     * Redirect the user to the Discord OAuth2 authorization page.
     */
    public function redirectToDiscord(Request $request)
    {
        if ($request->has('redirect')) {
            session(['post_login_redirect' => $request->input('redirect')]);
        } else {
            session(['post_login_redirect' => url()->previous()]);
        }

        return Socialite::driver('discord')->redirect();
    }

    /**
     * Handle the OAuth2 callback from Discord, registering or updating the user profile.
     */
    public function handleDiscordCallback()
    {
        try {
            $discordUser = Socialite::driver('discord')->user();
        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Authentication failed or was cancelled: ' . $e->getMessage());
        }

        $user = User::updateOrCreate([
            'discord_id' => $discordUser->getId(),
        ], [
            'name' => $discordUser->getName() ?? $discordUser->getNickname() ?? 'Discord User',
            'email' => $discordUser->getEmail() ?? $discordUser->getId() . '@discord.hublybot.local',
            'avatar' => $discordUser->getAvatar(),
            'discord_token' => $discordUser->token,
            'discord_refresh_token' => $discordUser->refreshToken,
        ]);

        Auth::login($user, true);

        $redirectUrl = session()->pull('post_login_redirect', route('dashboard'));

        if (str_contains($redirectUrl, '/auth/redirect') || str_contains($redirectUrl, '/auth/callback')) {
            $redirectUrl = route('dashboard');
        }

        return redirect()->to($redirectUrl);
    }

    /**
     * Log the user out of the application and invalidate the session.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
