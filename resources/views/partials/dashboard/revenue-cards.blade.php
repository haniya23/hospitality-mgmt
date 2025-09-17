@push('styles')
<style>
    .card-gradient { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
    .card-gradient-2 { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
</style>
@endpush

<div class="grid grid-cols-2 gap-4">
    <div class="card-gradient rounded-2xl p-4 shadow-lg">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                <i class="fas fa-rupee-sign text-orange-600"></i>
            </div>
            <span class="text-xs bg-green-100 text-green-600 px-2 py-1 rounded-full font-medium">+12%</span>
        </div>
        <div class="text-2xl font-bold text-gray-800" x-text="'₹' + formatNumber(revenue.today)"></div>
        <div class="text-sm text-gray-600">Today's Revenue</div>
    </div>

    <div class="card-gradient-2 rounded-2xl p-4 shadow-lg">
        <div class="flex items-center justify-between mb-2">
            <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center">
                <i class="fas fa-chart-line text-blue-600"></i>
            </div>
            <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-medium">+8%</span>
        </div>
        <div class="text-2xl font-bold text-gray-800" x-text="'₹' + formatNumber(revenue.month)"></div>
        <div class="text-sm text-gray-600">This Month</div>
    </div>
</div>