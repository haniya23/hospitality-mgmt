@extends('layouts.app')

@section('title', 'Welcome to Your Free Trial')

@section('header')
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white py-16">
        <div class="max-w-4xl mx-auto text-center px-4">
            <div class="mb-6">
                <i class="fas fa-gift text-6xl text-yellow-300"></i>
            </div>
            <h1 class="text-4xl font-bold mb-4">Welcome {{ auth()->user()->name }}!</h1>
            <p class="text-xl mb-6">Your 30-day free trial has started. Choose your plan to unlock features.</p>
            <div class="bg-white bg-opacity-20 rounded-full px-6 py-3 inline-block">
                <span class="text-2xl font-bold">{{ auth()->user()->remaining_trial_days }} Days Remaining</span>
            </div>
        </div>
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-4">Choose Your Trial Experience</h2>
        <p class="text-gray-600">Select a plan to unlock features during your trial period</p>
    </div>

    <div class="grid md:grid-cols-2 gap-8">
        <!-- Starter Trial -->
        <div class="bg-white rounded-2xl shadow-lg p-8 border-2 border-gray-200 hover:border-blue-400 transition-all">
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Start with Starter</h3>
                <div class="text-4xl font-bold text-blue-600 mb-2">FREE</div>
                <div class="text-sm text-gray-500 mb-4">30-day trial • Then ₹399/month</div>
                <p class="text-gray-600">Perfect for single property</p>
            </div>
            
            <ul class="space-y-3 mb-8">
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-3"></i>
                    <span>1 property</span>
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
                    <span>1 image per accommodation</span>
                </li>
            </ul>
            
            <form action="{{ route('subscription.subscribe') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="starter">
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-all">
                    Start Starter Trial
                </button>
            </form>
        </div>

        <!-- Professional Trial -->
        <div class="bg-white rounded-2xl shadow-xl p-8 border-2 border-purple-400 relative transform scale-105">
            <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                <span class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-1 rounded-full text-sm font-medium">
                    Recommended
                </span>
            </div>
            
            <div class="text-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Try Professional</h3>
                <div class="text-4xl font-bold text-purple-600 mb-2">FREE</div>
                <div class="text-sm text-gray-500 mb-4">30-day trial • Then ₹699/month</div>
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
                    <span>5 images per accommodation</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-check text-green-500 mr-3"></i>
                    <span>B2B partner management</span>
                </li>
                <li class="flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-3"></i>
                    <span class="text-purple-600 font-medium">New features early access</span>
                </li>
            </ul>
            
            <form action="{{ route('subscription.subscribe') }}" method="POST">
                @csrf
                <input type="hidden" name="plan" value="professional">
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white py-3 rounded-xl font-semibold hover:from-purple-700 hover:to-pink-700 transition-all">
                    Start Professional Trial
                </button>
            </form>
        </div>
    </div>

    <div class="text-center mt-8">
        <p class="text-gray-600 mb-4">You can switch between plans anytime during your trial</p>
        <a href="{{ route('dashboard') }}" class="text-purple-600 hover:text-purple-700 font-medium">
            Skip for now and explore dashboard →
        </a>
    </div>
</div>
@endsection