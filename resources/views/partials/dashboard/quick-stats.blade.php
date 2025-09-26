@push('styles')
<style>
    .card-gradient-3 { background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); }
    .card-gradient-4 { background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%); }
</style>
@endpush

<div class="grid grid-cols-2 gap-4">
    <div class="card-gradient-3 rounded-2xl p-4 shadow-lg cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:scale-105" 
         @click="navigateToGuests()">
        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center mb-3">
            <i class="fas fa-users text-purple-600"></i>
        </div>
        <div class="text-2xl font-bold text-gray-800" x-text="stats.totalGuests"></div>
        <div class="text-sm text-gray-600">Total Guests</div>
    </div>

    <div class="card-gradient-4 rounded-2xl p-4 shadow-lg cursor-pointer hover:shadow-xl transition-all duration-300 transform hover:scale-105" 
         @click="navigateToReviews()">
        <div class="w-10 h-10 rounded-xl bg-white bg-opacity-30 flex items-center justify-center mb-3">
            <i class="fas fa-star text-yellow-600"></i>
        </div>
        <div class="text-2xl font-bold text-gray-800" x-text="stats.avgRating"></div>
        <div class="text-sm text-gray-600">Avg Rating</div>
    </div>
</div>