<?php

namespace App\Http\Controllers;

use Google\Client as GoogleClient;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    // Redirect to Google
    public function redirectToGoogle()
    {
        // مهم: stateless() لتجاوز مشاكل session على localhost
        return Socialite::driver('google')->scopes([
        'openid',
        'profile',
        'email',
        'https://www.googleapis.com/auth/calendar' // صلاحية الوصول للـ Calendar
    ])->with([
            'access_type' => 'offline', // 👈 مهم
            'prompt' => 'consent'       // 👈 مهم جداً
        ])
        ->redirect();
    }

    // Callback from Google


public function handleGoogleCallback()
{
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::updateOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName(),
            'google_id' => $googleUser->getId(),
            'password' => bcrypt(Str::random(16)),
            'google_token' => $googleUser->token, // access token
            'google_refresh_token' => $googleUser->refreshToken ?? null, // مهم باش نجيب الأحداث من بعد
        ]
    );
     // 👇 هذا هو المهم
    if ($googleUser->refreshToken) {
        $user->google_refresh_token = $googleUser->refreshToken;
        $user->save();
    }

    Auth::login($user); // 👈 مهم

    session()->regenerate(); // 👈 تثبيت السيشن

    return redirect('home');
}
}