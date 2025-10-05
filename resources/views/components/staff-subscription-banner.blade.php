@if(session('staff_owner_subscription_expired'))
<div class="bg-gradient-to-r from-orange-400 to-red-500 text-white p-4 rounded-lg shadow-lg mb-6 border-l-4 border-red-600">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <i class="fas fa-exclamation-triangle text-2xl"></i>
        </div>
        <div class="ml-4 flex-1">
            <h3 class="text-lg font-semibold mb-1">
                <i class="fas fa-info-circle mr-2"></i>
                Service Temporarily Unavailable
            </h3>
            <p class="text-sm opacity-90 mb-2">
                The subscription for your assigned properties has expired. Please contact your manager or property owner for further details.
            </p>
            @if(session('staff_expired_owners'))
            <div class="text-xs opacity-75">
                <strong>Affected Properties:</strong> 
                @foreach(session('staff_expired_owners') as $owner)
                    {{ $owner }}@if(!$loop->last), @endif
                @endforeach
            </div>
            @endif
        </div>
        <div class="flex-shrink-0 ml-4">
            <button onclick="this.parentElement.parentElement.parentElement.style.display='none'" 
                    class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    </div>
</div>
@endif
