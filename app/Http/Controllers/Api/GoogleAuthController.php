<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        $redirect = $this->safeRedirectPath($request->query('redirect'));

        if ($redirect !== null) {
            $request->session()->put('google_oauth_redirect', $redirect);
        }

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('login', ['oauth_error' => 'Google sign-in was cancelled or failed.']);
        }

        $googleUser = Socialite::driver('google')->user();

        $user = $this->resolveUser($googleUser);

        if (! $user instanceof User) {
            return redirect()->route('login', ['oauth_error' => $user]);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        $redirect = $request->session()->pull('google_oauth_redirect', '/');

        return redirect()->to($redirect);
    }

    /**
     * Only allow in-app, single-slash paths. Rejects protocol-relative URLs
     * (//host) and backslash variants (/\host) that browsers resolve off-site.
     */
    private function safeRedirectPath(mixed $redirect): ?string
    {
        if (! is_string($redirect) || ! str_starts_with($redirect, '/')) {
            return null;
        }

        if (str_starts_with($redirect, '//') || str_starts_with($redirect, '/\\')) {
            return null;
        }

        return $redirect;
    }

    /**
     * Find an existing user by google_id, or link/create one.
     * Google sign-in is only for regular users — admin accounts are rejected.
     */
    private function resolveUser(object $googleUser): User|string
    {
        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();

        if (! $this->emailIsVerified($googleUser)) {
            return 'Your Google account email is not verified. Please verify it with Google and try again.';
        }

        $user = User::where('google_id', $googleId)->first();

        if ($user) {
            $this->fillProfile($user, $googleUser);
            $user->save();

            return $user;
        }

        $existing = User::where('email', $email)->first();

        if ($existing) {
            if ($existing->isAdmin()) {
                return 'Admin accounts cannot sign in with Google. Please use your password instead.';
            }

            $existing->google_id = $googleId;
            $this->fillProfile($existing, $googleUser);
            $existing->save();

            return $existing;
        }

        return 'Akun belum terdaftar. Silakan daftar terlebih dahulu sebelum masuk dengan Google.';
    }

    private function fillProfile(User $user, object $googleUser): void
    {
        if ($googleUser->getName()) {
            $user->name = $googleUser->getName();
        }

        if ($googleUser->getAvatar()) {
            $user->avatar = $googleUser->getAvatar();
        }
    }

    /**
     * Google only guarantees the address is owned by the account holder when the
     * raw `email_verified` claim is true. Linking/creating accounts on an
     * unverified address would allow account takeover, so fail closed.
     */
    private function emailIsVerified(object $googleUser): bool
    {
        $raw = $googleUser->user ?? null;

        if (! is_array($raw) || ! array_key_exists('email_verified', $raw)) {
            return false;
        }

        return filter_var($raw['email_verified'], FILTER_VALIDATE_BOOLEAN);
    }
}
