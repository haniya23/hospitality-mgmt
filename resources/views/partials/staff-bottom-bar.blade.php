<!-- Staff Bottom Navigation Bar -->
@php
    $user = auth()->user();
@endphp

<div class="fixed bottom-0 left-0 right-0 z-20 lg:hidden" x-data="{ showMoreMenu: false }">
    <!-- Floating Navigation Container -->
    <div class="mx-4 mb-4 lg:mx-8 lg:mb-8">
        <div class="bg-white/95 backdrop-blur-lg rounded-3xl shadow-2xl border border-gray-200/50 px-2 py-3 lg:px-4 lg:py-4">
            <div class="flex items-center justify-around lg:justify-center lg:gap-6">
                <!-- Dashboard -->
                <div class="group relative">
                    <a href="{{ route('staff.dashboard') }}" class="flex flex-col items-center justify-center p-2 lg:p-4 rounded-2xl transition-all duration-200 {{ request()->routeIs('staff.dashboard') ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-lg' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-tachometer-alt text-lg lg:text-2xl mb-1"></i>
                        <span class="text-xs font-medium">Dashboard</span>
                    </a>
                </div>

                <!-- Bookings -->
                <div class="group relative">
                    <a href="{{ route('staff.bookings') }}" class="flex flex-col items-center justify-center p-2 lg:p-4 rounded-2xl transition-all duration-200 {{ request()->routeIs('staff.bookings*') ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-lg' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-calendar-alt text-lg lg:text-2xl mb-1"></i>
                        <span class="text-xs font-medium">Bookings</span>
                    </a>
                </div>

                <!-- Tasks -->
                <div class="group relative">
                    <a href="{{ route('staff.tasks') }}" class="flex flex-col items-center justify-center p-2 lg:p-4 rounded-2xl transition-all duration-200 {{ request()->routeIs('staff.tasks') ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-lg' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-tasks text-lg lg:text-2xl mb-1"></i>
                        <span class="text-xs font-medium">Tasks</span>
                    </a>
                </div>

                @if(Auth::user()->hasPermission('manage_guest_services') || Auth::user()->hasPermission('manage_checkin_checkout'))
                <!-- Guest Service -->
                <div class="group relative">
                    <a href="{{ route('staff.guest-service.index') }}" class="flex flex-col items-center justify-center p-2 lg:p-4 rounded-2xl transition-all duration-200 {{ request()->routeIs('staff.guest-service.*') ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-lg' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-concierge-bell text-lg lg:text-2xl mb-1"></i>
                        <span class="text-xs font-medium">Guest</span>
                    </a>
                </div>
                @endif

                <!-- More Menu -->
                <div class="group relative">
                    <button @click="showMoreMenu = !showMoreMenu" class="flex flex-col items-center justify-center p-2 lg:p-4 rounded-2xl transition-all duration-200 text-gray-600 hover:text-blue-600 hover:bg-blue-50">
                        <i class="fas fa-ellipsis-h text-lg lg:text-2xl mb-1"></i>
                        <span class="text-xs font-medium">More</span>
                    </button>

                    <!-- Drop-up Menu -->
                    <div x-show="showMoreMenu" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform translate-y-4"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform translate-y-4"
                         @click.away="showMoreMenu = false"
                         class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-64 sm:w-72 bg-white/95 backdrop-blur-lg rounded-2xl shadow-2xl border border-gray-200/50 py-2 z-50">
                        
                        <!-- Bookings Section -->
                        <div class="px-3 py-2">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Bookings</h4>
                            <div class="space-y-1">
                                <a href="{{ route('staff.bookings') }}" 
                                   @click="showMoreMenu = false" 
                                   class="flex items-center px-3 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-lg {{ request()->routeIs('staff.bookings') && !request()->routeIs('staff.bookings.create') ? 'bg-blue-50 text-blue-600' : '' }}">
                                    <i class="fas fa-calendar-alt w-4 mr-3 text-gray-400"></i>
                                    <span class="text-sm font-medium">Upcoming Bookings</span>
                                </a>
                                <a href="{{ route('staff.bookings.create') }}" 
                                   @click="showMoreMenu = false" 
                                   class="flex items-center px-3 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-lg {{ request()->routeIs('staff.bookings.create') ? 'bg-blue-50 text-blue-600' : '' }}"
                                   @if(!Auth::user()->hasPermission('create_bookings'))
                                       onclick="showPermissionMessage('create_bookings'); return false;"
                                   @endif>
                                    <i class="fas fa-plus w-4 mr-3 text-gray-400"></i>
                                    <span class="text-sm font-medium">Add Booking</span>
                                </a>
                            </div>
                        </div>

                        <!-- Checklists Section -->
                        <div class="px-3 py-2">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Work</h4>
                            <div class="space-y-1">
                                <a href="{{ route('staff.checklists') }}" 
                                   @click="showMoreMenu = false" 
                                   class="flex items-center px-3 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-lg {{ request()->routeIs('staff.checklists') ? 'bg-blue-50 text-blue-600' : '' }}">
                                    <i class="fas fa-clipboard-check w-4 mr-3 text-gray-400"></i>
                                    <span class="text-sm font-medium">Checklists</span>
                                </a>
                                <a href="{{ route('staff.attendance') }}" 
                                   @click="showMoreMenu = false" 
                                   class="flex items-center px-3 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-lg {{ request()->routeIs('staff.attendance') ? 'bg-blue-50 text-blue-600' : '' }}">
                                    <i class="fas fa-calendar-check w-4 mr-3 text-gray-400"></i>
                                    <span class="text-sm font-medium">Attendance</span>
                                </a>
                            </div>
                        </div>

                        <!-- Communication Section -->
                        <div class="px-3 py-2 border-t border-gray-100">
                            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Communication</h4>
                            <div class="space-y-1">
                                <a href="{{ route('staff.notifications') }}" 
                                   @click="showMoreMenu = false" 
                                   class="flex items-center px-3 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-lg {{ request()->routeIs('staff.notifications') ? 'bg-blue-50 text-blue-600' : '' }}">
                                    <i class="fas fa-bell w-4 mr-3 text-gray-400"></i>
                                    <span class="text-sm font-medium">Notifications</span>
                                    <span x-show="stats && stats.unreadNotifications > 0" 
                                          class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 font-medium" 
                                          x-text="stats.unreadNotifications"></span>
                                </a>
                                <a href="{{ route('staff.activity') }}" 
                                   @click="showMoreMenu = false" 
                                   class="flex items-center px-3 py-2.5 text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors rounded-lg {{ request()->routeIs('staff.activity') ? 'bg-blue-50 text-blue-600' : '' }}">
                                    <i class="fas fa-history w-4 mr-3 text-gray-400"></i>
                                    <span class="text-sm font-medium">Activity Log</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- My Properties -->
                <div class="group relative">
                    <a href="{{ route('staff.properties') }}" class="flex flex-col items-center justify-center p-2 lg:p-4 rounded-2xl transition-all duration-200 {{ request()->routeIs('staff.properties*') ? 'bg-gradient-to-r from-blue-500 to-purple-500 text-white shadow-lg' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-building text-lg lg:text-2xl mb-1"></i>
                        <span class="text-xs font-medium">Properties</span>
                        <span class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center" 
                              x-text="{{ $user->getAssignedProperties()->count() }}"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permission Message Modal -->
<div id="permission-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4">Access Denied</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="permission-message">
                    You don't have permission to perform this action. Please contact your manager for access.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <button onclick="closePermissionModal()" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showPermissionMessage(permission) {
    const messages = {
        'create_bookings': 'You don\'t have permission to create bookings. Please contact your manager to request this access.',
        'edit_bookings': 'You don\'t have permission to edit bookings. Please contact your manager to request this access.',
        'cancel_bookings': 'You don\'t have permission to cancel bookings. Please contact your manager to request this access.'
    };
    
    document.getElementById('permission-message').textContent = messages[permission] || 'You don\'t have permission to perform this action. Please contact your manager for access.';
    document.getElementById('permission-modal').classList.remove('hidden');
}

function closePermissionModal() {
    document.getElementById('permission-modal').classList.add('hidden');
}
</script>
