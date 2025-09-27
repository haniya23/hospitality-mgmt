@extends('layouts.app')

@section('title', 'Booking Dashboard - Stay loops')

@section('header')
    <x-page-header 
        title="Booking Dashboard" 
        subtitle="Create and manage your bookings" 
        icon="calendar-plus">
    </x-page-header>
@endsection

@section('content')
<div x-data="bookingDashboard()" x-init="init()" class="space-y-6">
    <!-- Quick Action Cards -->
    <div class="grid md:grid-cols-3 gap-6">
        <!-- Normal Booking -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-all cursor-pointer group"
             @click="startNormalBooking()">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-calendar-plus text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Normal Booking</h3>
                    <p class="text-sm text-gray-600">Standard booking flow</p>
                </div>
            </div>
            <p class="text-gray-600 text-sm mb-4">Select property → accommodation → dates → guest details → confirm</p>
            <div class="flex items-center text-blue-600 font-medium">
                <span>Start Booking</span>
                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </div>

        <!-- Ease Booking -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-all cursor-pointer group"
             @click="startEaseBooking()">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-bolt text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Ease Booking</h3>
                    <p class="text-sm text-gray-600">Quick date-first booking</p>
                </div>
            </div>
            <p class="text-gray-600 text-sm mb-4">Select dates → system suggests available rooms → pick & confirm</p>
            <div class="flex items-center text-green-600 font-medium">
                <span>Quick Book</span>
                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </div>

        <!-- B2B Booking -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-200 hover:shadow-xl transition-all cursor-pointer group"
             @click="startB2BBooking()">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                    <i class="fas fa-handshake text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">B2B Booking</h3>
                    <p class="text-sm text-gray-600">Partner bookings</p>
                </div>
            </div>
            <p class="text-gray-600 text-sm mb-4">Select partner → property → accommodation → dates → amount</p>
            <div class="flex items-center text-purple-600 font-medium">
                <span>B2B Book</span>
                <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </div>
    </div>

    <!-- Recent Bookings -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-gray-900">Recent Bookings</h2>
            <a href="{{ route('bookings.index') }}" class="text-green-600 hover:text-green-700 font-medium">
                View All →
            </a>
        </div>
        
        <div class="space-y-4">
            <template x-for="booking in recentBookings" :key="booking.id">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-calendar-check text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900" x-text="booking.guest_name"></h4>
                            <p class="text-sm text-gray-600" x-text="booking.property_name + ' - ' + booking.accommodation_name"></p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-semibold text-gray-900" x-text="'₹' + booking.total_amount"></p>
                        <p class="text-sm text-gray-600" x-text="booking.check_in_date + ' to ' + booking.check_out_date"></p>
                    </div>
                </div>
            </template>
            
            <div x-show="recentBookings.length === 0" class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar-plus text-gray-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No bookings yet</h3>
                <p class="text-gray-600 mb-4">Create your first booking using one of the options above</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-calendar text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Bookings</p>
                    <p class="text-lg font-semibold text-gray-900" x-text="stats.totalBookings"></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Confirmed</p>
                    <p class="text-lg font-semibold text-gray-900" x-text="stats.confirmedBookings"></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Pending</p>
                    <p class="text-lg font-semibold text-gray-900" x-text="stats.pendingBookings"></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-4 shadow-sm">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-rupee-sign text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Revenue</p>
                    <p class="text-lg font-semibold text-gray-900" x-text="'₹' + stats.totalRevenue"></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function bookingDashboard() {
    return {
        recentBookings: @json($recentBookings ?? []),
        stats: {
            totalBookings: {{ $stats['total_bookings'] ?? 0 }},
            confirmedBookings: {{ $stats['confirmed_bookings'] ?? 0 }},
            pendingBookings: {{ $stats['pending_bookings'] ?? 0 }},
            totalRevenue: {{ $stats['total_revenue'] ?? 0 }}
        },

        init() {
            console.log('Booking dashboard initialized');
            
            // Check if accommodation was pre-selected from accommodations page
            const urlParams = new URLSearchParams(window.location.search);
            const accommodationUuid = urlParams.get('accommodation');
            
            if (accommodationUuid) {
                const selectedAccommodation = sessionStorage.getItem('selectedAccommodation');
                if (selectedAccommodation) {
                    try {
                        const accommodation = JSON.parse(selectedAccommodation);
                        console.log('Pre-selected accommodation:', accommodation);
                        
                        // Show a notification about the pre-selected accommodation
                        this.showPreSelectedAccommodation(accommodation);
                        
                        // Auto-start normal booking with pre-selected accommodation
                        setTimeout(() => {
                            this.startNormalBookingWithAccommodation(accommodation);
                        }, 2000);
                    } catch (error) {
                        console.error('Error parsing selected accommodation:', error);
                    }
                }
            }
        },

        showPreSelectedAccommodation(accommodation) {
            // Create and show a notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white p-4 rounded-lg shadow-lg z-50 max-w-md';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3"></i>
                    <div>
                        <p class="font-semibold">Accommodation Selected!</p>
                        <p class="text-sm">${accommodation.name} - ₹${accommodation.base_price}/night</p>
                        <p class="text-xs mt-1">Redirecting to booking form...</p>
                    </div>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        },

        startNormalBookingWithAccommodation(accommodation) {
            // Clear the accommodation parameter from URL and sessionStorage
            const url = new URL(window.location);
            url.searchParams.delete('accommodation');
            window.history.replaceState({}, document.title, url.toString());
            sessionStorage.removeItem('selectedAccommodation');
            
            // Redirect to booking create page with accommodation pre-selected
            window.location.href = `/bookings/create?accommodation=${accommodation.uuid}&type=normal`;
        },

        startNormalBooking() {
            window.location.href = '/bookings/create?type=normal';
        },

        startEaseBooking() {
            window.location.href = '/bookings/create?type=ease';
        },

        startB2BBooking() {
            window.location.href = '/bookings/create?type=b2b';
        }
    }
}
</script>
@endpush
