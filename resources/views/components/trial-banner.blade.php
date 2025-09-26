@if(auth()->user()->subscription_status === 'trial' && auth()->user()->is_trial_active)
<div class="mb-6 mt-4 sm:mt-6 mx-4 sm:mx-6 lg:mx-8">
    <a href="{{ route('subscription.plans') }}" class="block w-full rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-4 sm:px-6 sm:py-5 lg:px-8 lg:py-6 shadow-lg hover:shadow-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:scale-[1.02]">
        <div class="flex items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                <div class="bg-white/20 rounded-full p-2 sm:p-2.5 flex-shrink-0">
                    <i class="fas fa-gift text-white text-sm sm:text-base"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-sm sm:text-base font-semibold leading-tight block">
                        <span class="text-blue-100">{{ auth()->user()->remaining_trial_days }} days left</span>
                    </span>
                    <span class="text-xs sm:text-sm text-blue-100 font-medium block mt-0.5">
                        Professional trial • Tap to upgrade
                    </span>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-2 flex-shrink-0">
                <span class="text-xs font-bold bg-white/25 hover:bg-white/35 px-3 py-2 rounded-full transition-colors backdrop-blur-sm border border-white/20">
                    Upgrade Now
                </span>
                <i class="fas fa-arrow-right text-white/70 text-sm"></i>
            </div>
        </div>
    </a>
</div>

@elseif(auth()->user()->isTrialExpired())
<div class="mb-6 mt-4 sm:mt-6 mx-4 sm:mx-6 lg:mx-8">
    <div class="bg-gradient-to-r from-red-600 to-red-700 text-white px-4 py-4 sm:px-6 sm:py-5 lg:px-8 lg:py-6 rounded-xl shadow-lg border-l-4 border-red-300">
        <div class="flex items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                <div class="bg-white/20 rounded-full p-2 sm:p-2.5 flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-white text-sm sm:text-base animate-pulse"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-sm sm:text-base font-bold block">
                        Trial Expired
                    </span>
                    <span class="text-xs sm:text-sm text-red-100 font-medium block mt-0.5">
                        Upgrade now to continue using all features
                    </span>
                </div>
            </div>
            <a href="{{ route('subscription.plans') }}" 
               class="bg-white text-red-600 px-4 py-2 sm:px-5 sm:py-2.5 rounded-lg font-bold hover:bg-red-50 transition-all text-xs sm:text-sm flex-shrink-0 shadow-md hover:shadow-lg transform hover:scale-105">
                <i class="fas fa-rocket mr-1"></i>Upgrade
            </a>
        </div>
    </div>
</div>

@elseif(auth()->user()->subscription_status === 'active')
<div class="mb-6 mt-4 sm:mt-6 mx-4 sm:mx-6 lg:mx-8">
    <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-4 py-4 sm:px-6 sm:py-5 lg:px-8 lg:py-6 rounded-xl shadow-lg border-l-4 border-green-300">
        <div class="flex items-center justify-between gap-3 sm:gap-4">
            <div class="flex items-center gap-3 sm:gap-4 flex-1 min-w-0">
                <div class="bg-white/20 rounded-full p-2 sm:p-2.5 flex-shrink-0">
                    <i class="fas fa-check-circle text-white text-sm sm:text-base"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-sm sm:text-base font-bold block">
                        {{ auth()->user()->plan_name }} Plan
                    </span>
                    <span class="text-xs sm:text-sm text-green-100 font-medium block mt-0.5">
                        Active subscription • Full access
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('subscription.plans') }}" 
                   class="text-white text-xs sm:text-sm font-medium underline hover:no-underline transition-all hover:text-green-100 flex items-center gap-1">
                    <i class="fas fa-cog text-xs"></i>
                    <span class="hidden sm:inline">Manage</span>
                </a>
                <div class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></div>
            </div>
        </div>
    </div>
</div>
@endif  