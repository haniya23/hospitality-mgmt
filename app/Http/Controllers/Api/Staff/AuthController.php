<?php

namespace App\Http\Controllers\Api\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StaffMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Staff login - returns API token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'string|max:255',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Check if user has staff access
        $staffMember = $user->staffMember;
        if (!$staffMember) {
            throw ValidationException::withMessages([
                'email' => ['This account does not have staff access.'],
            ]);
        }

        // Check if staff is active
        if ($staffMember->status !== 'active') {
            throw ValidationException::withMessages([
                'email' => ['Your staff account is not active. Please contact your manager.'],
            ]);
        }

        // Create token
        $token = $user->createToken($request->device_name ?? 'staff-device')->plainTextToken;

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
                    'email' => $user->email,
                    'mobile_number' => $user->mobile_number,
                ],
                'staff' => [
                    'id' => $staffMember->id,
                    'uuid' => $staffMember->uuid,
                    'staff_role' => $staffMember->staff_role,
                    'job_title' => $staffMember->job_title,
                    'department' => $staffMember->department ? [
                        'id' => $staffMember->department->id,
                        'name' => $staffMember->department->name,
                    ] : null,
                    'property' => [
                        'id' => $staffMember->property->id,
                        'uuid' => $staffMember->property->uuid,
                        'name' => $staffMember->property->name,
                    ],
                ],
            ],
        ], 200);
    }

    /**
     * Staff logout - revoke current token
     */
    public function logout(Request $request)
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ], 200);
    }

    /**
     * Get authenticated staff profile
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $staffMember = $user->staffMember;

        if (!$staffMember) {
            return response()->json([
                'success' => false,
                'message' => 'Staff member not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile_number' => $user->mobile_number,
                ],
                'staff' => [
                    'id' => $staffMember->id,
                    'uuid' => $staffMember->uuid,
                    'staff_role' => $staffMember->staff_role,
                    'job_title' => $staffMember->job_title,
                    'employment_type' => $staffMember->employment_type,
                    'status' => $staffMember->status,
                    'join_date' => $staffMember->join_date?->format('Y-m-d'),
                    'phone' => $staffMember->phone,
                    'department' => $staffMember->department ? [
                        'id' => $staffMember->department->id,
                        'uuid' => $staffMember->department->uuid,
                        'name' => $staffMember->department->name,
                    ] : null,
                    'property' => [
                        'id' => $staffMember->property->id,
                        'uuid' => $staffMember->property->uuid,
                        'name' => $staffMember->property->name,
                    ],
                    'supervisor' => $staffMember->supervisor ? [
                        'id' => $staffMember->supervisor->id,
                        'uuid' => $staffMember->supervisor->uuid,
                        'name' => $staffMember->supervisor->user->name,
                        'job_title' => $staffMember->supervisor->job_title,
                    ] : null,
                ],
            ],
        ], 200);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $request->validate([
            'device_name' => 'string|max:255',
        ]);

        $user = $request->user();

        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken($request->device_name ?? 'staff-device')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 200);
    }
}

