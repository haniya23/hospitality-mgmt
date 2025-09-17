<div class="grid grid-cols-2 gap-4">
    <div class="bg-white rounded-xl p-4 shadow-sm">
        <div class="text-2xl font-bold text-gray-800" x-text="partners.filter(p => p.status === 'active').length"></div>
        <div class="text-sm text-gray-600">Active Partners</div>
    </div>
    <div class="bg-white rounded-xl p-4 shadow-sm">
        <div class="text-2xl font-bold text-gray-800" x-text="partners.reduce((sum, p) => sum + p.bookings, 0)"></div>
        <div class="text-sm text-gray-600">Total Bookings</div>
    </div>
</div>