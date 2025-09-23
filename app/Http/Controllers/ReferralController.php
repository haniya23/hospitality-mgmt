<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\ReferralWithdrawal;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('referral.index', [
            'referralCode' => $user->referral_code,
            'referralLink' => url('/register?ref=' . $user->referral_code),
            'earnings' => $user->referral_earnings,
            'completedReferrals' => $user->completed_referrals_count,
            'canWithdraw' => $user->canWithdrawReferralEarnings(),
            'referrals' => $user->referrals()->with('referred')->latest()->get(),
            'withdrawals' => $user->referralWithdrawals()->latest()->get(),
        ]);
    }

    public function withdraw(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->canWithdrawReferralEarnings()) {
            return redirect()->back()->with('error', 'Minimum 4 successful referrals required to withdraw.');
        }

        if ($user->referral_earnings < 199) {
            return redirect()->back()->with('error', 'Minimum ₹199 required to withdraw.');
        }

        ReferralWithdrawal::create([
            'user_id' => $user->id,
            'amount' => $user->referral_earnings,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Withdrawal request submitted successfully.');
    }
}