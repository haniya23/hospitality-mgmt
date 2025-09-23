@extends('layouts.app')

@section('title', 'Referral Program')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-gradient-to-r from-purple-500 to-pink-500 rounded-2xl p-6 mb-8 text-white">
        <h2 class="text-2xl font-bold mb-2">Earn ₹199 per Referral!</h2>
        <p class="opacity-90">Refer friends and earn when they complete a 3+ month subscription</p>
    </div>

    <div class="grid md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-xl p-6 shadow-lg">
            <h3 class="text-lg font-semibold mb-4">Your Referral Link</h3>
            <div class="bg-gray-100 p-3 rounded-lg mb-4">
                <code class="text-sm">{{ $referralLink }}</code>
            </div>
            <button onclick="copyToClipboard('{{ $referralLink }}')" class="bg-blue-600 text-white px-4 py-2 rounded-lg">
                Copy Link
            </button>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-lg">
            <h3 class="text-lg font-semibold mb-4">Earnings Summary</h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span>Total Earnings:</span>
                    <span class="font-bold">₹{{ number_format($earnings) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Completed Referrals:</span>
                    <span class="font-bold">{{ $completedReferrals }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Withdrawal Status:</span>
                    <span class="font-bold {{ $canWithdraw ? 'text-green-600' : 'text-red-600' }}">
                        {{ $canWithdraw ? 'Available' : 'Need ' . (4 - $completedReferrals) . ' more' }}
                    </span>
                </div>
            </div>
            @if($canWithdraw && $earnings >= 199)
                <form action="{{ route('referral.withdraw') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg">
                        Withdraw ₹{{ number_format($earnings) }}
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Link copied to clipboard!');
    });
}
</script>
@endsection