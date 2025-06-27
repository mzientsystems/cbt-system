<?php

// app/Http/Controllers/Auth/LoginController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    protected function credentials(Request $request)
    {
        return [
            'username' => $request->username,
            'password' => $request->password,
            'status' => 'active'
        ];
    }

    protected function validateLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        $throttleKey = Str::lower($request->input('username')) . '|' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->fireLockoutEvent($request);
            throw ValidationException::withMessages([
                'username' => [trans('auth.throttle', [
                    'seconds' => RateLimiter::availableIn($throttleKey)
                ])],
            ]);
        }

        $user = \App\Models\User::where('username', $request->username)->first();
        
        if ($user) {
            if ($user->isLocked()) {
                throw ValidationException::withMessages([
                    'username' => ['Account is temporarily locked. Please try again later.'],
                ]);
            }

            if (!$user->isActive()) {
                throw ValidationException::withMessages([
                    'username' => ['Account is not active. Please contact administrator.'],
                ]);
            }
        }

        $credentials = $this->credentials($request);
        
        if ($this->guard()->attempt($credentials, $request->boolean('remember'))) {
            if ($user) {
                $user->resetLoginAttempts();
                $user->updateLastLogin();
            }
            
            RateLimiter::clear($throttleKey);
            return true;
        }

        if ($user) {
            $user->incrementLoginAttempts();
        }

        RateLimiter::hit($throttleKey);
        return false;
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->needsPasswordChange()) {
            return redirect()->route('password.change')->with('warning', 'You need to change your password.');
        }

        // Redirect based on role
        switch ($user->role->name) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'lecturer':
                return redirect()->route('lecturer.dashboard');
            case 'student':
                return redirect()->route('student.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}
