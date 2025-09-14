<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Pricing Management</h1>
            <p class="text-gray-600 mt-1">Manage pricing rules and seasonal rates</p>
        </div>
        
        <div class="flex items-center gap-3">
            <button wire:click="openModal" 
                    class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-2 rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-200">
                + Add Rule
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4">
            <!-- Property Filter -->
            <div class="relative" x-data="{ open: false }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                <button @click="open = !open" type="button" class="w-full border border-gray-200 rounded-lg py-2 px-3 text-left text-sm bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-between">
                    <span class="truncate">
                        @if($property_id)
                            {{ $properties->firstWhere('id', $property_id)->name ?? 'All Properties' }}
                        @else
                            All Properties
                        @endif
                    </span>
                    <svg class="w-4 h-4 text-gray-400" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                    <button wire:click="$set('property_id', null)" @click="open = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm">
                        All Properties
                    </button>
                    @foreach($properties as $property)
                        <button wire:click="$set('property_id', {{ $property->id }})" @click="open = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm {{ $property_id == $property->id ? 'bg-blue-50 text-blue-700' : '' }}">
                            {{ $property->name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Accommodation Filter -->
            <div class="relative" x-data="{ open: false }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Accommodation</label>
                <button @click="open = !open" type="button" class="w-full border border-gray-200 rounded-lg py-2 px-3 text-left text-sm bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-between">
                    <span class="truncate">
                        @if($accommodation_id)
                            {{ $accommodations->firstWhere('id', $accommodation_id)->display_name ?? 'All Accommodations' }}
                        @else
                            All Accommodations
                        @endif
                    </span>
                    <svg class="w-4 h-4 text-gray-400" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                    <button wire:click="$set('accommodation_id', null)" @click="open = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm">
                        All Accommodations
                    </button>
                    @foreach($accommodations as $accommodation)
                        <button wire:click="$set('accommodation_id', {{ $accommodation->id }})" @click="open = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm {{ $accommodation_id == $accommodation->id ? 'bg-blue-50 text-blue-700' : '' }}">
                            {{ $accommodation->display_name }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Rule Type Filter -->
            <div class="relative" x-data="{ open: false }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <button @click="open = !open" type="button" class="w-full border border-gray-200 rounded-lg py-2 px-3 text-left text-sm bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-between">
                    <span class="truncate">
                        @switch($rule_type)
                            @case('seasonal') Seasonal @break
                            @case('promotional') Promotional @break
                            @case('b2b_contract') B2B Contract @break
                            @case('loyalty_discount') Loyalty @break
                            @default All Types
                        @endswitch
                    </span>
                    <svg class="w-4 h-4 text-gray-400" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg">
                    <button wire:click="$set('rule_type', '')" @click="open = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm">All Types</button>
                    <button wire:click="$set('rule_type', 'seasonal')" @click="open = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm">Seasonal</button>
                    <button wire:click="$set('rule_type', 'promotional')" @click="open = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm">Promotional</button>
                    <button wire:click="$set('rule_type', 'b2b_contract')" @click="open = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm">B2B Contract</button>
                    <button wire:click="$set('rule_type', 'loyalty_discount')" @click="open = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm">Loyalty</button>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="relative" x-data="{ open: false }">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <button @click="open = !open" type="button" class="w-full border border-gray-200 rounded-lg py-2 px-3 text-left text-sm bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 flex items-center justify-between">
                    <span class="truncate">
                        @if($is_active === '1') Active
                        @elseif($is_active === '0') Inactive
                        @else All Status
                        @endif
                    </span>
                    <svg class="w-4 h-4 text-gray-400" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false" x-transition class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg">
                    <button wire:click="$set('is_active', '')" @click="open = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm">All Status</button>
                    <button wire:click="$set('is_active', '1')" @click="open = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm">Active</button>
                    <button wire:click="$set('is_active', '0')" @click="open = false" type="button" class="w-full px-3 py-2 text-left hover:bg-gray-50 text-sm">Inactive</button>
                </div>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rule name..." 
                       class="w-full border border-gray-200 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Clear Filters -->
            <div class="flex items-end">
                <button wire:click="clearFilters" 
                        class="w-full bg-gray-100 text-gray-700 px-3 py-2 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    Clear Filters
                </button>
            </div>
        </div>
    </div>

    <!-- List View -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($pricingRules && $pricingRules->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rule</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property/Room</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adjustment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($pricingRules as $rule)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $rule->rule_name }}</div>
                                        @if($rule->promo_code)
                                            <div class="text-xs text-blue-600 font-mono">{{ $rule->promo_code }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>{{ $rule->property->name }}</div>
                                        @if($rule->accommodation)
                                            <div class="text-xs text-gray-500">{{ $rule->accommodation->display_name }}</div>
                                        @else
                                            <div class="text-xs text-gray-500">All accommodations</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @switch($rule->rule_type)
                                                @case('seasonal') bg-green-100 text-green-800 @break
                                                @case('promotional') bg-purple-100 text-purple-800 @break
                                                @case('b2b_contract') bg-blue-100 text-blue-800 @break
                                                @case('loyalty_discount') bg-yellow-100 text-yellow-800 @break
                                                @default bg-gray-100 text-gray-800
                                            @endswitch
                                        ">
                                            @switch($rule->rule_type)
                                                @case('seasonal') Seasonal @break
                                                @case('promotional') Promotional @break
                                                @case('b2b_contract') B2B Contract @break
                                                @case('loyalty_discount') Loyalty @break
                                                @default {{ ucfirst($rule->rule_type) }}
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>{{ $rule->start_date->format('M j') }} - {{ $rule->end_date->format('M j, Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $rule->start_date->diffInDays($rule->end_date) + 1 }} days</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($rule->rate_adjustment)
                                            <span class="font-medium {{ $rule->rate_adjustment > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $rule->rate_adjustment > 0 ? '+' : '' }}₹{{ number_format(abs($rule->rate_adjustment)) }}
                                            </span>
                                        @elseif($rule->percentage_adjustment)
                                            <span class="font-medium {{ $rule->percentage_adjustment > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $rule->percentage_adjustment > 0 ? '+' : '' }}{{ $rule->percentage_adjustment }}%
                                            </span>
                                        @else
                                            <span class="text-gray-400">No adjustment</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $rule->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button wire:click="editRule({{ $rule->id }})" 
                                                    class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </button>
                                            <button wire:click="deleteRule({{ $rule->id }})" 
                                                    wire:confirm="Are you sure you want to delete this pricing rule?"
                                                    class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $pricingRules->links() }}
                </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pricing rules</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new pricing rule.</p>
                <div class="mt-6">
                    <button wire:click="openModal" 
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-xl font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Create Pricing Rule
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Pricing Modal -->
    <livewire:pricing-modal />
</div>