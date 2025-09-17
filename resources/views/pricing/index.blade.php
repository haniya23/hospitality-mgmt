@extends('layouts.app')

@section('title', 'Pricing Management')

@section('header')
    @include('partials.pricing.header')
@endsection

@section('content')
<div x-data="pricingData()" class="space-y-6">
    @include('partials.pricing.rules')
    @include('partials.pricing.calendar')
</div>
@endsection

@push('scripts')
<script>
function pricingData() {
    return {
        rules: [
            { id: 1, name: 'Weekend Premium', type: 'weekend', multiplier: 1.5, status: 'active' },
            { id: 2, name: 'Holiday Surge', type: 'holiday', multiplier: 2.0, status: 'active' },
            { id: 3, name: 'Off Season', type: 'seasonal', multiplier: 0.8, status: 'inactive' }
        ]
    }
}
</script>
@endpush