<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'The given data was invalid.',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY); // 422
        }

        $credentials = $validator->validated();
        $throttleKey = strtolower($request->input('email')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'message' => "Too many login attempts. Please try again in {$seconds} seconds.",
                'errors' => ['email' => ["Too many login attempts. Please try again in {$seconds} seconds."]]
            ], Response::HTTP_TOO_MANY_REQUESTS); // 429
        }

        if (Auth::attempt($credentials, true)) { 
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);

            return response()->json([
                'status' => 'success',
                'message' => 'Authentication successful.',
                'redirect' => route('apps.main')
            ], Response::HTTP_OK);
        }

        RateLimiter::hit($throttleKey, 60);

        return response()->json([
            'message' => 'These credentials do not match our records.',
            'errors' => [
                'email' => ['These credentials do not match our records.']
            ]
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        if ($request->hasSession()) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login');
    }
}