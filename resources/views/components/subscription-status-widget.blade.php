@if(auth()->user()->subscription_status && auth()->user()->subscription_status !== 'trial')
<div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900">Subscription Status</h3>
        <a href="{{ route('subscription.plans') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            Manage
        </a>
    </div>
    
    <div class="space-y-4">
        <!-- Plan Info -->
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-green-400 to-green-600 flex items-center justify-center">
                    <i class="fas fa-crown text-white text-sm"></i>
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ ucfirst(auth()->user()->subscription_status) }} Plan</p>
                    <p class="text-sm text-gray-500">Active subscription</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-green-600">Active</p>
                <p class="text-xs text-gray-500">
                    @if(auth()->user()->subscription_ends_at)
                        Expires {{ auth()->user()->subscription_ends_at->format('M d, Y') }}
                    @else
                        No expiry
                    @endif
                </p>
            </div>
        </div>
        
        <!-- Usage Stats -->
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Properties</span>
                    <span class="text-sm font-semibold text-gray-900">
                        {{ auth()->user()->getUsagePercentage()['properties']['used'] }}/{{ auth()->user()->getUsagePercentage()['properties']['max'] }}
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ auth()->user()->getUsagePercentage()['properties']['percentage'] }}%"></div>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-600">Accommodations</span>
                    <span class="text-sm font-semibold text-gray-900">
                        {{ auth()->user()->getUsagePercentage()['accommodations']['used'] }}/{{ auth()->user()->getUsagePercentage()['accommodations']['max'] }}
                    </span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ auth()->user()->getUsagePercentage()['accommodations']['percentage'] }}%"></div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        @if(auth()->user()->subscription_status === 'professional')
        <div class="pt-2">
            <button onclick="showAddAccommodationsModal()" 
                    class="w-full bg-green-50 hover:bg-green-100 text-green-700 py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Add More Accommodations
            </button>
        </div>
        @endif
        
        @if(auth()->user()->subscription_status === 'starter')
        <div class="pt-2">
            <button onclick="showUpgradeModal()" 
                    class="w-full bg-purple-50 hover:bg-purple-100 text-purple-700 py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-arrow-up mr-2"></i>Upgrade to Professional
            </button>
        </div>
        @endif
    </div>
</div>
@endif
