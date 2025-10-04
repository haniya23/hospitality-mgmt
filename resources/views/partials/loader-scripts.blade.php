{{-- Global Loader Scripts --}}

<script>
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
    
    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            hideGlobalLoader();
        }, 500);
    });
    
    // Hide loader when page is fully loaded
    window.addEventListener('load', function() {
        setTimeout(() => {
            hideGlobalLoader();
        }, 800);
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
