<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StaffAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.staff-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string',
            'pin' => 'required|string|size:4',
        ]);

        $user = User::where('mobile_number', $request->mobile_number)
                   ->where('is_active', true)
                   ->where('is_staff', true)
                   ->first();

        if (!$user || !Hash::check($request->pin, $user->pin_hash)) {
            throw ValidationException::withMessages([
                'mobile_number' => ['Invalid mobile number or PIN.'],
            ]);
        }

        // Check if staff has active assignments
        if (!$user->getActiveStaffAssignments()->count()) {
            throw ValidationException::withMessages([
                'mobile_number' => ['No active staff assignments found.'],
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        return redirect()->route('staff.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/staff/login');
    }
}
