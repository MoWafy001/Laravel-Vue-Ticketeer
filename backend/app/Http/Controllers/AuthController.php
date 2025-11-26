<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Responses\JsonResponse;
use App\Models\Organizer;
use App\Models\Buyer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function registerOrganizer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:organizers',
            'password' => 'required|string|min:8',
        ]);

        $organizer = Organizer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = auth('organizer')->login($organizer);

        return JsonResponse::created('Organizer registered successfully', [
            'organizer' => $organizer,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('organizer')->factory()->getTTL() * 60,
        ]);
    }

    public function loginOrganizer(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = auth('organizer')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        return JsonResponse::success('Login successful', [
            'user' => auth('organizer')->user(),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('organizer')->factory()->getTTL() * 60,
        ]);
    }

    public function registerBuyer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:buyers',
            'password' => 'required|string|min:8',
        ]);

        $buyer = Buyer::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = auth('buyer')->login($buyer);

        return JsonResponse::created('Buyer registered successfully', [
            'buyer' => $buyer,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('buyer')->factory()->getTTL() * 60,
        ]);
    }

    public function loginBuyer(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = auth('buyer')->attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        return JsonResponse::success('Login successful', [
            'buyer' => auth('buyer')->user(),
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth('buyer')->factory()->getTTL() * 60,
        ]);
    }

    public function logout(Request $request)
    {
        // Determine guard based on token payload or try both?
        // Ideally the middleware sets the guard.
        // For now, we can try to logout from the current guard if known, or just invalidate token.

        try {
            if (auth('organizer')->check()) {
                auth('organizer')->logout();
            } elseif (auth('buyer')->check()) {
                auth('buyer')->logout();
            }
        } catch (\Exception $e) {
            // Token might be invalid already
        }

        return JsonResponse::success('Logged out successfully');
    }
}
