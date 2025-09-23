@extends('layouts.mobile')

@section('title', 'User Management - Admin')
@section('page-title', 'User Management')

@section('content')

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-900">User Management</h2>
                <p class="text-sm text-gray-600">{{ $users->total() }} total users</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                Create User
            </a>
        </div>

        <!-- Users List -->
        <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
            @if($users->isEmpty())
                <div class="px-6 py-8 text-center text-gray-500">
                    <p>No users found.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($users as $user)
                        <div class="px-6 py-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $user->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $user->mobile_number }}</p>
                                            @if($user->email)
                                                <p class="text-sm text-gray-600">{{ $user->email }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="mt-2 flex items-center space-x-4">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full
                                            @if($user->subscription_status === 'trial') bg-yellow-100 text-yellow-800
                                            @elseif($user->subscription_status === 'starter') bg-blue-100 text-blue-800
                                            @elseif($user->subscription_status === 'professional') bg-purple-100 text-purple-800
                                            @endif">
                                            {{ ucfirst($user->subscription_status) }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            Properties: {{ $user->properties_count }}/{{ $user->properties_limit }}
                                        </span>
                                        @if($user->subscription_ends_at)
                                            <span class="text-xs text-gray-500">
                                                Expires: {{ $user->subscription_ends_at->format('M d, Y') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-3 py-1 rounded-lg text-xs font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div class="flex justify-center">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection