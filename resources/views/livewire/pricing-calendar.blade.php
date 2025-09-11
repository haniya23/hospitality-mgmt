<div class="space-y-6">
    <!-- Header Controls -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-xl font-bold text-gray-900">Pricing Calendar</h3>
            <p class="text-gray-600">Manage seasonal rates and promotional pricing</p>
        </div>
        
        <div class="flex items-center space-x-3">
            <select wire:model.live="property_id" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <option value="">Select Property</option>
                @foreach($properties as $property)
                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                @endforeach
            </select>
            
            <button wire:click="openRuleModal" 
                    class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-4 py-2 rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-200">
                + Add Rule
            </button>
        </div>
    </div>

    @if($property_id)
        <!-- Calendar -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Calendar Header -->
            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <button wire:click="previousMonth" class="p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    
                    <h2 class="text-xl font-bold text-white">{{ $monthName }}</h2>
                    
                    <button wire:click="nextMonth" class="p-2 text-white hover:bg-white hover:bg-opacity-20 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Calendar Grid -->
            <div class="p-4">
                <!-- Day Headers -->
                <div class="grid grid-cols-7 gap-1 mb-2">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $day)
                        <div class="p-2 text-center text-sm font-medium text-gray-500">{{ $day }}</div>
                    @endforeach
                </div>

                <!-- Calendar Days -->
                @foreach($calendarWeeks as $week)
                    <div class="grid grid-cols-7 gap-1 mb-1">
                        @foreach($week as $day)
                            <div class="min-h-[80px] p-1 border border-gray-100 rounded-lg relative cursor-pointer
                                {{ $day['isCurrentMonth'] ? 'bg-white hover:bg-gray-50' : 'bg-gray-50' }}
                                {{ $day['isToday'] ? 'ring-2 ring-purple-500' : '' }}"
                                wire:click="selectDate('{{ $day['date']->format('Y-m-d') }}')"
                            >
                                <!-- Date Number -->
                                <div class="text-sm font-medium mb-1
                                    {{ $day['isCurrentMonth'] ? 'text-gray-900' : 'text-gray-400' }}
                                    {{ $day['isToday'] ? 'text-purple-600' : '' }}">
                                    {{ $day['date']->format('j') }}
                                </div>

                                <!-- Pricing Rules -->
                                @if($day['ruleCount'] > 0)
                                    <div class="space-y-1">
                                        @foreach($day['rules']->take(2) as $rule)
                                            <div class="text-xs px-2 py-1 rounded-full
                                                @if($rule->rule_type === 'seasonal') bg-blue-100 text-blue-800
                                                @elseif($rule->rule_type === 'promotional') bg-red-100 text-red-800
                                                @elseif($rule->rule_type === 'b2b_contract') bg-green-100 text-green-800
                                                @else bg-purple-100 text-purple-800 @endif"
                                                title="{{ $rule->rule_name }}"
                                            >
                                                {{ Str::limit($rule->rule_name, 8) }}
                                                @if($rule->percentage_adjustment)
                                                    {{ $rule->percentage_adjustment > 0 ? '+' : '' }}{{ $rule->percentage_adjustment }}%
                                                @elseif($rule->rate_adjustment)
                                                    {{ $rule->rate_adjustment > 0 ? '+' : '' }}₹{{ $rule->rate_adjustment }}
                                                @endif
                                            </div>
                                        @endforeach
                                        
                                        @if($day['ruleCount'] > 2)
                                            <div class="text-xs text-gray-500 text-center">
                                                +{{ $day['ruleCount'] - 2 }} more
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Rule Type Indicators -->
                                <div class="absolute bottom-1 right-1 flex space-x-1">
                                    @if($day['hasPromo'])
                                        <div class="w-2 h-2 bg-red-500 rounded-full" title="Promotional"></div>
                                    @endif
                                    @if($day['hasSeasonal'])
                                        <div class="w-2 h-2 bg-blue-500 rounded-full" title="Seasonal"></div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <!-- Legend -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-wrap gap-4 text-xs">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-blue-100 border border-blue-300 rounded"></div>
                        <span class="text-gray-600">Seasonal</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-red-100 border border-red-300 rounded"></div>
                        <span class="text-gray-600">Promotional</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-100 border border-green-300 rounded"></div>
                        <span class="text-gray-600">B2B Contract</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-purple-100 border border-purple-300 rounded"></div>
                        <span class="text-gray-600">Loyalty</span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl p-8 text-center shadow-lg">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Select a Property</h3>
            <p class="text-gray-600">Choose a property to manage its pricing calendar</p>
        </div>
    @endif

    <!-- Pricing Rule Modal -->
    @if($showRuleModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" wire:click="closeRuleModal"></div>

                <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Add Pricing Rule</h3>
                        <button wire:click="closeRuleModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="saveRule" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rule Name</label>
                            <input type="text" wire:model="rule_name" placeholder="e.g., Summer Season 2024" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            @error('rule_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Rule Type</label>
                            <select wire:model="rule_type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                <option value="seasonal">Seasonal Rate</option>
                                <option value="promotional">Promotional Offer</option>
                                <option value="b2b_contract">B2B Contract Rate</option>
                                <option value="loyalty_discount">Loyalty Discount</option>
                            </select>
                            @error('rule_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                <input type="date" wire:model="start_date" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('start_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                <input type="date" wire:model="end_date" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('end_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fixed Amount (₹)</label>
                                <input type="number" wire:model="rate_adjustment" step="0.01" placeholder="e.g., 500" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('rate_adjustment') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Percentage (%)</label>
                                <input type="number" wire:model="percentage_adjustment" step="0.01" placeholder="e.g., 20" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @error('percentage_adjustment') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Stay (nights)</label>
                            <input type="number" wire:model="min_stay_nights" min="1" placeholder="Optional" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" wire:model="is_active" id="is_active" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">Active Rule</label>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                            <button type="button" wire:click="closeRuleModal" 
                                    class="px-6 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-6 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white rounded-xl hover:from-purple-600 hover:to-purple-700 transition-all duration-200">
                                Save Rule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>