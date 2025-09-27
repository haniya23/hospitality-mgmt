<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Stay loops</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-green-50 to-green-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full space-y-8 px-4" x-data="{ showPin: false }">
        <div class="text-center">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">
                Stay loops
            </h1>
            <h2 class="text-xl font-semibold text-gray-700">
                Create your account
            </h2>
        </div>
        <form class="mt-8 space-y-6" method="POST" action="{{ route('register') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input id="name" name="name" type="text" required 
                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                           placeholder="Enter your full name" value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="mobile_number" class="block text-sm font-medium text-gray-700">Mobile Number</label>
                    <input id="mobile_number" name="mobile_number" type="tel" required 
                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                           placeholder="Enter your mobile number" value="{{ old('mobile_number') }}">
                    @error('mobile_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address (Optional)</label>
                    <input id="email" name="email" type="email" 
                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500" 
                           placeholder="Enter your email address" value="{{ old('email') }}">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="pin" class="block text-sm font-medium text-gray-700">4-Digit PIN</label>
                    <input id="pin" name="pin" :type="showPin ? 'text' : 'password'" required maxlength="4"
                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-2xl tracking-widest" 
                           placeholder="••••">
                    @error('pin')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="pin_confirmation" class="block text-sm font-medium text-gray-700">Confirm PIN</label>
                    <input id="pin_confirmation" name="pin_confirmation" :type="showPin ? 'text' : 'password'" required maxlength="4"
                           class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-2xl tracking-widest" 
                           placeholder="••••">
                </div>
            </div>

            <div class="flex items-center">
                <input id="show-pin" type="checkbox" x-model="showPin" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                <label for="show-pin" class="ml-2 block text-sm text-gray-900">
                    Show PIN
                </label>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Create Account
                </button>
            </div>

            <div class="text-center">
                <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-500">
                    Already have an account? Sign in here
                </a>
            </div>
        </form>
    </div>
</body>
</html>