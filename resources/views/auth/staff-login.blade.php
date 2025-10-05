<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - Hospitality Management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-screen">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" x-data="staffLogin()">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-16 w-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-lg">
                    <i class="fas fa-users text-white text-2xl"></i>
                </div>
                <h2 class="mt-6 text-3xl font-bold text-gray-900">Staff Login</h2>
                <p class="mt-2 text-sm text-gray-600">Access your assigned tasks and responsibilities</p>
            </div>

            <!-- Login Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <form @submit.prevent="submitLogin()" class="space-y-6">
                    <!-- Mobile Number -->
                    <div>
                        <label for="mobile_number" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-mobile-alt mr-2 text-blue-500"></i>
                            Mobile Number
                        </label>
                        <input 
                            id="mobile_number" 
                            name="mobile_number" 
                            type="tel" 
                            x-model="form.mobile_number"
                            required 
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800" 
                            placeholder="Enter your mobile number"
                            maxlength="10"
                        >
                    </div>

                    <!-- PIN -->
                    <div>
                        <label for="pin" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-2 text-green-500"></i>
                            PIN (4 digits)
                        </label>
                        <input 
                            id="pin" 
                            name="pin" 
                            type="password" 
                            x-model="form.pin"
                            required 
                            maxlength="4"
                            pattern="[0-9]{4}"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-white hover:border-gray-300 font-medium text-gray-800" 
                            placeholder="Enter your 4-digit PIN"
                        >
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center">
                        <input 
                            id="remember" 
                            name="remember" 
                            type="checkbox" 
                            x-model="form.remember"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        >
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button 
                            type="submit" 
                            :disabled="isSubmitting"
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 transition-all duration-200"
                        >
                            <span x-show="!isSubmitting">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Login to Dashboard
                            </span>
                            <span x-show="isSubmitting">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Logging in...
                            </span>
                        </button>
                    </div>

                    <!-- Error Messages -->
                    <div x-show="errors.length > 0" class="bg-red-50 border border-red-200 rounded-xl p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Login Failed</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc list-inside space-y-1">
                                        <template x-for="error in errors" :key="error">
                                            <li x-text="error"></li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Help Text -->
                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Contact your manager if you need help accessing your account
                    </p>
                </div>
            </div>

            <!-- Owner Login Option -->
            <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
                <div class="text-center">
                    <div class="mx-auto h-12 w-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center shadow-lg mb-4">
                        <i class="fas fa-home text-white text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Property Owner Access</h3>
                    <p class="text-sm text-gray-600 mb-4">Manage your properties and bookings</p>
                    <a href="/login" class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-md hover:shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Owner Login
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center">
                <p class="text-sm text-gray-500">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Secure staff access portal
                </p>
            </div>
        </div>
    </div>

    <script>
    function staffLogin() {
        return {
            form: {
                mobile_number: '',
                pin: '',
                remember: false
            },
            isSubmitting: false,
            errors: [],

            async submitLogin() {
                this.isSubmitting = true;
                this.errors = [];

                try {
                    const response = await fetch('/staff/login', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(this.form)
                    });

                    if (response.ok) {
                        // Login successful, redirect
                        window.location.href = '/staff/dashboard';
                    } else {
                        const data = await response.json();
                        if (data.errors) {
                            // Handle validation errors
                            Object.values(data.errors).forEach(errorArray => {
                                this.errors.push(...errorArray);
                            });
                        } else {
                            this.errors.push(data.message || 'Login failed. Please try again.');
                        }
                    }
                } catch (error) {
                    console.error('Login error:', error);
                    this.errors.push('Network error. Please check your connection and try again.');
                } finally {
                    this.isSubmitting = false;
                }
            }
        }
    }
    </script>
</body>
</html>
