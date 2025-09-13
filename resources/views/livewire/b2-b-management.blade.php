@section('page-title', 'B2B Partners')

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
            <h2 class="text-2xl font-bold text-gray-900">B2B Partner Management</h2>
            <p class="text-gray-600">Manage your business partners</p>
        </div>
        <button wire:click="openCreateModal" 
                class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
            + New B2B Partner
        </button>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <input type="text" wire:model.live="search" placeholder="Search partners..." 
               class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
    </div>

    <!-- Partners List -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">B2B Partners ({{ $partners->total() }})</h3>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($partners as $partner)
                <div class="p-6 hover:bg-gray-50">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <h4 class="font-medium text-gray-900">{{ $partner->partner_name }}</h4>
                                <span class="px-2 py-1 text-xs rounded-full {{ $partner->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($partner->status) }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 space-y-1">
                                <div>📱 {{ $partner->phone }}</div>
                                @if($partner->email)
                                    <div>✉️ {{ $partner->email }}</div>
                                @endif
                                <div class="text-xs text-gray-500">
                                    Added {{ $partner->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">
                                {{ $partner->reservations_count ?? 0 }} bookings
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No B2B Partners Found</h3>
                    <p class="text-gray-600 mb-4">Start by adding your first business partner.</p>
                    <button wire:click="openCreateModal" 
                            class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
                        Add First Partner
                    </button>
                </div>
            @endforelse
        </div>

        @if($partners->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $partners->links() }}
            </div>
        @endif
    </div>

    <!-- Create Partner Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black/50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Add New B2B Partner</h3>
                    
                    <form wire:submit.prevent="createPartner" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Partner Name</label>
                            <input type="text" wire:model="partner_name" 
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('partner_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                            <input type="text" wire:model="contact_person" 
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('contact_person') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" wire:model="mobile_number" 
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('mobile_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                            <input type="email" wire:model="email" 
                                   class="w-full border border-gray-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="flex gap-3 mt-6">
                            <button type="button" wire:click="closeCreateModal" 
                                    class="flex-1 px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">
                                Create Partner
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>