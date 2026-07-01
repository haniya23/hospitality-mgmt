<div class="bg-gradient-to-br from-white to-gray-50 rounded-2xl shadow-lg border border-gray-200 p-6 mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <!-- Left Section -->
        <div class="flex-1">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-bed text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                        Create Accommodation
                    </h1>
                    <p class="text-gray-600 text-sm sm:text-base">Add a new room or accommodation to your property</p>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ route('accommodations.index') }}" 
               class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 rounded-xl hover:from-gray-200 hover:to-gray-300 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 font-semibold border border-gray-300">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Accommodations
            </a>
        </div>
    </div>
</div>
