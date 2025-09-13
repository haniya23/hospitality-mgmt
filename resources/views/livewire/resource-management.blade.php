@section('page-title', 'Resources')

<div class="space-y-6">
    <!-- Header -->
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-900">Resource Center</h2>
        <p class="text-gray-600 mt-2">Quick access to all your management tools</p>
    </div>

    <!-- Resources Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($resources as $resource)
            <a href="{{ route($resource['route']) }}" 
               class="bg-white rounded-2xl shadow-lg p-6 hover:shadow-xl transition-all duration-200 hover:-translate-y-1 group">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform
                        {{ $resource['color'] === 'purple' ? 'bg-purple-100 text-purple-600' : '' }}
                        {{ $resource['color'] === 'green' ? 'bg-green-100 text-green-600' : '' }}
                        {{ $resource['color'] === 'blue' ? 'bg-blue-100 text-blue-600' : '' }}
                        {{ $resource['color'] === 'teal' ? 'bg-teal-100 text-teal-600' : '' }}
                        {{ $resource['color'] === 'indigo' ? 'bg-indigo-100 text-indigo-600' : '' }}
                        {{ $resource['color'] === 'emerald' ? 'bg-emerald-100 text-emerald-600' : '' }}">
                        
                        @if($resource['icon'] === 'building')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        @elseif($resource['icon'] === 'calendar')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        @elseif($resource['icon'] === 'chart')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        @elseif($resource['icon'] === 'users')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        @elseif($resource['icon'] === 'handshake')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        @elseif($resource['icon'] === 'clipboard')
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        @endif
                    </div>
                    
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 group-hover:text-{{ $resource['color'] }}-600 transition-colors">
                            {{ $resource['title'] }}
                        </h3>
                        <p class="text-sm text-gray-600 mt-1">{{ $resource['description'] }}</p>
                        
                        <div class="flex items-center mt-3 text-sm text-{{ $resource['color'] }}-600">
                            <span>Access now</span>
                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>

    <!-- Quick Stats -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Overview</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ auth()->user()->properties()->count() }}</div>
                <div class="text-sm text-gray-600">Properties</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-emerald-600">{{ \App\Models\Reservation::whereHas('accommodation.property', fn($q) => $q->where('owner_id', auth()->id()))->where('status', 'confirmed')->count() }}</div>
                <div class="text-sm text-gray-600">Active Bookings</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-teal-600">{{ \App\Models\Guest::count() }}</div>
                <div class="text-sm text-gray-600">Customers</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ \App\Models\B2bPartner::where('requested_by', auth()->id())->count() }}</div>
                <div class="text-sm text-gray-600">B2B Partners</div>
            </div>
        </div>
    </div>
</div>