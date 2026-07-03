@extends('layouts.app')

@section('title', 'Admin Dashboard - Stay loops')
@section('page-title', 'Admin Panel')

@section('content')

    @if(session('success'))
        <div class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-3 rounded-2xl mb-6 shadow-sm flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div x-data="{ activeTab: 'users' }" class="space-y-6">
        
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-slate-900 via-purple-950 to-indigo-950 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-purple-500 rounded-full opacity-10 blur-2xl"></div>
            <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-indigo-500 rounded-full opacity-10 blur-2xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">Admin Dashboard</h2>
                    <p class="text-indigo-200 text-sm mt-1">Manage system configurations, analytics, and pending actions.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.users.create') }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/10 px-4 py-2 rounded-2xl text-xs font-semibold backdrop-blur-sm transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-user-plus text-purple-300"></i> Create User
                    </a>
                    <a href="{{ route('admin.create-property') }}" class="bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white px-4 py-2 rounded-2xl text-xs font-semibold shadow-lg shadow-indigo-500/20 transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-plus"></i> Create Property
                    </a>
                </div>
            </div>

            <!-- Main Stats Row -->
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mt-6 pt-6 border-t border-white/10">
                <div class="bg-white/5 p-3 rounded-2xl backdrop-blur-sm border border-white/5 hover:bg-white/10 transition-all duration-200 cursor-pointer" @click="activeTab = 'users'">
                    <div class="text-xs text-indigo-200">Total Users</div>
                    <div class="text-xl font-bold mt-0.5 flex items-baseline gap-1">
                        {{ $stats['total_users'] }}
                    </div>
                </div>
                <div class="bg-white/5 p-3 rounded-2xl backdrop-blur-sm border border-white/5 hover:bg-white/10 transition-all duration-200 cursor-pointer" @click="activeTab = 'users'">
                    <div class="text-xs text-indigo-200">Customers</div>
                    <div class="text-xl font-bold mt-0.5">{{ $stats['total_customers'] }}</div>
                </div>
                <div class="bg-white/5 p-3 rounded-2xl backdrop-blur-sm border border-white/5 hover:bg-white/10 transition-all duration-200 cursor-pointer" @click="activeTab = 'analytics'">
                    <div class="text-xs text-indigo-200">Properties</div>
                    <div class="text-xl font-bold mt-0.5">{{ $stats['total_properties'] }}</div>
                </div>
                <div class="bg-white/5 p-3 rounded-2xl backdrop-blur-sm border border-white/5 hover:bg-white/10 transition-all duration-200 cursor-pointer" @click="activeTab = 'analytics'">
                    <div class="text-xs text-indigo-200">B2B Partners</div>
                    <div class="text-xl font-bold mt-0.5">{{ $stats['b2b_partners'] }}</div>
                </div>
                <div class="bg-white/5 p-3 rounded-2xl backdrop-blur-sm border border-white/5 hover:bg-white/10 transition-all duration-200 cursor-pointer relative" @click="activeTab = 'actions'">
                    <div class="text-xs text-indigo-200">Pending Properties</div>
                    <div class="text-xl font-bold mt-0.5 flex items-center justify-between">
                        <span>{{ $stats['pending_properties'] }}</span>
                        @if($stats['pending_properties'] > 0)
                            <span class="w-2.5 h-2.5 bg-rose-500 rounded-full animate-pulse"></span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Styled Tabs -->
        <div class="bg-white/60 p-1.5 rounded-2xl border border-gray-100 flex gap-2 max-w-lg shadow-sm backdrop-blur-md">
            <button @click="activeTab = 'users'" 
                :class="activeTab === 'users' ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-white hover:text-gray-900'"
                class="flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-users"></i>
                Users
            </button>
            <button @click="activeTab = 'analytics'" 
                :class="activeTab === 'analytics' ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-white hover:text-gray-900'"
                class="flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-chart-pie"></i>
                Analytics
            </button>
            <button @click="activeTab = 'actions'" 
                :class="activeTab === 'actions' ? 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md' : 'text-gray-600 hover:bg-white hover:text-gray-900'"
                class="flex-1 py-3 px-4 rounded-xl text-sm font-semibold transition-all duration-200 flex items-center justify-center gap-2 relative">
                <i class="fas fa-tasks"></i>
                Actions
                @if($stats['pending_properties'] > 0)
                    <span class="absolute top-1 right-2 bg-rose-500 text-white text-[10px] w-5 h-5 rounded-full flex items-center justify-center font-bold border-2 border-white">
                        {{ $stats['pending_properties'] }}
                    </span>
                @endif
            </button>
        </div>

        <!-- Tab Contents -->
        
        <!-- ============================================ -->
        <!-- 1. USERS TAB -->
        <!-- ============================================ -->
        <div x-show="activeTab === 'users'" x-transition class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Registered Users -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 lg:col-span-2 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-gray-900">Recent Owners & Staff</h3>
                            <p class="text-xs text-gray-500 mt-0.5">List of recently joined accounts in the system.</p>
                        </div>
                        <a href="{{ route('admin.user-management') }}" class="text-xs text-indigo-600 font-semibold hover:underline flex items-center gap-1">
                            View All <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentUsers as $user)
                            <div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-tr from-purple-100 to-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center font-bold">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-900">{{ $user->name }}</h4>
                                        <p class="text-xs text-gray-500">{{ $user->mobile_number }} • {{ $user->email ?? 'No email' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <p class="text-xs font-semibold text-gray-900">{{ $user->properties_count }} properties</p>
                                    </div>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-gray-400 hover:text-indigo-600 p-1.5 rounded-lg hover:bg-gray-100 transition-all">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-400">
                                <i class="fas fa-users text-2xl mb-2 block"></i>
                                <p class="text-sm">No registered owners found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Customer Database Highlights -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-gray-900">Guest Database</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Quick lookup metrics of guests.</p>
                        </div>
                        <a href="{{ route('admin.customer-data') }}" class="text-xs text-indigo-600 font-semibold hover:underline flex items-center gap-1">
                            View All <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="bg-gray-50 p-4 rounded-2xl flex items-center justify-between border border-gray-100">
                            <div>
                                <span class="text-xs text-gray-500 font-medium">Database Volume</span>
                                <h4 class="text-2xl font-black text-gray-900 mt-0.5">{{ $stats['total_customers'] }}</h4>
                            </div>
                            <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-address-book text-lg"></i>
                            </div>
                        </div>

                        <div class="p-4 border border-dashed border-gray-200 rounded-2xl space-y-3">
                            <h4 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Helpful Resources</h4>
                            <ul class="space-y-2 text-xs">
                                <li>
                                    <a href="{{ route('admin.download-sample-locations') }}" class="text-indigo-600 hover:underline flex items-center gap-1.5 font-medium">
                                        <i class="fas fa-download text-indigo-400"></i> Download Location Template JSON
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- 2. ANALYTICS TAB -->
        <!-- ============================================ -->
        <div x-show="activeTab === 'analytics'" x-transition class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Location Distribution -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 lg:col-span-2 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-gray-900">Properties by Location</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Top geographical hubs for stay loops.</p>
                        </div>
                        <a href="{{ route('admin.location-analytics') }}" class="text-xs text-indigo-600 font-semibold hover:underline flex items-center gap-1">
                            More Insights <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        @if($locationStats->isEmpty())
                            <div class="text-center py-8 text-gray-400">
                                <i class="fas fa-map-marked-alt text-2xl mb-2 block"></i>
                                <p class="text-sm">No properties with locations indexed.</p>
                            </div>
                        @else
                            <div class="space-y-4">
                                @foreach($locationStats as $loc)
                                    <div>
                                        <div class="flex justify-between items-center text-xs font-semibold text-gray-700 mb-1">
                                            <span>{{ $loc['location'] }}</span>
                                            <span>{{ $loc['property_count'] }} {{ Str::plural('property', $loc['property_count']) }} ({{ $loc['approval_rate'] }}% approved)</span>
                                        </div>
                                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                                            <div class="bg-indigo-600 h-full rounded-full" style="width: {{ min(100, $loc['property_count'] * 15) }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- B2B Partners Highlights -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-gray-900">Recent B2B Partners</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Corporate partner integrations.</p>
                        </div>
                        <a href="{{ route('admin.b2b-management') }}" class="text-xs text-indigo-600 font-semibold hover:underline flex items-center gap-1">
                            Manage B2B <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($recentB2bPartners as $partner)
                            <div class="p-4 hover:bg-gray-50 transition-colors flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-900">{{ $partner->company_name }}</h4>
                                    <p class="text-[10px] text-gray-400">Contact: {{ $partner->contactUser->name ?? 'N/A' }}</p>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-bold rounded-xl bg-purple-50 text-purple-700">
                                    {{ $partner->requests_count }} requests
                                </span>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-400">
                                <i class="fas fa-handshake text-2xl mb-2 block"></i>
                                <p class="text-sm">No corporate B2B partners registered.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- 3. ACTIONS TAB (Approvals & Requests) -->
        <!-- ============================================ -->
        <div x-show="activeTab === 'actions'" x-transition class="space-y-6">
            <div class="max-w-4xl mx-auto space-y-6">
                
                <!-- Pending Property Approvals Widget -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            <span class="w-2.5 h-2.5 bg-rose-500 rounded-full animate-pulse"></span>
                            <h3 class="font-bold text-gray-900">Properties Pending Approval</h3>
                        </div>
                        <a href="{{ route('admin.property-approvals') }}" class="text-xs text-indigo-600 font-semibold hover:underline flex items-center gap-1">
                            Expand List <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($pendingPropertiesList as $property)
                            <div class="p-4 space-y-3">
                                <div>
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900">{{ $property->name }}</h4>
                                            <p class="text-xs text-gray-500">{{ $property->category->name }} • Owner: {{ $property->owner->name }}</p>
                                        </div>
                                        <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 rounded-full">
                                            Pending
                                        </span>
                                    </div>
                                    @if($property->description)
                                        <p class="text-xs text-gray-600 bg-gray-50 p-2.5 rounded-xl mt-2 border border-gray-100">{{ Str::limit($property->description, 120) }}</p>
                                    @endif
                                </div>
                                <div class="flex space-x-2">
                                    <form method="POST" action="{{ route('admin.properties.approve', $property) }}" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full bg-gradient-to-r from-emerald-600 to-green-600 text-white py-2 rounded-xl text-xs font-semibold hover:from-emerald-700 hover:to-green-700 shadow-sm transition-all duration-200">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.properties.reject', $property) }}" class="flex-1" onsubmit="return handleReject(event, this)">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="reason" id="reject-reason-{{ $property->id }}">
                                        <button type="submit" class="w-full bg-gradient-to-r from-rose-600 to-pink-600 text-white py-2 rounded-xl text-xs font-semibold hover:from-rose-700 hover:to-pink-700 shadow-sm transition-all duration-200">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-gray-400">
                                <i class="fas fa-check-circle text-2xl mb-2 text-emerald-500 block"></i>
                                <p class="text-sm">All properties have been reviewed.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        function handleReject(event, form) {
            event.preventDefault();
            const propertyId = form.querySelector('input[name="reason"]').id.split('-').pop();
            const reason = prompt('Please provide a reason for rejection:');
            if (reason && reason.trim()) {
                form.querySelector('input[name="reason"]').value = reason.trim();
                form.submit();
            }
            return false;
        }
    </script>
@endsection