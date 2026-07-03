<?php

namespace App\Http\Controllers\Api\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Get owner profile
     */
    public function show(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'mobile_number' => $user->mobile_number,
                'profile_photo_url' => $user->profile_photo_url,
            ]
        ]);
    }

    /**
     * Update owner profile
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile_number' => 'required|string|unique:users,mobile_number,' . $user->id,
            'pin' => 'nullable|string|size:4',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile_number = $request->mobile_number;

        if ($request->filled('pin')) {
            $user->pin_hash = Hash::make($request->pin);
        }

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if exists
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $path = $request->file('profile_photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'mobile_number' => $user->mobile_number,
                'profile_photo_url' => $user->profile_photo_url,
            ]
        ]);
    }
}
