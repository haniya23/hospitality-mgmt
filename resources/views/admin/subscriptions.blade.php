@extends('layouts.app')

@section('title', 'Subscription Management - Admin')
@section('page-title', 'Subscription Management')

@section('content')

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        <!-- Pending Subscription Requests -->
        <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Pending Subscription Requests</h3>
            </div>
            
            @if($subscriptionRequests->where('status', 'pending')->isEmpty())
                <div class="px-6 py-8 text-center text-gray-500">
                    <p>No pending subscription requests.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($subscriptionRequests->where('status', 'pending') as $request)
                        <div class="px-6 py-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $request->user->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $request->user->mobile_number }}</p>
                                        <p class="text-sm text-gray-600">Requested: {{ ucfirst($request->requested_plan) }} ({{ $request->billing_cycle }})</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                        Pending
                                    </span>
                                </div>
                                
                                <p class="text-xs text-gray-500">Requested: {{ $request->created_at->format('M d, Y H:i') }}</p>
                                
                                <form method="POST" action="{{ route('admin.subscriptions.approve', $request) }}" class="flex space-x-3">
                                    @csrf
                                    @method('PATCH')
                                    <select name="months" class="flex-1 rounded-lg border-gray-300 text-sm">
                                        <option value="1">1 Month</option>
                                        <option value="3">3 Months</option>
                                        <option value="6">6 Months</option>
                                        <option value="12" {{ $request->billing_cycle === 'yearly' ? 'selected' : '' }}>12 Months</option>
                                    </select>
                                    <button type="submit" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg">
                                        Approve
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Active Subscriptions -->
        <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Active Subscriptions</h3>
            </div>
            
            @if($activeSubscriptions->isEmpty())
                <div class="px-6 py-8 text-center text-gray-500">
                    <p>No active subscriptions.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($activeSubscriptions as $user)
                        <div class="px-6 py-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $user->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $user->mobile_number }}</p>
                                        <p class="text-sm text-gray-600">Plan: {{ ucfirst($user->subscription_status) }}</p>
                                        <p class="text-sm text-gray-600">Properties: {{ $user->properties_count }}/{{ $user->properties_limit }}</p>
                                        @if($user->subscription_ends_at)
                                            <p class="text-xs text-gray-500">Expires: {{ $user->subscription_ends_at->format('M d, Y') }}</p>
                                        @endif
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                                        Active
                                    </span>
                                </div>
                                
                                <!-- Quick Update Form -->
                                <form method="POST" action="{{ route('admin.users.subscription.update', $user) }}" class="grid grid-cols-3 gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="subscription_status" class="rounded-lg border-gray-300 text-sm">
                                        <option value="trial" {{ $user->subscription_status === 'trial' ? 'selected' : '' }}>Trial</option>
                                        <option value="starter" {{ $user->subscription_status === 'starter' ? 'selected' : '' }}>Starter</option>
                                        <option value="professional" {{ $user->subscription_status === 'professional' ? 'selected' : '' }}>Professional</option>
                                    </select>
                                    <input type="number" name="properties_limit" value="{{ $user->properties_limit }}" min="1" max="10" class="rounded-lg border-gray-300 text-sm" placeholder="Limit">
                                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-3 py-2 rounded-xl text-xs font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200">
                                        Update
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($subscriptionRequests->hasPages() || $activeSubscriptions->hasPages())
            <div class="flex justify-center space-x-4">
                @if($subscriptionRequests->hasPages())
                    {{ $subscriptionRequests->links() }}
                @endif
                @if($activeSubscriptions->hasPages())
                    {{ $activeSubscriptions->links() }}
                @endif
            </div>
        @endif
    </div>
@endsection