@extends('layouts.mobile')

@section('title', 'Create User - Admin')
@section('page-title', 'Create User')

@section('content')

    <div class="space-y-6">
        <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Create New User</h3>
            </div>
            
            <form method="POST" action="{{ route('admin.users.store') }}" class="px-6 py-4 space-y-4">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                    <input type="text" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" required
                           class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('mobile_number') border-red-500 @enderror">
                    @error('mobile_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">PIN (4-6 digits)</label>
                    <input type="password" id="pin" name="pin" required
                           class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('pin') border-red-500 @enderror">
                    @error('pin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subscription_status" class="block text-sm font-medium text-gray-700 mb-2">Subscription Plan</label>
                    <select id="subscription_status" name="subscription_status" required
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('subscription_status') border-red-500 @enderror">
                        <option value="trial" {{ old('subscription_status') === 'trial' ? 'selected' : '' }}>Trial</option>
                        <option value="starter" {{ old('subscription_status') === 'starter' ? 'selected' : '' }}>Starter</option>
                        <option value="professional" {{ old('subscription_status') === 'professional' ? 'selected' : '' }}>Professional</option>
                    </select>
                    @error('subscription_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="properties_limit" class="block text-sm font-medium text-gray-700 mb-2">Properties Limit</label>
                    <input type="number" id="properties_limit" name="properties_limit" value="{{ old('properties_limit', 1) }}" min="1" max="10" required
                           class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('properties_limit') border-red-500 @enderror">
                    @error('properties_limit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                        Create User
                    </button>
                    <a href="{{ route('admin.user-management') }}" class="flex-1 bg-gray-500 text-white py-3 rounded-xl font-medium hover:bg-gray-600 transition-all duration-200 text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection