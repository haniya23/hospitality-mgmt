@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('header')
    <x-page-header 
        title="Choose Your Plan" 
        subtitle="Select the perfect plan for your business" 
        icon="credit-card">
        
        <div class="text-center mb-6">
            <div class="inline-flex items-center bg-white bg-opacity-20 rounded-full px-4 py-2">
                <i class="fas fa-gift text-yellow-300 mr-2"></i>
                <span class="text-white font-medium">{{ auth()->user()->remaining_trial_days }} Days Trial Remaining</span>
            </div>
        </div>
    </x-page-header>
@endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8" x-data="{ yearly: false }">
    <!-- Billing Toggle -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center bg-gray-100 rounded-full p-1">
            <button @click="yearly = false" :class="!yearly ? 'bg-white shadow-sm' : ''" class="px-4 py-2 rounded-full text-sm font-medium transition-all">Monthly</button>
            <button @click="yearly = true" :class="yearly ? 'bg-white shadow-sm' : ''" class="px-4 py-2 rounded-full text-sm font-medium transition-all">Yearly <span class="text-green-600 text-xs ml-1">(2 months free)</span></button>
        </div>
    </div>
    
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Starter Plan -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border-2 border-gray-200 hover:border-blue-400 transition-all">
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Starter</h3>
                <div class="flex items-center justify-center space-x-2 mb-2">
                    <span class="text-2xl text-gray-400 line-through" x-text="yearly ? '₹14,990' : '₹1,499'"></span>
                    <div class="text-4xl font-bold text-blue-600">
                        <span x-text="yearly ? '₹3,990' : '₹399'"></span>
                        <span class="text-lg text-gray-500" x-text="yearly ? '/year' : '/month'"></span>
                    </div>
                </div>
                <div class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-sm font-medium inline-block mb-2">
                    <span x-text="yearly ? 'Save ₹798 annually (2 months free)' : 'Limited Time Offer - 73% OFF'"></span>
                </div>
                <p class="text-gray-600">Perfect for single property</p>
            </div>
            
            <ul class="space-y-3 mb-8">
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-3"></i>
                    <span>1 property only</span>
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
                    <span>1 image per accommodation</span>
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
            
            <form action="{{ route('subscription.subscribe') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="starter">
                <input type="hidden" name="billing" x-bind:value="yearly ? 'yearly' : 'monthly'">
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-all">
                    Choose Starter Plan
                </button>
            </form>
        </div>

        <!-- Professional Plan -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-purple-400 relative transform scale-105">
            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                <span class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-1 rounded-full text-sm font-medium">
                    Most Popular
                </span>
            </div>
            
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Professional</h3>
                <div class="flex items-center justify-center space-x-2 mb-2">
                    <span class="text-2xl text-gray-400 line-through" x-text="yearly ? '₹29,990' : '₹2,999'"></span>
                    <div class="text-4xl font-bold text-purple-600">
                        <span x-text="yearly ? '₹6,990' : '₹699'"></span>
                        <span class="text-lg text-gray-500" x-text="yearly ? '/year' : '/month'"></span>
                    </div>
                </div>
                <div class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-sm font-medium inline-block mb-2">
                    <span x-text="yearly ? 'Save ₹1,398 annually (2 months free)' : 'Special Offer - 77% OFF'"></span>
                </div>
                <p class="text-gray-600">Complete hospitality solution</p>
            </div>
            
            <ul class="space-y-3 mb-8">
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-3"></i>
                    <span>Up to 5 properties</span>
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
                    <span>5 images per accommodation</span>
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
            
            <form action="{{ route('subscription.subscribe') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="professional">
                <input type="hidden" name="billing" x-bind:value="yearly ? 'yearly' : 'monthly'">
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all">
                    Choose Professional Plan
                </button>
            </form>
        </div>
    </div>

    <div class="text-center mt-12">
        <p class="text-gray-600 mb-4">All plans include a 30-day free trial. No credit card required.</p>
        <div class="flex justify-center space-x-8 text-sm text-gray-500">
            <span><i class="fas fa-shield-alt mr-2"></i>Secure payments</span>
            <span><i class="fas fa-sync-alt mr-2"></i>Cancel anytime</span>
            <span><i class="fas fa-headset mr-2"></i>24/7 support</span>
        </div>
    </div>
</div>
@endsection