<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Stay loops')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- jQuery and Select2 CSS and JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Custom Select2 Styling -->
    <style>
        [x-cloak] { display: none !important; }
        
        .select2-container--default .select2-selection--single {
            height: 42px;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 0 12px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 40px;
            padding-left: 0;
            color: #374151;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 40px;
            right: 8px;
        }
        
        .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
        }
        
        .select2-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6;
        }
        
        .select2-container--default .select2-results__option[aria-selected=true] {
            background-color: #e5e7eb;
            color: #374151;
        }
        
        /* Global Loader Styles */
        .global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.3s ease;
        }
        
        .global-loader.hidden {
            opacity: 0;
            pointer-events: none;
        }
        
        .loader-spinner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }
        
        .loader-text {
            color: #6b7280;
            font-size: 1rem;
            font-weight: 500;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: false, sidebarCollapsed: false, globalLoading: false, loadingMessage: 'Loading...' }">
    @include('partials.top-bar')
    @include('partials.sidebar')
    
    <div class="lg:ml-72 transition-all duration-300" :class="{ 'lg:ml-16': sidebarCollapsed }">
        @yield('header')
        
        <main class="px-4 pt-20 pb-40 lg:pt-6 lg:pb-32">
            @yield('content')
        </main>
    </div>
    
    @include('partials.bottom-bar')
    
    <!-- Global Loader -->
    <div x-show="globalLoading" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="global-loader"
         x-cloak>
        <div class="loader-spinner">
            <!-- From Uiverse.io by devAaus -->
            <div class="w-20 h-20 border-4 border-transparent text-blue-400 text-4xl animate-spin flex items-center justify-center border-t-blue-400 rounded-full">
                <div class="w-16 h-16 border-4 border-transparent text-red-400 text-2xl animate-spin flex items-center justify-center border-t-red-400 rounded-full"></div>
            </div>
            <p class="loader-text" x-text="loadingMessage"></p>
        </div>
    </div>
    
    <script>
        // Listen for sidebar toggle events
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('toggle-sidebar', function() {
                // Find the Alpine.js component and toggle sidebar
                const body = document.body;
                if (body._x_dataStack && body._x_dataStack[0]) {
                    body._x_dataStack[0].sidebarOpen = !body._x_dataStack[0].sidebarOpen;
                }
            });
        });
        
        // Global loader functions
        window.showGlobalLoader = function(message = 'Loading...') {
            const body = document.body;
            if (body._x_dataStack && body._x_dataStack[0]) {
                body._x_dataStack[0].loadingMessage = message;
                body._x_dataStack[0].globalLoading = true;
            }
        };
        
        window.hideGlobalLoader = function() {
            const body = document.body;
            if (body._x_dataStack && body._x_dataStack[0]) {
                body._x_dataStack[0].globalLoading = false;
            }
        };
        
        // Show loader immediately on page load
        showGlobalLoader('Loading page...');
        
        // Hide loader when page is fully loaded
        window.addEventListener('load', function() {
            setTimeout(() => hideGlobalLoader(), 800);
        });
        
        // Also hide loader when DOM is ready (faster)
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => hideGlobalLoader(), 500);
        });
        
        // Show loader on page refresh/reload
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                showGlobalLoader('Loading page...');
                setTimeout(() => hideGlobalLoader(), 300);
            }
        });
        
        // Show loader on back/forward navigation
        window.addEventListener('popstate', function() {
            showGlobalLoader('Loading page...');
            setTimeout(() => hideGlobalLoader(), 300);
        });
        
        // Intercept fetch requests to show/hide global loader
        const originalFetch = window.fetch;
        window.fetch = function(...args) {
            // Show loader for non-GET requests
            if (args[1] && args[1].method && args[1].method !== 'GET') {
                showGlobalLoader('Processing...');
            }
            
            return originalFetch.apply(this, args)
                .then(response => {
                    // Hide loader when request completes
                    setTimeout(() => hideGlobalLoader(), 300);
                    return response;
                })
                .catch(error => {
                    // Hide loader on error
                    hideGlobalLoader();
                    throw error;
                });
        };
        
        // Intercept page navigation to show loader
        window.addEventListener('beforeunload', function() {
            showGlobalLoader('Loading page...');
        });
        
        // Show loader for form submissions
        document.addEventListener('submit', function(e) {
            if (e.target.tagName === 'FORM') {
                showGlobalLoader('Processing form...');
            }
        });
        
        // Show loader for link clicks (except hash links)
        document.addEventListener('click', function(e) {
            if (e.target.tagName === 'A' && e.target.href && !e.target.href.includes('#')) {
                showGlobalLoader('Loading page...');
            }
        });
    </script>
    
    @stack('scripts')
    
    <!-- Toast Notifications -->
    <x-toast-notification />
    
    <!-- Subscription Upgrade Modal -->
    <x-subscription-upgrade-modal />
</body>
</html>