@extends('layouts.mobile')

@section('title', 'Register - Hospitality Manager')
@section('page-title', 'Create Account')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900">Create your account</h2>
            <p class="mt-2 text-sm text-gray-600">Join Hospitality Manager today</p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('register') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Enter your full name" value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mobile_number" class="block text-sm font-medium text-gray-700">Mobile Number</label>
                    <input id="mobile_number" name="mobile_number" type="tel" required 
                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Enter your mobile number" value="{{ old('mobile_number') }}">
                    @error('mobile_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address (Optional)</label>
                    <input id="email" name="email" type="email" 
                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="Enter your email address" value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pin" class="block text-sm font-medium text-gray-700">4-Digit PIN</label>
                    <input id="pin" name="pin" type="password" required maxlength="4" pattern="[0-9]{4}"
                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-2xl tracking-widest"
                           placeholder="••••">
                    @error('pin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Choose a 4-digit PIN for secure access</p>
                </div>

                <div>
                    <label for="pin_confirmation" class="block text-sm font-medium text-gray-700">Confirm PIN</label>
                    <input id="pin_confirmation" name="pin_confirmation" type="password" required maxlength="4" pattern="[0-9]{4}"
                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-2xl tracking-widest"
                           placeholder="••••">
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                    Create Account
                </button>
            </div>

            <div class="text-center">
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-500">
                        Sign in here
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection