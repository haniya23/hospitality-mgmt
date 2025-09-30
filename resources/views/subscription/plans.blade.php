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
            if (urlParams.get('payment') === 'success') {
                this.showSuccessAnimation = true;
                // Clean up URL
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        },
        
        // Upgrade Modal Methods
        openUpgradeModal() {
            console.log('Opening upgrade modal...');
            this.showUpgradeModal = true;
            console.log('showUpgradeModal set to:', this.showUpgradeModal);
        },
        
        closeUpgradeModal() {
            this.showUpgradeModal = false;
            this.additionalAccommodations = 0;
            this.selectedPlan = null;
        },
        
        selectPlan(plan) {
            this.selectedPlan = plan;
            // Handle plan selection logic here
            console.log('Selected plan:', plan, 'Additional accommodations:', this.additionalAccommodations);
        },
        
        // Payment Methods
        async subscribeToPlan(plan) {
            this.loading = true;
            
            try {
                const response = await fetch('{{ route("cashfree.create-order") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        plan: plan,
                        billing: this.yearly ? 'yearly' : 'monthly',
                        additional_accommodations: 0
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Redirect to Cashfree payment page
                    window.location.href = data.payment_link;
                } else {
                    this.showError(data.message || 'Failed to create payment order');
                }
            } catch (error) {
                console.error('Subscription error:', error);
                this.showError('Payment service temporarily unavailable. Please try again later.');
            } finally {
                this.loading = false;
            }
        },
        
        showError(message) {
            // You can implement a toast notification here
            alert(message);
        },
        
        // Success Animation Methods
        hideSuccessAnimation() {
            this.showSuccessAnimation = false;
            // Reload page to show updated subscription status
            window.location.reload();
        }
    }
}
</script>

<div class="max-w-6xl mx-auto px-4 py-8" x-data="subscriptionPage()">
    
    <!-- Success Animation -->
    <div x-show="showSuccessAnimation" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0 scale-95" 
         x-transition:enter-end="opacity-100 scale-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100 scale-100" 
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
         x-cloak>
        <div class="bg-white rounded-2xl p-8 max-w-md mx-4 text-center shadow-2xl">
            <div class="mb-6">
                <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-check text-4xl text-green-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h3>
                <p class="text-gray-600 mb-4">Welcome to your new plan. Your subscription has been activated.</p>
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
                    <h3 class="text-xl font-bold mb-2">Current Plan: {{ ucfirst(auth()->user()->subscription_status) }}</h3>
                    <p class="opacity-90">You have an active subscription</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold">
                        @if(auth()->user()->subscription_ends_at)
                            @php
                                $daysRemaining = now()->diffInDays(auth()->user()->subscription_ends_at, false);
                                $daysRemaining = max(0, (int) $daysRemaining);
                            @endphp
                            {{ $daysRemaining }}
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
                    <div class="text-3xl font-bold mb-2">₹999<span class="text-lg opacity-75">/month</span></div>
                    <div class="text-sm opacity-75 mb-4">Special offer from ₹9,999</div>
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
    
    @if(auth()->user()->subscription_status === 'trial')
    <!-- Billing Toggle -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center bg-gray-100 rounded-full p-1">
            <button @click="yearly = false" :class="!yearly ? 'bg-white shadow-sm' : ''" class="px-4 py-2 rounded-full text-sm font-medium transition-all">Monthly</button>
            <button @click="yearly = true" :class="yearly ? 'bg-white shadow-sm' : ''" class="px-4 py-2 rounded-full text-sm font-medium transition-all">Yearly <span class="text-green-600 text-xs ml-1">(2 months free)</span></button>
        </div>
    </div>
    
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
                        <span class="text-2xl text-gray-400 line-through">₹2,999</span>
                        <div class="text-4xl font-bold text-blue-600">
                            <span>₹299</span>
                            <span class="text-lg text-gray-500">/month</span>
                        </div>
                    </div>
                    <div class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-medium inline-block mb-2">
                        Special Offer
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
                        <span class="text-2xl text-gray-400 line-through">₹9,999</span>
                        <div class="text-4xl font-bold text-purple-600">
                            <span>₹999</span>
                            <span class="text-lg text-gray-500">/month</span>
                        </div>
                    </div>
                    <div class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-medium inline-block mb-2">
                        Special Offer
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

    <!-- Upgrade Modal -->
<div x-show="showUpgradeModal" 
     x-transition:enter="ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100" 
     x-transition:leave="ease-in duration-200" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0" 
     class="fixed inset-0 z-50 overflow-y-auto"
     x-cloak
     style="background-color: rgba(0,0,0,0.5);">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" @click="closeUpgradeModal()">
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full" @click.stop>
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-arrow-up text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Upgrade Your Plan</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Choose a plan that fits your business needs</p>
                        </div>
                        
                        <!-- Current Usage -->
                        <div class="mt-4 bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Current Usage</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <div class="text-gray-600">Properties</div>
                                    <div class="font-semibold">{{ auth()->user()->getUsagePercentage()['properties']['used'] }} / {{ auth()->user()->getUsagePercentage()['properties']['max'] }}</div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ auth()->user()->getUsagePercentage()['properties']['percentage'] }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="text-gray-600">Accommodations</div>
                                    <div class="font-semibold">{{ auth()->user()->getUsagePercentage()['accommodations']['used'] }} / {{ auth()->user()->getUsagePercentage()['accommodations']['max'] }}</div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ auth()->user()->getUsagePercentage()['accommodations']['percentage'] }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Plan Options -->
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Starter Plan -->
                            <div class="border rounded-lg p-4 hover:border-blue-500 transition-colors">
                                <div class="text-center">
                                    <h4 class="font-semibold text-lg">Starter Plan</h4>
                                    <div class="text-2xl font-bold text-blue-600">₹299<span class="text-sm text-gray-500">/month</span></div>
                                    <div class="text-sm text-gray-500 line-through">₹2999</div>
                                    <div class="text-xs text-green-600 font-semibold">Special Offer</div>
                                </div>
                                <ul class="mt-3 text-sm space-y-1">
                                    <li>• 1 Property</li>
                                    <li>• 3 Accommodations</li>
                                    <li>• Basic Features</li>
                                    <li>• Email Support</li>
                                </ul>
                                <button @click="subscribeToPlan('starter')" class="w-full mt-3 bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                                    <span x-show="!loading">Choose Starter</span>
                                    <span x-show="loading" class="flex items-center justify-center">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>
                                        Processing...
                                    </span>
                                </button>
                            </div>
                            
                            <!-- Professional Plan -->
                            <div class="border rounded-lg p-4 hover:border-purple-500 transition-colors border-purple-200">
                                <div class="text-center">
                                    <h4 class="font-semibold text-lg">Professional Plan</h4>
                                    <div class="text-2xl font-bold text-purple-600">₹999<span class="text-sm text-gray-500">/month</span></div>
                                    <div class="text-sm text-gray-500 line-through">₹9999</div>
                                    <div class="text-xs text-green-600 font-semibold">Special Offer</div>
                                </div>
                                <ul class="mt-3 text-sm space-y-1">
                                    <li>• 5 Properties</li>
                                    <li>• 15 Accommodations</li>
                                    <li>• Advanced Features</li>
                                    <li>• Priority Support</li>
                                </ul>
                                <button @click="subscribeToPlan('professional')" class="w-full mt-3 bg-purple-600 text-white py-2 rounded-lg hover:bg-purple-700 transition">
                                    <span x-show="!loading">Choose Professional</span>
                                    <span x-show="loading" class="flex items-center justify-center">
                                        <i class="fas fa-spinner fa-spin mr-2"></i>
                                        Processing...
                                    </span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Additional Accommodations -->
                        <div class="mt-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <h4 class="font-semibold text-yellow-800 mb-2">Need More Accommodations?</h4>
                            <p class="text-sm text-yellow-700 mb-3">Add additional accommodations for ₹99 each per month</p>
                            <div class="flex items-center space-x-2">
                                <input type="number" x-model="additionalAccommodations" min="1" max="50" class="w-20 px-2 py-1 border rounded text-sm">
                                <span class="text-sm text-gray-600">accommodations</span>
                                <span class="text-sm font-semibold text-green-600">+₹<span x-text="(additionalAccommodations || 0) * 99"></span>/month</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button @click="closeUpgradeModal()" 
                        class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

</div>

@endsection
