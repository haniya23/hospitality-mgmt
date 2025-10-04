@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('header')
    <x-page-header 
        title="Choose Your Plan" 
        subtitle="Select the perfect plan for your business" 
        icon="credit-card">
        
    </x-page-header>
@endsection

@section('content')
<script>
function subscriptionPage() {
    return {
        // Upgrade Modal
        showUpgradeModal: false,
        additionalAccommodations: 0,
        selectedPlan: null,
        
        // Payment
        loading: false,
        
        // Success Animation
        showSuccessAnimation: false,
        
        // Billing
        yearly: false,
        
        init() {
            // Check if we just returned from a successful payment
            const urlParams = new URLSearchParams(window.location.search);
            // Check URL params for payment status
            
            if (urlParams.get('payment') === 'success') {
                // Showing success animation
                this.showSuccessAnimation = true;
                // Clean up URL
                window.history.replaceState({}, document.title, window.location.pathname);
                
                // Force page refresh after a short delay to ensure updated subscription status
                setTimeout(() => {
                    // Auto-refreshing page to show updated accommodation data
                    window.location.reload();
                }, 2000); // Reduced to 2 seconds for faster feedback
            }
        },
        
        // Upgrade Modal Methods
        openUpgradeModal() {
            // Opening upgrade modal
            this.showUpgradeModal = true;
            this.additionalAccommodations = 1; // Set default to 1 accommodation
        },
        
        closeUpgradeModal() {
            this.showUpgradeModal = false;
            this.additionalAccommodations = 0;
            this.selectedPlan = null;
        },
        
        selectPlan(plan) {
            this.selectedPlan = plan;
            // Handle plan selection logic here
            // Selected plan and additional accommodations
        },
        
        // Payment Methods
        async subscribeToPlan(plan) {
            this.loading = true;
            
            try {
                const response = await fetch('/api/subscription/create-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        plan: plan,
                        billing: this.yearly ? 'yearly' : 'monthly',
                        quantity: 1
                    })
                });
                
                const data = await response.json();
                
                if (response.ok && data.payment_url) {
                    // Redirect to Cashfree payment page
                    window.location.href = data.payment_url;
                } else {
                    this.showError(data.message || 'Failed to create payment order');
                }
            } catch (error) {
                // Subscription error
                this.showError('Payment service temporarily unavailable. Please try again later.');
            } finally {
                this.loading = false;
            }
        },
        
        showError(message) {
            showError('Error', message);
        },

        // Add accommodations for Professional users
        async addAccommodations() {
            if (this.additionalAccommodations <= 0) {
                this.showError('Please select at least 1 accommodation');
                return;
            }

            this.loading = true;
            
            try {
                const response = await fetch('/api/subscription/addons', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        quantity: this.additionalAccommodations
                    })
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Redirect to payment page
                    window.location.href = data.payment_url;
                } else {
                    this.showError(data.message || 'Failed to create payment order');
                }
            } catch (error) {
                // Add accommodations error
                this.showError('Service temporarily unavailable. Please try again later.');
            } finally {
                this.loading = false;
            }
        },

        showSuccess(message) {
            showSuccess('Success', message);
        },
        
        // Success Animation Methods
        hideSuccessAnimation() {
            this.showSuccessAnimation = false;
            // Force reload page to show updated subscription status and accommodation data
            window.location.href = window.location.pathname;
        },
        
        getSuccessMessage() {
            // Check if this was an accommodation add-on payment
            const urlParams = new URLSearchParams(window.location.search);
            const successMessage = '{{ session("success") }}';
            
            if (successMessage.includes('additional accommodations')) {
                return 'Your additional accommodations have been added successfully! They will be active for 30 days.';
            } else {
                return 'Welcome to your new plan. Your subscription has been activated.';
            }
        }
    }
}
</script>

<div class="max-w-6xl mx-auto px-4 py-8" x-data="subscriptionPage()">
    
    <!-- Success Message -->
    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        <i class="fas fa-check-circle mr-2"></i>
        {{ session('success') }}
    </div>
    @endif
    
    <!-- Success Animation -->
    <div x-show="showSuccessAnimation" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50"
         style="z-index: 99999 !important;"
         x-cloak
         style="display: none;">
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center shadow-2xl">
            <div class="mb-6">
                <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-check text-4xl text-green-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h3>
                <p class="text-gray-600 mb-4" x-text="getSuccessMessage()">Welcome to your new plan. Your subscription has been activated.</p>
            </div>
            <button @click="hideSuccessAnimation()" 
                    class="w-full bg-green-600 text-white py-3 rounded-xl font-semibold hover:bg-green-700 transition-all">
                Continue
            </button>
        </div>
    </div>

    @if(auth()->user()->subscription_status && auth()->user()->subscription_status !== 'trial')
        <!-- Current Subscription Status -->
        <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-2xl p-6 mb-8 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold mb-2">Current Plan: {{ ucfirst(auth()->user()->subscription_status) }}
                        @if(auth()->user()->billing_cycle)
                            ({{ ucfirst(auth()->user()->billing_cycle) }})
                        @endif
                    </h3>
                    <p class="opacity-90">You have an active subscription</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">
                        @if(auth()->user()->subscription_ends_at)
                            {{ auth()->user()->remaining_subscription_days }}
                        @else
                            0
                        @endif
                    </div>
                    <div class="text-sm opacity-90">Days Remaining</div>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <div class="font-semibold">Properties</div>
                    <div>{{ auth()->user()->getUsagePercentage()['properties']['used'] }} / {{ auth()->user()->getUsagePercentage()['properties']['max'] }}</div>
                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1 mt-1">
                        <div class="bg-white h-1 rounded-full" style="width: {{ auth()->user()->getUsagePercentage()['properties']['percentage'] }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="font-semibold">Accommodations</div>
                    <div>{{ auth()->user()->getUsagePercentage()['accommodations']['used'] }} / {{ auth()->user()->getUsagePercentage()['accommodations']['max'] }}</div>
                    <div class="w-full bg-white bg-opacity-20 rounded-full h-1 mt-1">
                        <div class="bg-white h-1 rounded-full" style="width: {{ auth()->user()->getUsagePercentage()['accommodations']['percentage'] }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="font-semibold">Expires On</div>
                    <div>{{ auth()->user()->subscription_ends_at ? auth()->user()->subscription_ends_at->format('M d, Y') : 'N/A' }}</div>
                </div>
                <div>
                    <div class="font-semibold">Status</div>
                    <div class="flex items-center">
                        <span class="w-2 h-2 bg-green-300 rounded-full mr-2"></span>
                        Active
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Details -->
        @if(auth()->user()->activeSubscription)
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 mb-8 text-white">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-receipt text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Subscription Details</h3>
                        <p class="opacity-90">Current plan and billing information</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">
                        ₹{{ number_format(auth()->user()->activeSubscription->total_subscription_amount, 0) }}
                    </div>
                    <div class="text-sm opacity-90">Total Amount</div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <div class="font-semibold">Base Plan</div>
                    <div class="text-lg font-bold">₹{{ number_format(auth()->user()->activeSubscription->price, 0) }}</div>
                    <div class="text-xs opacity-75">{{ ucfirst(auth()->user()->activeSubscription->plan_slug) }}</div>
                </div>
                <div>
                    <div class="font-semibold">Add-ons</div>
                    <div class="text-lg font-bold">₹{{ number_format(auth()->user()->activeSubscription->total_addon_amount, 0) }}</div>
                    <div class="text-xs opacity-75">{{ auth()->user()->activeSubscription->addons()->where('cycle_end', '>', now())->sum('qty') }} accommodations</div>
                </div>
                <div>
                    <div class="font-semibold">Billing Cycle</div>
                    <div class="text-lg font-bold">{{ ucfirst(auth()->user()->activeSubscription->billing_interval) }}</div>
                    <div class="text-xs opacity-75">Next: {{ auth()->user()->activeSubscription->current_period_end->format('M d, Y') }}</div>
                </div>
                <div>
                    <div class="font-semibold">Status</div>
                    <div class="text-lg font-bold flex items-center">
                        <span class="w-2 h-2 bg-green-300 rounded-full mr-2"></span>
                        {{ ucfirst(auth()->user()->activeSubscription->status) }}
                    </div>
                    <div class="text-xs opacity-75">{{ auth()->user()->activeSubscription->days_remaining }} days left</div>
                </div>
            </div>
        </div>
        @endif

        <!-- Added Accommodations Summary -->
        @if(auth()->user()->activeSubscription && auth()->user()->activeSubscription->addons()->where('cycle_end', '>', now())->count() > 0)
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl p-6 mb-8 text-white"
             @if(session('success') && str_contains(session('success'), 'additional accommodations'))
             x-data="{ isNewlyAdded: true }"
             x-init="setTimeout(() => isNewlyAdded = false, 5000)"
             :class="{ 'ring-4 ring-green-400': isNewlyAdded }"
             @endif>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-plus-circle text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Added Accommodations</h3>
                        <p class="opacity-90">Additional accommodations with 30-day expiry</p>
                        @if(session('success') && str_contains(session('success'), 'additional accommodations'))
                        <div class="mt-1">
                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full animate-pulse">
                                ✨ Just Added!
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">
                        {{ auth()->user()->activeSubscription->addons()->where('cycle_end', '>', now())->sum('qty') }}
                    </div>
                    <div class="text-sm opacity-90">Active Add-ons</div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                @php
                    $activeAddons = auth()->user()->activeSubscription->addons()->where('cycle_end', '>', now())->get();
                    $totalAddons = $activeAddons->sum('qty');
                    $expiringSoon = $activeAddons->filter(function($addon) {
                        return now()->diffInDays($addon->cycle_end, false) <= 7;
                    })->sum('qty');
                    $earliestExpiry = $activeAddons->min('cycle_end');
                    $totalAddonAmount = $activeAddons->sum(function($addon) {
                        return $addon->qty * $addon->unit_price;
                    });
                @endphp
                
                <div>
                    <div class="font-semibold">Total Added</div>
                    <div class="text-lg font-bold">{{ $totalAddons }} accommodations</div>
                    <div class="text-xs opacity-75">₹{{ number_format($totalAddonAmount, 0) }} total</div>
                </div>
                <div>
                    <div class="font-semibold">Expiring Soon</div>
                    <div class="text-lg font-bold {{ $expiringSoon > 0 ? 'text-yellow-300' : 'text-green-300' }}">
                        {{ $expiringSoon }} accommodations
                    </div>
                    @if($expiringSoon > 0)
                    <div class="text-xs opacity-75">Within 7 days</div>
                    @endif
                </div>
                <div>
                    <div class="font-semibold">Earliest Expiry</div>
                    <div class="text-lg font-bold">
                        @if($earliestExpiry)
                            {{ $earliestExpiry->format('M d') }}
                        @else
                            N/A
                        @endif
                    </div>
                    @if($earliestExpiry)
                    <div class="text-xs opacity-75">
                        {{ now()->diffInDays($earliestExpiry, false) }} days left
                    </div>
                    @endif
                </div>
                <div>
                    <div class="font-semibold">Monthly Cost</div>
                    <div class="text-lg font-bold">₹{{ number_format($totalAddonAmount, 0) }}</div>
                    <div class="text-xs opacity-75">₹99 per accommodation</div>
                </div>
            </div>
        </div>
        @endif
    @endif
    
    @if(auth()->user()->subscription_status === 'starter')
    <!-- Upgrade to Professional Card -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-2xl shadow-lg p-8 text-white">
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold mb-2">Upgrade to Professional</h3>
                <p class="opacity-90">Unlock more properties and advanced features</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="font-semibold mb-3">What you'll get:</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            Up to 5 properties (vs 1 current)
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            15 accommodations (vs 3 current)
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            B2B partner management
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            Advanced analytics & reports
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            Priority support
                        </li>
                    </ul>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl font-bold mb-2">
                        <span x-text="yearly ? '₹9,990' : '₹999'"></span>
                        <span class="text-lg opacity-75" x-text="yearly ? '/year' : '/month'"></span>
                    </div>
                    <div class="text-sm opacity-75 mb-4" x-text="yearly ? 'Special offer from ₹119,988' : 'Special offer from ₹9,999'"></div>
                    <button @click="subscribeToPlan('professional')" 
                            :disabled="loading"
                            class="bg-white text-purple-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!loading">Upgrade Now</span>
                        <span x-show="loading" class="flex items-center justify-center">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Processing...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(auth()->user()->activeSubscription && auth()->user()->activeSubscription->plan_slug === 'professional')
    <!-- Add Accommodations Section for Professional Users -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-green-600 to-emerald-600 rounded-2xl shadow-lg p-8 text-white">
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold mb-2">Add More Accommodations</h3>
                <p class="opacity-90">Need more accommodation slots? Add them instantly!</p>
            </div>
            
            <div class="grid md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h4 class="font-semibold mb-3">Current Status:</h4>
                    <ul class="space-y-2 text-sm">
                        <li class="flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            {{ auth()->user()->getUsagePercentage()['accommodations']['used'] }} / {{ auth()->user()->getUsagePercentage()['accommodations']['max'] }} accommodations used
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Add up to 50 more accommodations
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-2"></i>
                            Instant activation
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-credit-card mr-2"></i>
                            Billed monthly with your subscription
                        </li>
                    </ul>
                </div>
                
                <div class="text-center">
                    <div class="text-3xl font-bold mb-2">₹99<span class="text-lg opacity-75">/month each</span></div>
                    <div class="text-sm opacity-75 mb-4">Add as many as you need</div>
                    <button @click="openUpgradeModal()" 
                            class="bg-white text-green-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-all">
                        <i class="fas fa-plus mr-2"></i>Add Accommodations
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <!-- Billing Toggle - Show for all users who can see plans -->
    @if(auth()->user()->subscription_status === 'trial' || auth()->user()->subscription_status === 'starter')
    <div class="text-center mb-8">
        <div class="inline-flex items-center bg-gray-100 rounded-full p-1">
            <button @click="yearly = false" :class="!yearly ? 'bg-white shadow-sm' : ''" class="px-4 py-2 rounded-full text-sm font-medium transition-all">Monthly</button>
            <button @click="yearly = true" :class="yearly ? 'bg-white shadow-sm' : ''" class="px-4 py-2 rounded-full text-sm font-medium transition-all">Yearly <span class="text-green-600 text-xs ml-1">(2 months free)</span></button>
        </div>
    </div>
    @endif
    
    @if(auth()->user()->subscription_status === 'trial')
    <!-- Starter Plan Section -->
    <div class="mb-12">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Starter Plan</h2>
            <p class="text-gray-600">Perfect for single property management</p>
        </div>
        
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-2xl shadow-lg p-8 border-2 border-gray-200 hover:border-blue-400 transition-all">
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Starter</h3>
                    <div class="flex items-center justify-center space-x-2 mb-2">
                        <span class="text-2xl text-gray-400 line-through" x-text="yearly ? '₹35,988' : '₹2,999'"></span>
                        <div class="text-4xl font-bold text-blue-600">
                            <span x-text="yearly ? '₹2,990' : '₹299'"></span>
                            <span class="text-lg text-gray-500" x-text="yearly ? '/year' : '/month'"></span>
                        </div>
                    </div>
                    <div class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-medium inline-block mb-2" x-show="!yearly">
                        Special Offer
                    </div>
                    <div class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-medium inline-block mb-2" x-show="yearly">
                        Save ₹598 (2 months free!)
                    </div>
                </div>
                
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>1 property only</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>3 accommodations</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Unlimited bookings</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Customer management</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Basic pricing management</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Image uploads available</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-times text-red-500 mr-3"></i>
                        <span class="text-gray-400">No B2B partner management</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-times text-red-500 mr-3"></i>
                        <span class="text-gray-400">Basic reports only</span>
                    </li>
                </ul>
                
                <button @click="subscribeToPlan('starter')" 
                        class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-all">
                    <span x-show="!loading">Choose Starter Plan</span>
                    <span x-show="loading" class="flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Processing...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    @if(auth()->user()->subscription_status === 'trial' || auth()->user()->subscription_status === 'starter')
    <!-- Professional Plan Section -->
    <div class="mb-12">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Professional Plan</h2>
            <p class="text-gray-600">Complete hospitality solution for growing businesses</p>
        </div>
        
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-purple-400 relative">
                <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                    <span class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-1 rounded-full text-sm font-medium">
                        Most Popular
                    </span>
                </div>
                
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Professional</h3>
                    <div class="flex items-center justify-center space-x-2 mb-2">
                        <span class="text-2xl text-gray-400 line-through" x-text="yearly ? '₹119,988' : '₹9,999'"></span>
                        <div class="text-4xl font-bold text-purple-600">
                            <span x-text="yearly ? '₹9,990' : '₹999'"></span>
                            <span class="text-lg text-gray-500" x-text="yearly ? '/year' : '/month'"></span>
                        </div>
                    </div>
                    <div class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-medium inline-block mb-2" x-show="!yearly">
                        Special Offer
                    </div>
                    <div class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-medium inline-block mb-2" x-show="yearly">
                        Save ₹1,998 (2 months free!)
                    </div>
                </div>
                
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Up to 5 properties</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>15 accommodations</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Unlimited bookings</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Advanced customer management</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Dynamic pricing & calendar</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Unlimited image uploads</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>B2B partner management</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Advanced reports & analytics</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Priority support</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-star text-yellow-500 mr-3"></i>
                        <span class="text-purple-600 font-medium">New features early access</span>
                    </li>
                </ul>
                
                <button @click="subscribeToPlan('professional')" 
                        class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all">
                    <span x-show="!loading">Choose Professional Plan</span>
                    <span x-show="loading" class="flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Processing...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif
    
    @if(auth()->user()->activeSubscription && (auth()->user()->activeSubscription->plan_slug === 'starter' || auth()->user()->activeSubscription->plan_slug === 'professional'))
    <!-- Extra Accommodations Section -->
    <div class="mb-12">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Need More Accommodations?</h2>
            <p class="text-gray-600">Add extra accommodations to your plan</p>
        </div>
        
        <div class="max-w-md mx-auto">
            <div class="bg-white rounded-2xl shadow-lg p-8 border-2 border-green-200 hover:border-green-400 transition-all">
                <div class="text-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Additional Accommodations</h3>
                    <div class="text-4xl font-bold text-green-600 mb-2">
                        <span>₹99</span>
                        <span class="text-lg text-gray-500">/month each</span>
                    </div>
                    <p class="text-gray-600">Add as many as you need</p>
                </div>
                
                <ul class="space-y-3 mb-8">
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Additional accommodation slots</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Same features as your plan</span>
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check text-green-500 mr-3"></i>
                        <span>Cancel anytime</span>
                    </li>
                </ul>
                
                <button @click="openUpgradeModal()" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-6 rounded-lg transition-all">
                    <i class="fas fa-plus mr-2"></i>Add Accommodations
                </button>
            </div>
        </div>
    </div>
    @endif

    @if(auth()->user()->subscription_status === 'trial')
    <div class="text-center mt-12">
        <p class="text-gray-600 mb-4">All plans include a 15-day free trial. No credit card required.</p>
        <div class="flex justify-center space-x-8 text-sm text-gray-500">
            <span><i class="fas fa-shield-alt mr-2"></i>Secure payments</span>
            <span><i class="fas fa-sync-alt mr-2"></i>Cancel anytime</span>
            <span><i class="fas fa-headset mr-2"></i>24/7 support</span>
        </div>
    </div>
    @endif

    <!-- Add Accommodations Modal -->
<div x-show="showUpgradeModal" 
     x-transition:enter="ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100" 
     x-transition:leave="ease-in duration-200" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0" 
     class="fixed inset-0 overflow-y-auto"
     style="z-index: 99999 !important;"
     x-cloak
     style="background-color: rgba(0,0,0,0.5);">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" @click="closeUpgradeModal()">
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full" @click.stop>
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <i class="fas fa-plus text-green-600"></i>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Add More Accommodations</h3>
                    <p class="text-sm text-gray-500 mb-6">Add additional accommodations to your current plan</p>
                    
                    <!-- Current Plan Display -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h4 class="font-semibold text-gray-800 mb-2">Current Plan</h4>
                        <div class="text-center">
                            <div class="text-xl font-bold text-gray-900">
                                @if(auth()->user()->subscription_status === 'professional')
                                    Professional Plan
                                @elseif(auth()->user()->subscription_status === 'starter')
                                    Starter Plan
                                @else
                                    Trial Plan
                                @endif
                            </div>
                            <div class="text-sm text-gray-600 mt-1">
                                {{ auth()->user()->getUsagePercentage()['accommodations']['used'] }} / {{ auth()->user()->getUsagePercentage()['accommodations']['max'] }} accommodations used
                            </div>
                            @if(auth()->user()->activeSubscription)
                            <div class="mt-2 pt-2 border-t border-gray-200">
                                <div class="text-sm text-gray-600">
                                    <div class="flex justify-between">
                                        <span>Base Plan:</span>
                                        <span class="font-medium">₹{{ number_format(auth()->user()->activeSubscription->price, 0) }}</span>
                                    </div>
                                    @if(auth()->user()->activeSubscription->total_addon_amount > 0)
                                    <div class="flex justify-between">
                                        <span>Add-ons:</span>
                                        <span class="font-medium">₹{{ number_format(auth()->user()->activeSubscription->total_addon_amount, 0) }}</span>
                                    </div>
                                    <div class="flex justify-between font-semibold text-gray-800">
                                        <span>Total:</span>
                                        <span>₹{{ number_format(auth()->user()->activeSubscription->total_subscription_amount, 0) }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Added Accommodations Details -->
                    @if(auth()->user()->activeSubscription && auth()->user()->activeSubscription->addons()->where('cycle_end', '>', now())->count() > 0)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6" 
                         @if(session('success') && str_contains(session('success'), 'additional accommodations'))
                         x-data="{ isNewlyAdded: true }"
                         x-init="setTimeout(() => isNewlyAdded = false, 5000)"
                         :class="{ 'ring-2 ring-green-400 bg-green-50 border-green-200': isNewlyAdded }"
                         @endif>
                        <h4 class="font-semibold text-blue-800 mb-3 flex items-center">
                            <i class="fas fa-plus-circle mr-2"></i>
                            Added Accommodations
                            @if(session('success') && str_contains(session('success'), 'additional accommodations'))
                            <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full animate-pulse">
                                Just Added!
                            </span>
                            @endif
                        </h4>
                        <div class="space-y-3">
                            @foreach(auth()->user()->activeSubscription->addons()->where('cycle_end', '>', now())->orderBy('cycle_end', 'asc')->get() as $addon)
                                @php
                                    $daysRemaining = now()->diffInDays($addon->cycle_end, false);
                                    $daysRemaining = max(0, (int) $daysRemaining);
                                    $isExpiringSoon = $daysRemaining <= 7;
                                @endphp
                                <div class="bg-white rounded-lg p-3 border border-blue-100">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-bed text-blue-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $addon->qty }} Additional Accommodations</div>
                                                <div class="text-sm text-gray-600">Added on {{ $addon->cycle_start->format('M d, Y') }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-medium {{ $isExpiringSoon ? 'text-red-600' : 'text-gray-900' }}">
                                                @if($daysRemaining > 0)
                                                    {{ $daysRemaining }} days left
                                                @else
                                                    Expired
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                Expires {{ $addon->cycle_end->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    @if($isExpiringSoon && $daysRemaining > 0)
                                    <div class="mt-2 bg-red-50 border border-red-200 rounded-lg p-2">
                                        <div class="flex items-center text-red-700 text-sm">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            <span>Expires soon! Consider renewing to avoid service interruption.</span>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    
                    <!-- Accommodation Input -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Number of Accommodations</label>
                            <div class="flex items-center justify-center space-x-3">
                                <button @click="additionalAccommodations = Math.max(1, additionalAccommodations - 1)" 
                                        class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition">
                                    <i class="fas fa-minus text-gray-600"></i>
                                </button>
                                <input type="number" 
                                       x-model="additionalAccommodations" 
                                       min="1" 
                                       max="50" 
                                       class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-center font-semibold focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                <button @click="additionalAccommodations = Math.min(50, additionalAccommodations + 1)" 
                                        class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300 transition">
                                    <i class="fas fa-plus text-gray-600"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Price Display -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">
                                    ₹<span x-text="(additionalAccommodations || 0) * 99"></span>
                                    <span class="text-sm text-gray-500">/month</span>
                                </div>
                                <div class="text-sm text-gray-600 mt-1">
                                    ₹99 per accommodation
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse space-x-3">
                <button @click="addAccommodations()" 
                        :disabled="loading || !additionalAccommodations"
                        class="w-full sm:w-auto bg-green-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading">Add Accommodations</span>
                    <span x-show="loading" class="flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Processing...
                    </span>
                </button>
                <button @click="closeUpgradeModal()" 
                        class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

</div>

@endsection
