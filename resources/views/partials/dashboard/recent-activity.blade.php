<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">Recent Activity</h3>
    </div>
    
    <div class="divide-y divide-gray-100">
        <template x-for="activity in recentActivity" :key="activity.id">
            <div class="p-4 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center shadow-sm"
                     :class="activity.type === 'booking' ? 'bg-blue-100 text-blue-600' : 
                            activity.type === 'checkin' ? 'bg-green-100 text-green-600' :
                            'bg-orange-100 text-orange-600'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="activity.type === 'booking'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="activity.type === 'checkin'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="activity.type === 'checkout'">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800" x-text="activity.message"></p>
                    <p class="text-xs text-gray-500" x-text="activity.time"></p>
                </div>
            </div>
        </template>
    </div>
</div>