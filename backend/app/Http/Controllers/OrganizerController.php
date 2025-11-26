<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Responses\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class OrganizerController extends Controller
{
    public function profile(Request $request)
    {
        return JsonResponse::success('Profile retrieved successfully', $request->user());
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
                return JsonResponse::error('Invalid current password', null, 422);
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

        return JsonResponse::success('Profile updated successfully', $organizer);
    }
}
