<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stay loops - Property Management Made Easy</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 min-h-screen">
    <!-- Navigation Bar -->
    <nav class="bg-white/80 backdrop-blur-lg border-b border-gray-200/50 shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-500 rounded-lg flex items-center justify-center">
                        <span class="text-white text-lg font-bold">S</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Stay loops</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-2 rounded-xl font-medium hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="border border-green-600 text-green-600 px-6 py-2 rounded-xl font-medium hover:bg-green-50 transition-colors">
                        Create Account
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-6xl font-bold text-gray-900 mb-4">
                Stay loops
            </h1>
            <p class="text-lg md:text-xl text-gray-600 max-w-2xl mx-auto">
                Streamline your property management with our comprehensive hospitality solution
            </p>
        </div>

        <!-- Features Grid -->
        <div class="grid md:grid-cols-3 gap-6 mb-12">
            <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-6 shadow-lg">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m0 0h2M7 7h10M7 11h10M7 15h10"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Property Management</h3>
                <p class="text-gray-600">Manage multiple properties, rooms, and accommodations from one dashboard</p>
            </div>

            <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-6 shadow-lg">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Staff Management</h3>
                <p class="text-gray-600">Organize your team with role-based access and staff assignments</p>
            </div>

            <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-6 shadow-lg">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Analytics & Reports</h3>
                <p class="text-gray-600">Track performance with detailed analytics and comprehensive reports</p>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="text-center">
            <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl p-8 shadow-lg max-w-md mx-auto">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Get Started Today</h2>
                <div class="space-y-4">
                    <a href="{{ route('login') }}" class="block w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-3 px-6 rounded-xl font-medium hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="block w-full border border-green-600 text-green-600 py-3 px-6 rounded-xl font-medium hover:bg-green-50 transition-colors">
                        Create Account
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>