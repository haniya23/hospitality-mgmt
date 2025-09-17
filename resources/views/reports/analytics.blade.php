@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('header')
    @include('partials.reports.header')
@endsection

@section('content')
<div x-data="reportsData()" class="space-y-6">
    @include('partials.reports.metrics')
    @include('partials.reports.charts')
</div>
@endsection

@push('scripts')
<script>
function reportsData() {
    return {
        metrics: {
            revenue: 890000,
            bookings: 156,
            occupancy: 78,
            avgRating: 4.6
        },
        period: 'month'
    }
}
</script>
@endpush