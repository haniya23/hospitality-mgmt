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
    
    <!-- jQuery, jQuery UI, and Select2 CSS and JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
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
        
        /* jQuery UI Datepicker Custom Styling */
        .ui-datepicker {
            border: 1px solid #d1d5db !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            font-family: inherit !important;
            font-size: 14px !important;
            background: white !important;
            padding: 8px !important;
        }
        
        .ui-datepicker-header {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
            border: none !important;
            border-radius: 0.5rem !important;
            color: white !important;
            font-weight: 600 !important;
            padding: 8px !important;
            margin-bottom: 8px !important;
        }
        
        .ui-datepicker-title {
            color: white !important;
            font-weight: 600 !important;
        }
        
        .ui-datepicker-prev, .ui-datepicker-next {
            background: rgba(255, 255, 255, 0.2) !important;
            border: none !important;
            border-radius: 0.375rem !important;
            color: white !important;
            cursor: pointer !important;
            width: 24px !important;
            height: 24px !important;
        }
        
        .ui-datepicker-prev:hover, .ui-datepicker-next:hover {
            background: rgba(255, 255, 255, 0.3) !important;
        }
        
        .ui-datepicker-calendar {
            width: 100% !important;
        }
        
        .ui-datepicker-calendar th {
            background: #f8fafc !important;
            color: #6b7280 !important;
            font-weight: 600 !important;
            padding: 8px 4px !important;
            text-align: center !important;
            border: none !important;
            font-size: 12px !important;
        }
        
        .ui-datepicker-calendar td {
            padding: 2px !important;
            border: none !important;
        }
        
        .ui-datepicker-calendar td a {
            display: block !important;
            padding: 8px !important;
            text-align: center !important;
            text-decoration: none !important;
            color: #374151 !important;
            border-radius: 0.375rem !important;
            transition: all 0.2s ease !important;
            font-weight: 500 !important;
        }
        
        .ui-datepicker-calendar td a:hover {
            background: #3b82f6 !important;
            color: white !important;
        }
        
        .ui-datepicker-calendar .ui-datepicker-today a {
            background: #dbeafe !important;
            color: #1d4ed8 !important;
            font-weight: 600 !important;
        }
        
        .ui-datepicker-calendar .ui-datepicker-current-day a,
        .ui-datepicker-calendar .ui-state-active {
            background: #3b82f6 !important;
            color: white !important;
            font-weight: 600 !important;
        }
        
        .ui-datepicker-calendar .ui-datepicker-other-month a {
            color: #9ca3af !important;
        }
        
        /* Date input styling */
        .datepicker-input {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3e%3cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'%3e%3c/path%3e%3c/svg%3e") !important;
            background-repeat: no-repeat !important;
            background-position: right 12px center !important;
            background-size: 20px !important;
            cursor: pointer !important;
        }
        
        /* Global datepicker initialization */
    </style>
    
    <script>
        // Global datepicker initialization
        $(document).ready(function() {
            // Initialize all datepicker inputs
            function initializeDatepickers() {
                $('.datepicker-input').each(function() {
                    const $input = $(this);
                    
                    // Skip if already initialized
                    if ($input.hasClass('hasDatepicker')) {
                        return;
                    }
                    
                    // Configure datepicker options
                    const options = {
                        dateFormat: 'yy-mm-dd',
                        changeMonth: true,
                        changeYear: true,
                        showAnim: 'slideDown',
                        yearRange: '-10:+10'
                    };
                    
                    // Add minDate for future dates if needed
                    if ($input.attr('name') && $input.attr('name').includes('check_in')) {
                        options.minDate = 0; // Today or future
                    }
                    
                    // Initialize datepicker
                    $input.datepicker(options);
                    
                    // Handle Livewire integration
                    if ($input.attr('wire:model')) {
                        $input.on('change', function() {
                            const wireModel = $input.attr('wire:model');
                            if (window.Livewire) {
                                window.Livewire.emit('dateChanged', wireModel, $input.val());
                            }
                        });
                    }
                });
            }
            
            // Initialize on page load
            initializeDatepickers();
            
            // Re-initialize when new content is loaded (for dynamic content)
            $(document).on('DOMNodeInserted', function() {
                setTimeout(initializeDatepickers, 100);
            });
            
            // Livewire hook for re-initialization
            if (window.Livewire) {
                window.Livewire.hook('message.processed', () => {
                    setTimeout(initializeDatepickers, 100);
                });
            }
        });
    </script>
    
    <style>
        
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
        
        <main class="px-4 pt-20 pb-40 lg:pt-16 lg:pb-8 overflow-y-auto">
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