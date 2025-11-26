<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

        $token = $organizer->createToken('auth_token', ['organizer'])->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Organizer registered successfully',
            'data' => [
                'organizer' => $organizer,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    public function loginOrganizer(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $organizer = Organizer::where('email', $request->email)->first();

        if (!$organizer || !Hash::check($request->password, $organizer->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        $token = $organizer->createToken('auth_token', ['organizer'])->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'user' => $organizer,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
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

        $token = $buyer->createToken('auth_token', ['buyer'])->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Buyer registered successfully',
            'data' => [
                'buyer' => $buyer,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    public function loginBuyer(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $buyer = Buyer::where('email', $request->email)->first();

        if (!$buyer || !Hash::check($request->password, $buyer->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        $token = $buyer->createToken('auth_token', ['buyer'])->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'buyer' => $buyer,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
        ]);
    }
}
