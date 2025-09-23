@if(auth()->user()->subscription_status === 'trial' && auth()->user()->is_trial_active)
<div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 rounded-xl mb-6 shadow-lg cursor-pointer" onclick="window.location.href='{{ route('subscription.plans') }}'">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-gift text-white text-lg"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg">{{ auth()->user()->remaining_trial_days }} Days Trial Remaining</h3>
                <p class="text-sm opacity-90">
                    {{ auth()->user()->plan_name }} • {{ auth()->user()->remaining_properties }} properties left
                    • Click to unlock full access
                </p>
            </div>
        </div>
        <div class="text-right">
            <div class="bg-white text-purple-600 px-4 py-2 rounded-lg font-bold text-sm">
                Upgrade from ₹399/mo
            </div>
            <div class="text-xs mt-1 opacity-75">73% OFF Limited Time</div>
        </div>
    </div>
</div>
@elseif(auth()->user()->isTrialExpired())
<div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-4 rounded-xl mb-6 shadow-lg">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-white"></i>
            </div>
            <div>
                <h3 class="font-bold text-lg">Trial Expired</h3>
                <p class="text-sm opacity-90">Upgrade to continue using all features</p>
            </div>
        </div>
        <a href="{{ route('subscription.plans') }}" class="bg-white text-red-600 px-4 py-2 rounded-lg font-medium hover:bg-gray-100 transition-all">
            Subscribe Now
        </a>
    </div>
</div>
@elseif(auth()->user()->subscription_status === 'active')
<div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-3 rounded-xl mb-6 shadow-lg">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                <i class="fas fa-crown text-white text-sm"></i>
            </div>
            <div>
                <h3 class="font-semibold">{{ auth()->user()->plan_name }} Plan Active</h3>
            </div>
        </div>
        <a href="{{ route('subscription.plans') }}" class="text-white text-sm underline hover:no-underline">
            Manage Plan
        </a>
    </div>
</div>
@endif