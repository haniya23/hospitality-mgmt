@extends('layouts.app')

@section('title', 'Add B2B Partner')

@section('header')
    {{-- This div remains here to encapsulate the header/nav component logic (e.g., stats) --}}
    <div x-data="b2bCreateData()" x-init="init()">
        @include('partials.b2b.header')
    </div>
@endsection

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <!-- Page Header -->
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center gap-3 mb-3">
            <a href="{{ route('b2b.index') }}" 
               class="inline-flex items-center justify-center w-10 h-10 rounded-lg bg-white border-2 border-gray-200 hover:border-purple-300 hover:bg-purple-50 transition-all duration-200 group">
                <svg class="w-5 h-5 text-gray-600 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-purple-600 to-indigo-600 bg-clip-text text-transparent">Add New B2B Partner</h1>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Create a new business partnership and start collaborating</p>
            </div>
        </div>
    </div>

    <!-- Main Form Card -->
    <div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-xl border-2 border-gray-200 overflow-hidden">
        <!-- Progress Indicator (Simplified for 1-step form) -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4">
            <div class="flex items-center justify-between text-white">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="font-semibold">Partner Registration Form</span>
                </div>
                <span class="text-sm bg-white/20 px-3 py-1 rounded-full backdrop-blur-sm">Step 1 of 1</span>
            </div>
        </div>

        <form action="{{ route('b2b.store') }}" method="POST" class="p-6 sm:p-8" id="b2bCreateForm" x-data="formFocus()">
            @csrf
            
            <!-- Partner Information Section -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Partner Information</h3>
                        <p class="text-sm text-gray-500">Basic details about the partner</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <div>
                        <label for="partner_name" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Partner Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="partner_name" 
                               name="partner_name" 
                               value="{{ old('partner_name') }}" 
                               x-ref="firstInput"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 @error('partner_name') border-red-500 @enderror" 
                               placeholder="e.g., Global Travel Agency" 
                               required>
                        @error('partner_name')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="partner_type" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Partner Type <span class="text-red-500">*</span>
                        </label>
                        {{-- The 'select2-dropdown' class is redundant and removed, Select2 handles the styling via its custom classes --}}
                        <select id="partner_type" 
                                name="partner_type" 
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 @error('partner_type') border-red-500 @enderror" 
                                required>
                            <option value="">Select Type</option>
                            <option value="Travel Agent" {{ old('partner_type') == 'Travel Agent' ? 'selected' : '' }}>Travel Agent</option>
                            <option value="OTA" {{ old('partner_type') == 'OTA' ? 'selected' : '' }}>OTA (Online Travel Agency)</option>
                            <option value="Corporate" {{ old('partner_type') == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                            <option value="Hotel Chain" {{ old('partner_type') == 'Hotel Chain' ? 'selected' : '' }}>Hotel Chain</option>
                            <option value="Tour Operator" {{ old('partner_type') == 'Tour Operator' ? 'selected' : '' }}>Tour Operator</option>
                        </select>
                        @error('partner_type')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Contact Information</h3>
                        <p class="text-sm text-gray-500">How to reach the partner</p>
                    </div>
                </div>
                
                <div class="space-y-4 sm:space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <div>
                            <label for="contact_person" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Contact Person <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="contact_person" 
                                   name="contact_person" 
                                   value="{{ old('contact_person') }}" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 @error('contact_person') border-red-500 @enderror" 
                                   placeholder="e.g., John Smith" 
                                   required>
                            @error('contact_person')
                                <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="mobile_number" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                                Mobile Number <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" 
                                   id="mobile_number" 
                                   name="mobile_number" 
                                   value="{{ old('mobile_number') }}" 
                                   class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 @error('mobile_number') border-red-500 @enderror" 
                                   placeholder="e.g., +91 9876543210" 
                                   required>
                            @error('mobile_number')
                                <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            Email <span class="text-gray-400 text-xs">(Optional)</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}" 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 @error('email') border-red-500 @enderror" 
                               placeholder="e.g., contact@travelagency.com">
                        @error('email')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Commission Settings Section -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Commission Settings</h3>
                        <p class="text-sm text-gray-500">Define financial terms</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                    <div>
                        <label for="commission_rate" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Commission Rate <span class="text-purple-600 font-bold">(%)</span>
                        </label>
                        <input type="number" 
                               id="commission_rate" 
                               name="commission_rate" 
                               value="{{ old('commission_rate', 10) }}" 
                               min="0" 
                               max="100" 
                               step="0.01"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 @error('commission_rate') border-red-500 @enderror" 
                               placeholder="10.00">
                        @error('commission_rate')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="default_discount_pct" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center gap-2">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            Default Discount <span class="text-purple-600 font-bold">(%)</span>
                        </label>
                        <input type="number" 
                               id="default_discount_pct" 
                               name="default_discount_pct" 
                               value="{{ old('default_discount_pct', 5) }}" 
                               min="0" 
                               max="100" 
                               step="0.01"
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 bg-white hover:border-gray-300 @error('default_discount_pct') border-red-500 @enderror" 
                               placeholder="5.00">
                        @error('default_discount_pct')
                            <p class="text-red-500 text-sm mt-2 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-5 mb-8">
                <div class="flex gap-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-bold text-blue-900 mb-2">Automatic Features</h3>
                        <div class="space-y-2">
                            <div class="flex items-start gap-2 text-sm text-blue-800">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>A reserved customer will be automatically created for this partner</span>
                            </div>
                            <div class="flex items-start gap-2 text-sm text-blue-800">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>The partner will receive a default PIN (0000) for login</span>
                            </div>
                            <div class="flex items-start gap-2 text-sm text-blue-800">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Partner status will be set to "Pending" until activated</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end gap-3 sm:gap-4 pt-6 border-t-2 border-gray-200">
                <a href="{{ route('b2b.index') }}" 
                   class="w-full sm:w-auto px-6 py-3 border-2 border-gray-300 rounded-xl text-gray-700 font-semibold hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 text-center flex items-center justify-center gap-2 group">
                    <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </a>
                <button type="submit" 
                        id="submitBtn"
                        class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                    <span id="submitBtnText" class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create B2B Partner
                    </span>
                    <span id="submitBtnLoader" class="hidden flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
{{-- Move custom CSS to a style block for cleaner loading --}}
<style>
/* Custom styles for Select2 purple theme */
.select2-purple-theme .select2-results__option--highlighted {
    background-color: #9333ea !important;
    color: white !important;
}
.select2-purple-container .select2-selection {
    /* Select2's generated element needs the full height/padding/border styling */
    border-radius: 0.75rem !important;
    border-width: 2px !important;
    padding: 0.5rem 1rem !important; /* Added 1rem for left/right padding */
    min-height: 3rem !important;
    box-shadow: none !important;
    border-color: #e5e7eb !important; /* Default gray-200 */
}
/* Style for Select2 focus/ring */
.select2-purple-container .select2-selection--focus {
    border-color: #9333ea !important; /* purple-500 */
    box-shadow: 0 0 0 2px rgba(147, 51, 234, 0.2) !important;
}
.select2-container--open .select2-dropdown {
    border: 2px solid #9333ea !important;
    border-radius: 0.75rem !important;
    margin-top: 0.5rem !important;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
}
.select2-results__option {
    padding: 0.75rem 1rem !important;
}

/* Animation for form load sections */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#b2bCreateForm > div {
    animation: slideInUp 0.5s ease-out backwards;
}

/* Apply staggered animation delay to form sections */
#b2bCreateForm > div:nth-child(2) { animation-delay: 0.1s; } /* Partner Info */
#b2bCreateForm > div:nth-child(3) { animation-delay: 0.2s; } /* Contact Info */
#b2bCreateForm > div:nth-child(4) { animation-delay: 0.3s; } /* Commission */
#b2bCreateForm > div:nth-child(5) { animation-delay: 0.4s; } /* Info Box */
#b2bCreateForm > div:nth-child(6) { animation-delay: 0.5s; } /* Action Buttons */

/* Smooth focus animation for inputs/selects not handled by alpine/select2 */
input:focus, select:focus, textarea:focus {
    transition: all 0.3s ease;
}
</style>

<script>
// Auto-focus functionality - Focuses on the first input and adds a subtle highlight
function formFocus() {
    return {
        init() {
            // Focus on the first input after a short delay
            this.$nextTick(() => {
                setTimeout(() => {
                    if (this.$refs.firstInput) {
                        this.$refs.firstInput.focus();
                        // Add a subtle highlight animation (matching the focus ring)
                        this.$refs.firstInput.classList.add('ring-2', 'ring-purple-500', 'border-purple-500');
                        setTimeout(() => {
                            this.$refs.firstInput.classList.remove('ring-2', 'ring-purple-500', 'border-purple-500');
                        }, 1500);
                    }
                }, 300);
            });
        }
    }
}

function b2bCreateData() {
    return {
        // Dummy data for the stat cards to prevent Alpine.js errors (from partials.b2b.header)
        partners: [],
        
        get activePartners() { return 0; },
        get totalBookings() { return 0; },
        get totalPartners() { return 0; },
        
        init() {
            this.initializeSelect2();
            this.setupFormSubmission();
            this.setupFormValidation();
        },

        initializeSelect2() {
            // Initialize Select2 for partner type dropdown with custom styling
            $('#partner_type').select2({
                placeholder: 'Select Type',
                allowClear: false,
                width: '100%',
                minimumResultsForSearch: -1, // Disable search for small list
                dropdownCssClass: 'select2-purple-theme',
                containerCssClass: 'select2-purple-container'
            });

            // Add change event to trigger validation styling for Select2
            $('#partner_type').on('change', function() {
                const $select = $(this);
                const $container = $select.next('.select2-container').find('.select2-selection');
                if ($select.val()) {
                    $container.removeClass('border-red-500');
                    $container.addClass('border-green-300');
                    // Remove green border after a moment
                    setTimeout(() => {
                        $container.removeClass('border-green-300');
                    }, 2000);
                }
            });
            
            // Initial check for validation errors
            if ($('#partner_type').hasClass('border-red-500')) {
                $('#partner_type').next('.select2-container').find('.select2-selection').addClass('border-red-500');
            }
        },

        setupFormValidation() {
            const form = document.getElementById('b2bCreateForm');
            if (!form) return;

            // Add real-time validation for inputs
            const inputs = form.querySelectorAll('input[required], select[required]');
            inputs.forEach(input => {
                const isSelect2 = $(input).hasClass('select2-hidden-accessible');
                const targetElement = isSelect2 
                    ? $(input).next('.select2-container').find('.select2-selection')[0] 
                    : input;

                input.addEventListener('blur', function() {
                    if (this.value.trim() === '') {
                        targetElement.classList.add('border-red-500');
                        targetElement.classList.remove('border-green-300');
                    } else {
                        targetElement.classList.remove('border-red-500');
                        targetElement.classList.add('border-green-300');
                        
                        // Remove green border after a moment
                        setTimeout(() => {
                            targetElement.classList.remove('border-green-300');
                        }, 2000);
                    }
                });

                // Remove error styling on input
                input.addEventListener('input', function() {
                    if (targetElement.classList.contains('border-red-500')) {
                        targetElement.classList.remove('border-red-500');
                    }
                });
            });

            // Email validation
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.addEventListener('blur', function() {
                    // Check if email is filled AND invalid
                    if (this.value && !this.validity.valid) {
                        this.classList.add('border-red-500');
                    } else if (!this.value && this.classList.contains('border-red-500')) {
                        // Clear error if empty (since it's optional)
                         this.classList.remove('border-red-500');
                    } else if (this.value && this.validity.valid) {
                        this.classList.remove('border-red-500');
                         this.classList.add('border-green-300');
                         setTimeout(() => { this.classList.remove('border-green-300'); }, 2000);
                    }
                });
            }
        },

        setupFormSubmission() {
            const form = document.getElementById('b2bCreateForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            const submitBtnLoader = document.getElementById('submitBtnLoader');

            if (!form || !submitBtn) return;

            // Prevent double submission
            let isSubmitting = false;
            
            // Helper function to reset button state
            const resetBtn = () => {
                submitBtnText.classList.remove('hidden');
                submitBtnLoader.classList.add('hidden');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                submitBtn.classList.add('hover:scale-105');
                isSubmitting = false;
            };

            // Add click event listener to submit button
            submitBtn.addEventListener('click', (e) => {
                e.preventDefault();

                if (isSubmitting) return;

                // Validate all fields
                const isValid = form.checkValidity();

                if (!isValid) {
                    form.reportValidity();
                    
                    // Highlight invalid fields and scroll to the first one
                    const invalidFields = form.querySelectorAll(':invalid');
                    invalidFields.forEach(field => {
                        // Apply error class to the right element (handling Select2)
                        const isSelect2 = $(field).hasClass('select2-hidden-accessible');
                        const targetElement = isSelect2 
                            ? $(field).next('.select2-container').find('.select2-selection')[0] 
                            : field;

                        targetElement.classList.add('border-red-500');

                        // Scroll to the first invalid field
                        if (invalidFields[0] === field) {
                             field.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    });
                    
                    return;
                }

                // Submission state
                isSubmitting = true;
                submitBtnText.classList.add('hidden');
                submitBtnLoader.classList.remove('hidden');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                submitBtn.classList.remove('hover:scale-105'); // Remove scale for loading state

                // Submit the form programmatically
                setTimeout(() => {
                    try {
                        form.submit();
                        
                        // Set a timeout to reset button if submission hangs (e.g. server error without redirect)
                        setTimeout(() => {
                            if (window.location.href.includes('/b2b/create')) {
                                resetBtn();
                            }
                        }, 5000);
                        
                    } catch (error) {
                        resetBtn();
                        alert('Form submission failed. Please try again.');
                        // Form submission error
                    }
                }, 100);
            });

            // Handle form submit event as well (as a fallback)
            form.addEventListener('submit', (e) => {
                if (!isSubmitting) {
                    e.preventDefault();
                    submitBtn.click();
                }
            });
        }
    }
}
</script>
@endpush