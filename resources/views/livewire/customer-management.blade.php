@section('page-title', 'Customers')

<div class="space-y-6">
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Customer Management</h2>
            <p class="text-gray-600">Manage your customer database</p>
        </div>
        <button wire:click="openCreateModal" 
                class="bg-teal-600 text-white px-6 py-3 rounded-xl hover:bg-teal-700 transition-colors">
            + New Customer
        </button>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <input type="text" wire:model.live="search" placeholder="Search customers..." 
               class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
    </div>

    <!-- Customers List -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Customers ({{ $customers->total() }})</h3>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($customers as $customer)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-900">{{ $customer->name }}</h4>
                            <div class="text-sm text-gray-600 space-y-1 mt-1">
                                <div>📱 {{ $customer->mobile_number }}</div>
                                @if($customer->email)
                                    <div>✉️ {{ $customer->email }}</div>
                                @endif
                                <div class="text-xs text-gray-500">
                                    Joined {{ $customer->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">
                                {{ $customer->reservations_count ?? 0 }} bookings
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Customers Found</h3>
                    <p class="text-gray-600 mb-4">Start by adding your first customer.</p>
                    <button wire:click="openCreateModal" 
                            class="bg-teal-600 text-white px-6 py-3 rounded-xl hover:bg-teal-700 transition-colors">
                        Add First Customer
                    </button>
                </div>
            @endforelse
        </div>

        @if($customers->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

    <!-- Create Customer Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New Customer</h3>
                    
                    <form wire:submit.prevent="createCustomer" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                            <input type="text" wire:model="name" 
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                            <input type="tel" wire:model="mobile_number" 
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            @error('mobile_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                            <input type="email" wire:model="email" 
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="flex gap-3 mt-6">
                            <button type="button" wire:click="closeCreateModal" 
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 bg-teal-600 text-white rounded-xl hover:bg-teal-700">
                                Create Customer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>