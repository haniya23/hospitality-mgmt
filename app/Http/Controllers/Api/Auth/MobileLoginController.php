<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileLoginController extends Controller
{
    /**
     * Handle mobile login with PIN
     */
    public function login(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string',
            'pin' => 'required|string',
            'device_name' => 'string|max:255',
        ]);

        $user = User::where('mobile_number', $request->mobile_number)
                   ->where('is_active', true)
                   ->first();

        if (!$user || !Hash::check($request->pin, $user->pin_hash)) {
            throw ValidationException::withMessages([
                'mobile_number' => ['Invalid mobile number or PIN.'],
            ]);
        }

        // Create token
        $token = $user->createToken($request->device_name ?? 'mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'mobile_number' => $user->mobile_number,
                    'email' => $user->email,
                    'profile_photo_url' => $user->profile_photo_url,
                    'user_type' => $user->is_admin ? 'admin' : 'owner',
                ],
            ],
        ], 200);
    }
}
