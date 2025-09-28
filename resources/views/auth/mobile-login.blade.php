<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Stay loops</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Header -->
    <header class="relative overflow-hidden mb-6">
        <!-- Modern Gradient Background -->
        <div class="absolute inset-0 bg-gradient-to-br from-white via-green-50/30 to-emerald-50/40 rounded-2xl"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-green-500/5 via-emerald-500/5 to-green-500/5 rounded-2xl"></div>
        
        <!-- Glass overlay -->
        <div class="absolute inset-0 bg-white/60 backdrop-blur-sm rounded-2xl border border-white/20"></div>
        
        <!-- Content -->
        <div class="relative px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <div class="text-center">
                <!-- Logo -->
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg mx-auto mb-4">
                    <i class="fas fa-home text-white text-2xl sm:text-3xl"></i>
                </div>
                
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">
                    Stay loops
                </h1>
                <p class="text-lg sm:text-xl text-gray-600">
                    Sign in to your account
                </p>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-md mx-auto px-4 pb-8" x-data="{ showPin: false }">
        <!-- Form Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 sm:p-8 border border-gray-100">
            <form class="space-y-6" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">Mobile Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                            <input id="mobile_number" name="mobile_number" type="tel" required 
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all" 
                                   placeholder="Enter your mobile number" value="{{ old('mobile_number') }}">
                        </div>
                    </div>
                    
                    <div>
                        <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">4-Digit PIN</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="pin" name="pin" :type="showPin ? 'text' : 'password'" required maxlength="4"
                                   class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-2xl tracking-widest transition-all" 
                                   placeholder="••••">
                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                            <p class="text-red-600 text-sm">{{ $errors->first() }}</p>
                        </div>
                    </div>
                @endif

                <!-- Options -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">
                            Remember me
                        </label>
                    </div>

                    <div class="flex items-center">
                        <input id="show-pin" type="checkbox" x-model="showPin" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="show-pin" class="ml-2 block text-sm text-gray-900">
                            Show PIN
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign in
                    </button>
                </div>

                <!-- Register Link -->
                <div class="text-center">
                    <a href="{{ route('register') }}" class="font-medium text-green-600 hover:text-green-500 transition-colors">
                        Don't have an account? Register here
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>