@extends('layouts.app')

@section('title', 'Create Property - Admin')
@section('page-title', 'Create Property for User')

@section('content')

    <div class="space-y-6">
        <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Create Property for User</h3>
                <p class="text-sm text-gray-600 mt-1">Admin-created properties are automatically approved</p>
            </div>
            
            <form method="POST" action="{{ route('admin.store-property') }}" class="px-6 py-4 space-y-4">
                @csrf
                
                <div>
                    <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-2">Select User</label>
                    <select id="owner_id" name="owner_id" required
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 select2-dropdown @error('owner_id') border-red-500 @enderror">
                        <option value="">Choose a user...</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('owner_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->mobile_number }}) - {{ ucfirst($user->subscription_status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('owner_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Property Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Enter property name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Property Category</label>
                    <select id="category_id" name="category_id" required
                            class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 select2-dropdown @error('category_id') border-red-500 @enderror">
                        <option value="">Choose a category...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Enter property description">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-medium text-blue-900">Admin Property Creation</h4>
                            <p class="text-sm text-blue-700 mt-1">
                                Properties created by admin are automatically approved and active. 
                                The selected user will be able to manage this property immediately.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3 pt-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                        Create Property
                    </button>
                    <a href="{{ route('admin.property-approvals') }}" class="flex-1 bg-gray-500 text-white py-3 rounded-xl font-medium hover:bg-gray-600 transition-all duration-200 text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Auto-update user info when selected
        document.getElementById('owner_id').addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                console.log('Selected user:', selectedOption.text);
            }
        });
    </script>
@endsection