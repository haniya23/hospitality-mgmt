@push('styles')
<style>
    .card-gradient { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
    .card-gradient-2 { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
    .card-gradient-3 { background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); }
    .card-gradient-4 { background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%); }
</style>
@endpush

<div class="grid grid-cols-2 gap-4">
    <div class="card-gradient rounded-xl p-4 shadow-sm">
        <div class="text-2xl font-bold text-gray-800" x-text="'₹' + metrics.revenue.toLocaleString()"></div>
        <div class="text-sm text-gray-600">Revenue</div>
    </div>
    <div class="card-gradient-2 rounded-xl p-4 shadow-sm">
        <div class="text-2xl font-bold text-gray-800" x-text="metrics.bookings"></div>
        <div class="text-sm text-gray-600">Bookings</div>
    </div>
    <div class="card-gradient-3 rounded-xl p-4 shadow-sm">
        <div class="text-2xl font-bold text-gray-800" x-text="metrics.occupancy + '%'"></div>
        <div class="text-sm text-gray-600">Occupancy</div>
    </div>
    <div class="card-gradient-4 rounded-xl p-4 shadow-sm">
        <div class="text-2xl font-bold text-gray-800" x-text="metrics.avgRating"></div>
        <div class="text-sm text-gray-600">Avg Rating</div>
    </div>
</div>