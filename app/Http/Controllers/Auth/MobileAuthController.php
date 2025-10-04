<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class MobileAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.mobile-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'mobile_number' => 'required|string',
            'pin' => 'required|string|size:4',
        ]);

        $user = User::where('mobile_number', $request->mobile_number)
                   ->where('is_active', true)
                   ->first();

        if (!$user || !Hash::check($request->pin, $user->pin_hash)) {
            throw ValidationException::withMessages([
                'mobile_number' => ['Invalid mobile number or PIN.'],
            ]);
        }

        Auth::login($user, $request->boolean('remember'));

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showRegistrationForm()
    {
        return view('auth.mobile-register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile_number' => 'required|string|unique:users',
            'pin' => 'required|string|size:4|confirmed',
            'email' => 'nullable|email|unique:users',
            'subscription_choice' => 'required|in:trial,starter,professional',
        ]);

        // Set subscription details based on choice
        $subscriptionData = [];
        if ($request->subscription_choice === 'trial') {
            $subscriptionData = [
                'subscription_status' => 'trial',
                'trial_plan' => 'professional',
                'trial_ends_at' => now()->addDays(15),
                'is_trial_active' => true,
                'properties_limit' => 1,
            ];
        } elseif ($request->subscription_choice === 'starter') {
            $subscriptionData = [
                'subscription_status' => 'starter',
                'trial_plan' => null,
                'trial_ends_at' => null,
                'is_trial_active' => false,
                'properties_limit' => 1,
                'subscription_ends_at' => now()->addYear(),
            ];
        } elseif ($request->subscription_choice === 'professional') {
            $subscriptionData = [
                'subscription_status' => 'professional',
                'trial_plan' => null,
                'trial_ends_at' => null,
                'is_trial_active' => false,
                'properties_limit' => 5,
                'subscription_ends_at' => now()->addYear(),
            ];
        }

        $user = User::create(array_merge([
            'name' => $request->name,
            'mobile_number' => $request->mobile_number,
            'pin_hash' => Hash::make($request->pin),
            'email' => $request->email,
            'user_type' => 'owner',
            'is_staff' => false,
        ], $subscriptionData));


        Auth::login($user);

        return redirect()->route('onboarding.wizard');
    }
}