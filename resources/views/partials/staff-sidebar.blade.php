<!-- Desktop Sidebar -->
<div class="hidden lg:flex lg:w-72 lg:flex-col lg:fixed lg:inset-y-0 lg:z-50">
    <div class="flex flex-col flex-grow bg-white border-r border-gray-200 shadow-lg">
        <!-- Logo -->
        <div class="flex items-center flex-shrink-0 px-6 py-4 border-b border-gray-100">
            <div class="flex items-center">
                <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-users text-white text-lg"></i>
                </div>
                <div class="ml-3">
                    <h1 class="text-lg font-bold text-gray-900">Staff Portal</h1>
                    <p class="text-xs text-gray-500 font-medium">{{ Auth::user()->name }}</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex-grow flex flex-col overflow-y-auto">
            <nav class="flex-1 px-4 py-6 space-y-6">
                <!-- Dashboard -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Dashboard</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('staff.dashboard') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.dashboard') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                                <i class="fas fa-tachometer-alt w-5 text-center"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.properties') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.properties*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                                <i class="fas fa-building w-5 text-center"></i>
                                <span>My Properties</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Bookings -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Bookings</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('staff.bookings') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.bookings') && !request()->routeIs('staff.bookings.create') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                                <i class="fas fa-calendar-alt w-5 text-center"></i>
                                <span>Upcoming Bookings</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.bookings.create') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.bookings.create') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
                               @if(!Auth::user()->hasPermission('create_bookings'))
                                   onclick="showPermissionMessage('create_bookings'); return false;"
                               @endif>
                                <i class="fas fa-plus w-5 text-center"></i>
                                <span>Add Booking</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Work -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Work</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('staff.tasks') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.tasks*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                                <i class="fas fa-tasks w-5 text-center"></i>
                                <span>Tasks</span>
                                @if(Auth::user()->getTodaysTasks()->count() > 0)
                                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 font-semibold">{{ Auth::user()->getTodaysTasks()->count() }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.checklists') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.checklists*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                                <i class="fas fa-clipboard-check w-5 text-center"></i>
                                <span>Checklists</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.attendance') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.attendance*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                                <i class="fas fa-clock w-5 text-center"></i>
                                <span>Attendance</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Guest Services -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Guest Services</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('staff.guest-service.index') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.guest-service*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                                <i class="fas fa-concierge-bell w-5 text-center"></i>
                                <span>Guest Service</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Communication -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Communication</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('staff.notifications') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.notifications') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                                <i class="fas fa-bell w-5 text-center"></i>
                                <span>Notifications</span>
                                @if(Auth::user()->getUnreadNotificationsCount() > 0)
                                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 font-semibold">{{ Auth::user()->getUnreadNotificationsCount() }}</span>
                                @endif
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('staff.activity') }}" 
                               class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.activity') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                                <i class="fas fa-history w-5 text-center"></i>
                                <span>Activity Log</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <!-- User Menu -->
        <div class="flex-shrink-0 border-t border-gray-200 bg-gray-50">
            <div class="px-6 py-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-gradient-to-r from-gray-400 to-gray-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 font-medium">Staff Member</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Sidebar -->
<div x-show="sidebarOpen" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full"
     class="fixed inset-y-0 left-0 z-50 lg:hidden mobile-sidebar"
     x-cloak>
    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white shadow-2xl">
        <div class="absolute top-0 right-0 -mr-12 pt-2">
            <button @click="sidebarOpen = false" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-gray-500 transition-colors">
                <i class="fas fa-times text-white"></i>
            </button>
        </div>
        
        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
            <div class="flex-shrink-0 flex items-center px-6 py-4 border-b border-gray-100">
                <div class="h-10 w-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                    <i class="fas fa-users text-white text-lg"></i>
                </div>
                <div class="ml-3">
                    <h1 class="text-lg font-bold text-gray-900">Staff Portal</h1>
                    <p class="text-xs text-gray-500 font-medium">{{ Auth::user()->name }}</p>
                </div>
            </div>
            
            <nav class="px-4 py-6 space-y-6" x-data="{ openSections: { bookings: true, work: false, guest: false, communication: false } }">
                <!-- Dashboard -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Dashboard</h3>
                    <div class="space-y-1">
                        <a href="{{ route('staff.dashboard') }}" 
                           @click="sidebarOpen = false"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.dashboard') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                            <i class="fas fa-tachometer-alt w-5 text-center"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('staff.properties') }}" 
                           @click="sidebarOpen = false"
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.properties*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                            <i class="fas fa-building w-5 text-center"></i>
                            <span>My Properties</span>
                        </a>
                    </div>
                </div>

                <!-- Bookings Section -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Bookings</h3>
                    <div class="space-y-1">
                        <a href="{{ route('staff.bookings') }}" 
                           @click="sidebarOpen = false" 
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.bookings') && !request()->routeIs('staff.bookings.create') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                            <i class="fas fa-calendar-alt w-5 text-center"></i>
                            <span>Upcoming Bookings</span>
                        </a>
                        <a href="{{ route('staff.bookings.create') }}" 
                           @click="sidebarOpen = false" 
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.bookings.create') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}"
                           @if(!Auth::user()->hasPermission('create_bookings'))
                               onclick="showPermissionMessage('create_bookings'); return false;"
                           @endif>
                            <i class="fas fa-plus w-5 text-center"></i>
                            <span>Add Booking</span>
                        </a>
                    </div>
                </div>

                <!-- Work Section -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Work</h3>
                    <div class="space-y-1">
                        <a href="{{ route('staff.tasks') }}" 
                           @click="sidebarOpen = false" 
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.tasks*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                            <i class="fas fa-tasks w-5 text-center"></i>
                            <span>Tasks</span>
                        </a>
                        <a href="{{ route('staff.checklists') }}" 
                           @click="sidebarOpen = false" 
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.checklists*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                            <i class="fas fa-clipboard-check w-5 text-center"></i>
                            <span>Checklists</span>
                        </a>
                        <a href="{{ route('staff.attendance') }}" 
                           @click="sidebarOpen = false" 
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.attendance*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                            <i class="fas fa-clock w-5 text-center"></i>
                            <span>Attendance</span>
                        </a>
                    </div>
                </div>

                <!-- Guest Services Section -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Guest Services</h3>
                    <div class="space-y-1">
                        <a href="{{ route('staff.guest-service.index') }}" 
                           @click="sidebarOpen = false" 
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.guest-service*') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                            <i class="fas fa-concierge-bell w-5 text-center"></i>
                            <span>Guest Service</span>
                        </a>
                    </div>
                </div>

                <!-- Communication Section -->
                <div>
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3 px-3">Communication</h3>
                    <div class="space-y-1">
                        <a href="{{ route('staff.notifications') }}" 
                           @click="sidebarOpen = false" 
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.notifications') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                            <i class="fas fa-bell w-5 text-center"></i>
                            <span>Notifications</span>
                        </a>
                        <a href="{{ route('staff.activity') }}" 
                           @click="sidebarOpen = false" 
                           class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 {{ request()->routeIs('staff.activity') ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-md' : 'text-gray-700 hover:bg-blue-50 hover:text-blue-700' }}">
                            <i class="fas fa-history w-5 text-center"></i>
                            <span>Activity Log</span>
                        </a>
                    </div>
                </div>
            </nav>
        </div>
        
        <!-- Mobile User Menu -->
        <div class="flex-shrink-0 border-t border-gray-200 bg-gray-50">
            <div class="px-6 py-4">
                <div class="flex items-center">
                    <div class="h-8 w-8 bg-gradient-to-r from-gray-400 to-gray-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 font-medium">Staff Member</p>
                    </div>
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