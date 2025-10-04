<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Stay loops')</title>
    
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-1VLPS4F73T"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-1VLPS4F73T');
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @include('partials.styles')
    
    @include('partials.scripts')
    @include('partials.datepicker-scripts')
    
    @stack('styles')
</head>
<body class="bg-gray-50" x-data="{ sidebarOpen: false, sidebarCollapsed: false, globalLoading: false, loadingMessage: 'Loading...' }">
    
    @include('partials.top-bar')
    @include('partials.sidebar')
    
    <div class="lg:ml-72 transition-all duration-300" :class="{ 'lg:ml-16': sidebarCollapsed }">
        @yield('header')
        
        <main class="px-4 pt-16 pb-32 lg:pt-4 lg:pb-8 sm:pb-36">
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
    
    @include('partials.app-scripts')
    @include('partials.loader-scripts')
    
    @stack('scripts')
    
    <!-- Development Utilities (only in debug mode) -->
    @if(config('app.debug'))
    @endif
    
    <!-- Toast Notifications -->
    <x-toast-notification />
    
    <!-- Subscription Upgrade Modal -->
    <x-subscription-upgrade-modal />
</body>
</html>