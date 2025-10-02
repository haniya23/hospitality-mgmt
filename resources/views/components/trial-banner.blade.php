<!--
    Blade Component: Subscription Status Banner (Advanced Animations)

    This version uses custom Tailwind animations and advanced Alpine.js directives
    to create a premium, highly engaging user experience.
-->

@if(auth()->user()->subscription_status === 'trial' && auth()->user()->is_trial_active)
<div
    class="mb-6 mt-4 sm:mt-6 mx-4 sm:mx-6 lg:mx-8"
    x-data="trialBanner({{ auth()->user()->remaining_trial_days ?? 0 }})"
    x-init="init()"
    x-cloak
    x-show="visible"
    class="opacity-0"
    :style="`animation-delay: 100ms; animation-fill-mode: backwards;`"
    x-bind:class="{ 'animate-fadeInUp': visible }"
>
    <a href="{{ route('subscription.plans') }}" class="relative block w-full rounded-2xl bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 text-white p-4 sm:p-5 lg:p-6 overflow-hidden group">
        <!-- Subtle background pattern -->
        <div class="absolute inset-0 bg-[url('/img/patterns/noise.svg')] opacity-20"></div>

        <!-- Animated progress bar for remaining time -->
        <div class="absolute bottom-0 left-0 h-1 bg-cyan-300/80" :style="`width: ${progressPercentage}%`"></div>

        <!-- Warning Glow Effect (when <= 3 days) -->
        <div x-show="daysLeft <= 3" class="absolute inset-0 animate-pulseGlow rounded-2xl" style="box-shadow: 0 0 20px rgba(253, 224, 71, 0.5);"></div>

        <div class="relative flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 flex-1 min-w-0">
                <!-- Icon with hover animation -->
                <div class="bg-white/10 rounded-full p-2.5 flex-shrink-0 transition-transform duration-500 ease-out group-hover:scale-110 group-hover:rotate-6">
                    <i class="fas fa-gift text-white text-base sm:text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm sm:text-base font-bold leading-tight">
                        <!-- Days left with flip-style update -->
                        <span class="inline-block" x-ref="daysCount" x-text="daysLeft"></span>
                        <span>-Day Professional Trial</span>
                        <span x-show="daysLeft <= 3" class="text-yellow-300 ml-2">⚠️</span>
                    </p>
                    <p class="text-xs sm:text-sm text-blue-200 font-medium block mt-1 transition-colors group-hover:text-white">
                        <span x-show="daysLeft > 3">Unlock your full potential. Upgrade today!</span>
                        <span x-show="daysLeft <= 3" class="text-yellow-200 font-semibold">Your trial is ending very soon. Upgrade now!</span>
                    </p>
                </div>
            </div>
            <!-- Button with shimmer effect -->
            <div class="hidden sm:flex items-center gap-3 flex-shrink-0">
                <span class="relative overflow-hidden text-xs font-bold bg-white/10 px-4 py-2 rounded-full transition-all duration-300 backdrop-blur-sm border border-white/20 group-hover:bg-white/20 group-hover:border-white/30" :class="{'bg-yellow-400/20 text-yellow-100 border-yellow-300/30': daysLeft <= 3}">
                    <span class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-transparent via-white/30 to-transparent transform -translate-x-full transition-transform duration-1000 ease-in-out group-hover:translate-x-full"></span>
                    <span>Upgrade Now</span>
                </span>
                <i class="fas fa-arrow-right text-white/70 text-sm transition-transform duration-300 group-hover:translate-x-1"></i>
            </div>
        </div>
    </a>
</div>

<script>
function trialBanner(initialDays) {
    return {
        daysLeft: initialDays,
        progressPercentage: 100,
        visible: false,
        init() {
            this.progressPercentage = (this.daysLeft / 14) * 100; // Assuming a 14-day trial
            setTimeout(() => { this.visible = true; }, 50);

            // Animate the number change with GSAP if available, fallback to simple animation
            if (typeof gsap !== 'undefined') {
                let current = { value: initialDays };
                gsap.to(current, {
                    duration: 1.0,
                    value: this.daysLeft,
                    round: true,
                    onUpdate: () => {
                        this.$refs.daysCount.textContent = current.value;
                    },
                    ease: "power2.out"
                });
            } else {
                // Fallback: Simple counter animation without GSAP
                let current = initialDays;
                const target = this.daysLeft;
                const increment = target > current ? 1 : -1;
                const duration = 1000; // 1 second
                const steps = Math.abs(target - current);
                const stepDuration = steps > 0 ? duration / steps : 0;
                
                const animate = () => {
                    if (current !== target) {
                        current += increment;
                        this.$refs.daysCount.textContent = current;
                        setTimeout(animate, stepDuration);
                    }
                };
                
                if (steps > 0) {
                    animate();
                }
            }
        }
    }
}
</script>


@elseif(auth()->user()->isTrialExpired())
<div
    class="mb-6 mt-4 sm:mt-6 mx-4 sm:mx-6 lg:mx-8"
    x-data="{ visible: false }" x-init="setTimeout(() => { visible = true; }, 50)"
    x-cloak x-show="visible" class="opacity-0"
    :style="`animation-delay: 100ms; animation-fill-mode: backwards;`"
    x-bind:class="{ 'animate-fadeInUp': visible }"
>
    <div class="relative bg-gradient-to-r from-red-700 to-red-900 text-white p-4 sm:p-5 lg:p-6 rounded-2xl overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-transparent via-red-500/20 to-transparent opacity-50 transform -skew-y-12"></div>
        <div class="relative flex flex-wrap sm:flex-nowrap items-center justify-between gap-4">
            <div class="flex items-center gap-4 flex-1 min-w-0">
                <div class="bg-white/10 rounded-full p-2.5 flex-shrink-0 animate-pulse">
                    <i class="fas fa-times-circle text-white text-base sm:text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-base sm:text-lg font-bold">
                        Trial Expired
                    </h3>
                    <p class="text-xs sm:text-sm text-red-200 font-medium block mt-1">
                        Upgrade your plan to reactivate all features.
                    </p>
                </div>
            </div>
            <a href="{{ route('subscription.plans') }}" class="relative w-full sm:w-auto flex-shrink-0 bg-white text-red-700 px-5 py-2.5 rounded-lg font-bold hover:bg-red-50 transition-all text-sm transform hover:scale-105 flex items-center justify-center gap-2 overflow-hidden group">
                <span class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-transparent via-red-200 to-transparent transform -translate-x-full transition-transform duration-1000 ease-in-out group-hover:translate-x-full"></span>
                <i class="fas fa-rocket transition-transform duration-300 group-hover:rotate-[-10deg]"></i>
                <span class="relative">Upgrade to Pro</span>
            </a>
        </div>
    </div>
</div>

@elseif(in_array(auth()->user()->subscription_status, ['starter', 'professional']))
<div
    class="mb-6 mt-4 sm:mt-6 mx-4 sm:mx-6 lg:mx-8"
    x-data="{ visible: false }" x-init="setTimeout(() => { visible = true; }, 50)"
    x-cloak x-show="visible" class="opacity-0"
    :style="`animation-delay: 100ms; animation-fill-mode: backwards;`"
    x-bind:class="{ 'animate-fadeInUp': visible }"
>
    <div class="relative bg-gradient-to-r from-green-600 to-emerald-800 text-white p-4 sm:p-5 lg:p-6 rounded-2xl overflow-hidden group">
        <!-- On-load shimmer effect -->
        <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent transform -translate-x-full animate-shimmer" style="animation-duration: 4s;"></div>

        <div class="relative flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 flex-1 min-w-0">
                <div class="bg-white/20 rounded-full p-2.5 flex-shrink-0 transition-transform duration-500 group-hover:scale-110">
                    <i class="fas fa-shield-alt text-white text-base sm:text-lg"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm sm:text-base font-bold capitalize">
                        You're on the {{ auth()->user()->plan_name }} Plan
                    </p>
                    <p class="text-xs sm:text-sm text-green-200 font-medium block mt-1">
                        @if(auth()->user()->subscription_ends_at)
                            Access until {{ auth()->user()->subscription_ends_at->format('F j, Y') }}.
                        @else
                            Your subscription is active. Thank you!
                        @endif
                    </p>
                </div>
            </div>
            <a href="{{ route('subscription.plans') }}" class="hidden sm:flex items-center gap-2 flex-shrink-0 text-white text-xs font-medium py-2 px-3 rounded-full bg-white/10 hover:bg-white/20 transition-all duration-300 border border-transparent hover:border-white/30">
                <i class="fas fa-cog transition-transform duration-500 group-hover:rotate-90"></i>
                <span>Manage Subscription</span>
            </a>
        </div>
    </div>
</div>
@endif