@extends('layouts.app')

@section('title', 'Dashboard - Hospitality Manager')

@section('header')
    @include('partials.dashboard.header')
@endsection

@section('content')
<div x-data="dashboardData()" x-init="init()" class="space-y-6">
    @include('partials.dashboard.revenue-cards')
    @include('partials.dashboard.properties-section')
    @include('partials.dashboard.recent-activity')
    @include('partials.dashboard.quick-stats')
</div>
@endsection

@push('scripts')
<script>
function dashboardData() {
    return {
        notifications: 3,
        todayStats: {
            checkIns: 12,
            checkOuts: 8,
            newBookings: 5
        },
        revenue: {
            today: 45000,
            month: 890000
        },
        stats: {
            totalGuests: 1240,
            avgRating: 4.8
        },
        properties: [
            {
                id: 1,
                name: "Ocean View Resort",
                category: "Resort",
                status: "active",
                rooms: 24,
                occupancy: 85,
                color: "#667eea, #764ba2"
            },
            {
                id: 2,
                name: "Mountain Lodge",
                category: "Lodge",
                status: "active",
                rooms: 12,
                occupancy: 92,
                color: "#f093fb, #f5576c"
            },
            {
                id: 3,
                name: "City Center Hotel",
                category: "Hotel",
                status: "pending",
                rooms: 36,
                occupancy: 67,
                color: "#4facfe, #00f2fe"
            }
        ],
        recentActivity: [
            {
                id: 1,
                type: 'booking',
                message: 'New booking from John Doe',
                time: '2 minutes ago'
            },
            {
                id: 2,
                type: 'checkin',
                message: 'Guest checked in to Room 205',
                time: '15 minutes ago'
            },
            {
                id: 3,
                type: 'checkout',
                message: 'Guest checked out from Room 102',
                time: '1 hour ago'
            },
            {
                id: 4,
                type: 'booking',
                message: 'Booking confirmed for Amanda Smith',
                time: '2 hours ago'
            }
        ],

        init() {
            console.log('Dashboard initialized');
        },

        formatNumber(num) {
            return new Intl.NumberFormat('en-IN').format(num);
        }
    }
}
</script>
@endpush