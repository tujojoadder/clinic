<?php

namespace App\Http\Controllers;

use App\Support\Audit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, RateLimiter};
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create() { return view('auth.login'); }

    public function store(Request $request)
    {
        $data = $request->validate(['email' => 'required|email|max:190', 'password' => 'required|string|max:128']);
        $key = strtolower($data['email']).'|'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) throw ValidationException::withMessages(['email' => 'অনেকবার চেষ্টা হয়েছে। পরে চেষ্টা করুন।']);
        if (!Auth::attempt($data + ['active' => 1])) {
            RateLimiter::hit($key, 900);
            throw ValidationException::withMessages(['email' => 'ইমেইল বা পাসওয়ার্ড সঠিক নয়।']);
        }
        RateLimiter::clear($key);
        $request->session()->regenerate();
        $request->session()->put('last_activity', time());
        Audit::record('login', 'users', Auth::id());
        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
