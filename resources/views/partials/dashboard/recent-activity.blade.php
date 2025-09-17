<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">Recent Activity</h3>
    </div>
    
    <div class="divide-y divide-gray-100">
        <template x-for="activity in recentActivity" :key="activity.id">
            <div class="p-4 flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center"
                     :class="activity.type === 'booking' ? 'bg-blue-100 text-blue-600' : 
                            activity.type === 'checkin' ? 'bg-green-100 text-green-600' :
                            'bg-orange-100 text-orange-600'">
                    <i :class="activity.type === 'booking' ? 'fas fa-calendar-plus' : 
                              activity.type === 'checkin' ? 'fas fa-sign-in-alt' :
                              'fas fa-sign-out-alt'"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-800" x-text="activity.message"></p>
                    <p class="text-xs text-gray-500" x-text="activity.time"></p>
                </div>
            </div>
        </template>
    </div>
</div>