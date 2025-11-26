<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OrganizerController extends Controller
{
    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Profile retrieved successfully',
            'data' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $organizer = $request->user();

        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('organizers')->ignore($organizer->id),
            ],
            'password' => 'nullable|string|min:8',
            'current_password' => 'required_with:email,password',
        ]);

        if ($request->has('current_password')) {
            if (!Hash::check($request->current_password, $organizer->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid current password',
                ], 422);
            }
        }

        if ($request->has('name')) {
            $organizer->name = $request->name;
        }

        if ($request->has('email')) {
            $organizer->email = $request->email;
        }

        if ($request->has('password')) {
            $organizer->password = Hash::make($request->password);
        }

        $organizer->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully',
            'data' => $organizer,
        ]);
    }
}
