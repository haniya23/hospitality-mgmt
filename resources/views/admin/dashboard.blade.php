@extends('layouts.mobile')

@section('title', 'Admin Dashboard - Hospitality Manager')
@section('page-title', 'Admin Panel')

@section('content')

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        <!-- Admin Stats -->
        <div class="bg-gradient-to-r from-red-500 to-pink-600 rounded-2xl p-6 text-white">
            <h2 class="text-xl font-bold mb-2">Admin Dashboard</h2>
            <div class="grid grid-cols-2 gap-4 mt-4">
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $stats['pending_properties'] }}</div>
                    <div class="text-sm opacity-90">Pending Properties</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $stats['total_users'] }}</div>
                    <div class="text-sm opacity-90">Total Users</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $stats['total_customers'] }}</div>
                    <div class="text-sm opacity-90">Customers</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $stats['b2b_partners'] }}</div>
                    <div class="text-sm opacity-90">B2B Partners</div>
                </div>
            </div>
        </div>

        <!-- Admin Menu -->
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('admin.property-approvals') }}" class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Property Approvals</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $stats['pending_properties'] }} pending</p>
                </div>
            </a>

            <a href="{{ route('admin.subscriptions') }}" class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Subscriptions</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $stats['pending_subscriptions'] }} pending</p>
                </div>
            </a>

            <a href="{{ route('admin.user-management') }}" class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">User Management</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $stats['total_users'] }} users</p>
                </div>
            </a>

            <a href="{{ route('admin.customer-data') }}" class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                <div class="text-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Customer Data</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $stats['total_customers'] }} customers</p>
                </div>
            </a>

            <a href="{{ route('admin.b2b-management') }}" class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                <div class="text-center">
                    <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m8 0H8m8 0v2a2 2 0 01-2 2H10a2 2 0 01-2-2V8m8 0V6a2 2 0 00-2-2H10a2 2 0 00-2 2v2"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">B2B Management</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $stats['b2b_partners'] }} partners</p>
                </div>
            </a>

            <a href="{{ route('admin.location-analytics') }}" class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-4 shadow-lg hover:shadow-xl transition-all duration-200 transform hover:-translate-y-1">
                <div class="text-center">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900">Location Analytics</h3>
                    <p class="text-sm text-gray-600 mt-1">Property insights</p>
                </div>
            </a>
        </div>
    </div>
@endsection