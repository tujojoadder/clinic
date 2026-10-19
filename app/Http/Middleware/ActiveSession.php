<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActiveSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()->active || time() - $request->session()->get('last_activity', time()) > 1800) {
            Auth::logout();
            $request->session()->invalidate();
            return redirect()->route('login');
        }
        $request->session()->put('last_activity', time());
        return $next($request);
    }
}
