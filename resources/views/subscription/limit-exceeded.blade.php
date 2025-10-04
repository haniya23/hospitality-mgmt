<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Subscription Limit Exceeded - Stay Loops</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Assuming partials.styles and partials.scripts are not strictly necessary if all styles are moved to Tailwind, or they contain global styles/scripts -->
    @include('partials.styles') 
    @include('partials.scripts')
    
    <style>
        /* Custom styles not easily achievable with direct Tailwind classes or for specific SVG sizing */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .limit-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #ff6b6b, #feca57, #48dbfb, #ff9ff3);
        }
        .limit-icon {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
        }
        .whatsapp-icon {
            width: 24px; /* Ensure SVG size is consistent */
            height: 24px;
        }
        /* Pulse animation */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        .animate-pulse-custom {
            animation: pulse 2s infinite;
        }

        /* Responsive adjustments for smaller screens that might need finer control */
        @media (max-width: 640px) {
            .limit-icon {
                width: 60px;
                height: 60px;
            }
            .limit-icon svg {
                width: 30px;
                height: 30px;
            }
        }
        @media (max-width: 480px) {
            .limit-icon {
                width: 50px;
                height: 50px;
            }
            .limit-icon svg {
                width: 25px;
                height: 25px;
            }
            .whatsapp-icon {
                width: 20px;
                height: 20px;
            }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center font-sans p-4 m-0">
    <div class="limit-container bg-white rounded-3xl shadow-2xl p-8 max-w-lg w-full text-center relative overflow-hidden
                sm:p-6 sm:rounded-2xl
                xs:p-5 xs:rounded-xl">
        <div class="limit-icon w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse-custom
                    sm:w-16 sm:h-16 sm:mb-4
                    xs:w-12 xs:h-12 xs:mb-3">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-10 h-10 text-white
                        sm:w-8 sm:h-8
                        xs:w-6 xs:h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
        </div>
        
        <h1 class="text-gray-800 text-3xl font-bold mb-4 leading-tight
                   sm:text-2xl sm:mb-3
                   xs:text-xl xs:mb-2">Accommodation Limit Exceeded</h1>
        
        <p class="text-gray-700 text-base leading-relaxed mb-6
                  sm:text-sm sm:mb-5
                  xs:text-sm xs:leading-normal xs:mb-4">
            You've exceeded the maximum number of accommodations allowed on your current plan. 
            Choose an option below to unlock additional accommodations and continue using the app.
        </p>
        
        <!-- Quick Access to Plans Page -->
        <div class="mb-6">
            <a href="{{ route('subscription.plans') }}" 
               class="flex items-center gap-4 bg-gradient-to-br from-indigo-500 to-purple-600 text-white no-underline p-4 rounded-xl transition-all duration-300 shadow-lg hover:shadow-xl hover:-translate-y-0.5
                      sm:p-3 sm:gap-3 sm:rounded-lg
                      xs:p-2.5 xs:gap-2 xs:rounded-md">
                <span class="text-2xl flex-shrink-0 sm:text-xl xs:text-lg">💳</span>
                <div class="flex-1 text-left">
                    <span class="block font-semibold text-lg sm:text-base xs:text-sm">Buy Accommodations Online</span>
                    <span class="block text-sm opacity-90 sm:text-xs xs:text-xs">Add accommodations instantly at ₹99/month each</span>
                </div>
                <span class="text-xl font-bold flex-shrink-0 sm:text-lg xs:text-base">→</span>
            </a>
        </div>
        
        <!-- Plan Details Section -->
        <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-6
                    sm:p-4 sm:rounded-lg sm:mb-5
                    xs:p-3 xs:rounded-md xs:mb-4">
            <h3 class="text-gray-800 text-xl font-semibold mb-4 text-center
                       sm:text-lg sm:mb-3
                       xs:text-base xs:mb-2">📊 Your Current Plan Details</h3>
            <div class="flex flex-col gap-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0
                            sm:py-1.5 xs:py-1">
                    <span class="text-gray-700 font-medium text-sm sm:text-xs xs:text-xs">Plan:</span>
                    <span class="text-gray-800 font-semibold text-sm sm:text-xs xs:text-xs">{{ $planDetails['name'] }} Plan</span>
                </div>
                @if($planDetails['billing_cycle'])
                <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0
                            sm:py-1.5 xs:py-1">
                    <span class="text-gray-700 font-medium text-sm sm:text-xs xs:text-xs">Billing Cycle:</span>
                    <span class="text-gray-800 font-semibold text-sm sm:text-xs xs:text-xs">{{ ucfirst($planDetails['billing_cycle']) }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0
                            sm:py-1.5 xs:py-1">
                    <span class="text-gray-700 font-medium text-sm sm:text-xs xs:text-xs">Properties Limit:</span>
                    <span class="text-gray-800 font-semibold text-sm sm:text-xs xs:text-xs">{{ $planDetails['properties_used'] }}/{{ $planDetails['properties_limit'] }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0
                            sm:py-1.5 xs:py-1">
                    <span class="text-gray-700 font-medium text-sm sm:text-xs xs:text-xs">Base Accommodations:</span>
                    <span class="text-gray-800 font-semibold text-sm sm:text-xs xs:text-xs">{{ $planDetails['base_accommodation_limit'] }}</span>
                </div>
                @if($planDetails['addon_count'] > 0)
                <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0
                            sm:py-1.5 xs:py-1">
                    <span class="text-gray-700 font-medium text-sm sm:text-xs xs:text-xs">Added Accommodations:</span>
                    <span class="text-gray-800 font-semibold text-sm sm:text-xs xs:text-xs">+{{ $planDetails['addon_count'] }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0
                            sm:py-1.5 xs:py-1">
                    <span class="text-gray-700 font-medium text-sm sm:text-xs xs:text-xs">Total Accommodations:</span>
                    <span class="text-gray-800 font-semibold text-sm sm:text-xs xs:text-xs">{{ $planDetails['accommodations_allowed'] }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0
                            sm:py-1.5 xs:py-1">
                    <span class="text-gray-700 font-medium text-sm sm:text-xs xs:text-xs">Accommodations Used:</span>
                    <span class="text-red-600 bg-red-100 px-2 py-1 rounded text-sm font-semibold sm:text-xs xs:text-xs">{{ $planDetails['accommodations_used'] }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0
                            sm:py-1.5 xs:py-1">
                    <span class="text-gray-700 font-medium text-sm sm:text-xs xs:text-xs">Exceeded By:</span>
                    <span class="text-red-600 bg-red-100 px-2 py-1 rounded text-sm font-semibold sm:text-xs xs:text-xs">+{{ $planDetails['accommodations_exceeded'] }}</span>
                </div>
                @if($planDetails['subscription_ends_at'])
                <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-b-0
                            sm:py-1.5 xs:py-1">
                    <span class="text-gray-700 font-medium text-sm sm:text-xs xs:text-xs">Subscription Ends:</span>
                    <span class="text-gray-800 font-semibold text-sm sm:text-xs xs:text-xs">{{ \Carbon\Carbon::parse($planDetails['subscription_ends_at'])->format('M d, Y') }}</span>
                </div>
                @endif
                @if($planDetails['active_subscription'])
                <div class="flex justify-between items-center py-2 last:border-b-0
                            sm:py-1.5 xs:py-1">
                    <span class="text-gray-700 font-medium text-sm sm:text-xs xs:text-xs">Total Cost:</span>
                    <span class="text-gray-800 font-semibold text-sm sm:text-xs xs:text-xs">₹{{ number_format($planDetails['active_subscription']->total_subscription_amount, 0) }}/{{ $planDetails['active_subscription']->billing_interval }}</span>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Addon Options Section -->
        <div class="mb-6">
            <h3 class="text-gray-800 text-xl font-semibold mb-4 text-center
                       sm:text-lg sm:mb-3
                       xs:text-base xs:mb-2">🚀 Choose Your Solution</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                <!-- Addon Accommodations Option -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-6 text-center transition-all duration-300 hover:border-indigo-500 hover:shadow-md
                            sm:p-5 sm:rounded-lg
                            xs:p-4 xs:rounded-md">
                    <div class="text-3xl mb-3 sm:text-2xl xs:text-xl">➕</div>
                    <h4 class="text-gray-800 text-lg font-semibold mb-2 sm:text-base xs:text-sm">Add Accommodations</h4>
                    <p class="text-gray-700 text-sm leading-snug mb-4 sm:text-xs xs:text-xs">Add {{ $planDetails['accommodations_exceeded'] }} accommodation(s) as an addon to unlock your account</p>
                    <div class="mb-4">
                        <span class="block text-gray-800 text-xl font-bold mb-1 sm:text-lg xs:text-base">₹{{ $planDetails['accommodations_exceeded'] * $planDetails['addon_price_per_month'] }}/month</span>
                        <span class="block text-gray-600 text-xs sm:text-xs xs:text-xxs">(₹{{ $planDetails['addon_price_per_month'] }} per accommodation/month)</span>
                    </div>
                    <a href="https://wa.me/919400960223?text=Hi%2C%20I%20would%20like%20to%20add%20{{ $planDetails['accommodations_exceeded'] }}%20accommodation(s)%20as%20addon%20to%20my%20{{ $planDetails['name'] }}%20plan%20at%20₹{{ $planDetails['addon_price_per_month'] }}%20per%20accommodation%20per%20month." 
                       target="_blank" 
                       class="inline-block w-full py-3 px-6 rounded-lg font-semibold text-sm no-underline transition-all duration-300 bg-indigo-500 text-white border-2 border-indigo-500 hover:bg-indigo-600 hover:border-indigo-600 hover:-translate-y-px
                              sm:py-2.5 sm:px-5 sm:text-xs
                              xs:py-2 xs:px-4 xs:text-xs">
                        📱 Contact WhatsApp
                    </a>
                    <a href="{{ route('subscription.plans') }}" 
                       class="inline-block w-full py-3 px-6 rounded-lg font-semibold text-sm no-underline transition-all duration-300 mt-2 bg-white text-indigo-500 border-2 border-indigo-500 hover:bg-indigo-500 hover:text-white hover:-translate-y-px
                              sm:py-2.5 sm:px-5 sm:text-xs
                              xs:py-2 xs:px-4 xs:text-xs">
                        💳 Buy Addons Online
                    </a>
                </div>
                
                <!-- Upgrade Plan Option -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-6 text-center transition-all duration-300 hover:border-indigo-500 hover:shadow-md
                            sm:p-5 sm:rounded-lg
                            xs:p-4 xs:rounded-md">
                    <div class="text-3xl mb-3 sm:text-2xl xs:text-xl">⬆️</div>
                    <h4 class="text-gray-800 text-lg font-semibold mb-2 sm:text-base xs:text-sm">Upgrade Plan</h4>
                    @if($planDetails['name'] === 'Trial' || $planDetails['name'] === 'Starter')
                        <p class="text-gray-700 text-sm leading-snug mb-4 sm:text-xs xs:text-xs">Upgrade to Professional plan for 15 accommodations and advanced features</p>
                        <div class="mb-4">
                            <span class="block text-gray-800 text-xl font-bold mb-1 sm:text-lg xs:text-base">₹999/month</span>
                            <span class="block text-gray-600 text-xs sm:text-xs xs:text-xxs">or ₹9,990/year (17% savings)</span>
                        </div>
                    @else
                        <p class="text-gray-700 text-sm leading-snug mb-4 sm:text-xs xs:text-xs">Contact us for enterprise plans with unlimited accommodations</p>
                        <div class="mb-4">
                            <span class="block text-gray-800 text-xl font-bold mb-1 sm:text-lg xs:text-base">Contact for Pricing</span>
                            <span class="block text-gray-600 text-xs sm:text-xs xs:text-xxs">Custom solutions available</span>
                        </div>
                    @endif
                    <a href="https://wa.me/919400960223?text=Hi%2C%20I%20would%20like%20to%20upgrade%20my%20{{ $planDetails['name'] }}%20plan%20to%20a%20higher%20tier%20with%20more%20accommodations.%20I%20currently%20have%20{{ $planDetails['accommodations_used'] }}%20accommodations%20and%20need%20more." 
                       target="_blank" 
                       class="inline-block w-full py-3 px-6 rounded-lg font-semibold text-sm no-underline transition-all duration-300 bg-white text-indigo-500 border-2 border-indigo-500 hover:bg-indigo-500 hover:text-white hover:-translate-y-px
                              sm:py-2.5 sm:px-5 sm:text-xs
                              xs:py-2 xs:px-4 xs:text-xs">
                        📱 Contact WhatsApp
                    </a>
                    <a href="{{ route('subscription.plans') }}" 
                       class="inline-block w-full py-3 px-6 rounded-lg font-semibold text-sm no-underline transition-all duration-300 mt-2 bg-indigo-500 text-white border-2 border-indigo-500 hover:bg-indigo-600 hover:border-indigo-600 hover:-translate-y-px
                              sm:py-2.5 sm:px-5 sm:text-xs
                              xs:py-2 xs:px-4 xs:text-xs">
                        💳 View Plans Online
                    </a>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-xl p-5 mb-6
                    sm:p-4 sm:rounded-lg sm:mb-5
                    xs:p-3 xs:rounded-md xs:mb-4">
            <h3 class="text-gray-800 text-lg font-semibold mb-3 sm:text-base xs:text-sm">📞 Contact Information</h3>
            <div class="flex flex-col gap-2 text-gray-700">
                <div class="flex items-center justify-center gap-2 flex-wrap text-sm sm:text-xs xs:text-xs">
                    <span>📱 WhatsApp:</span>
                    <a href="https://wa.me/919400960223" target="_blank" class="text-green-500 font-semibold no-underline hover:underline">+91 9400960223</a>
                </div>
                <div class="flex items-center justify-center gap-2 flex-wrap text-sm sm:text-xs xs:text-xs">
                    <span>💬 Message:</span>
                    <span class="break-words text-center">"I need to upgrade my subscription to add more accommodations"</span>
                </div>
                <div class="flex items-center justify-center gap-2 flex-wrap text-sm sm:text-xs xs:text-xs">
                    <span>💳 Online:</span>
                    <a href="{{ route('subscription.plans') }}" class="text-indigo-500 font-semibold no-underline hover:underline">Buy Addons Instantly</a>
                </div>
            </div>
        </div>
        
        <div class="bg-red-100 border border-red-300 rounded-lg p-4 mt-6
                    sm:p-3 sm:rounded-md sm:mt-5
                    xs:p-2.5 xs:rounded-sm xs:mt-4">
            <h4 class="text-red-700 font-semibold mb-2 text-lg sm:text-base xs:text-sm">🚫 Features Currently Blocked:</h4>
            <p class="text-red-800 text-sm leading-snug sm:text-xs xs:text-xs">• Adding new accommodations<br>
               • Creating new properties<br>
               • Accessing advanced features<br>
               • Full app functionality</p>
        </div>
        
        <form method="POST" action="{{ route('logout') }}" class="w-full max-w-xs mt-6">
            @csrf
            <button type="submit" class="inline-block bg-gray-200 text-gray-700 no-underline py-3 px-6 rounded-lg font-medium transition-all duration-300 w-full hover:bg-gray-300 hover:text-gray-800
                  sm:py-2.5 sm:px-5 sm:mt-5 sm:max-w-xs
                  xs:py-2 xs:px-4 xs:mt-4 xs:max-w-full">
            Logout
        </button>
        </form>
    </div>
</body>
</html>