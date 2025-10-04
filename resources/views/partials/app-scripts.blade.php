{{-- Main Application Scripts --}}

<script>
    // Listen for sidebar toggle events
    document.addEventListener('DOMContentLoaded', function() {
        // SCROLL FIX: Ensure body scroll is never permanently disabled
        // Remove any stuck modal-open class that might prevent scrolling
        document.body.classList.remove('modal-open');
        
        // Ensure body can always scroll
        if (document.body.style.overflow === 'hidden') {
            document.body.style.overflow = '';
        }
        
        // PROFESSIONAL TOP BAR: Initialize responsive behavior
        initializeTopBar();
        
        // SIDEBAR FIX: Ensure sidebar toggle works
        initializeSidebarToggle();
        
        window.addEventListener('toggle-sidebar', function() {
            // Find the Alpine.js component and toggle sidebar
            const body = document.body;
            if (body._x_dataStack && body._x_dataStack[0]) {
                body._x_dataStack[0].sidebarOpen = !body._x_dataStack[0].sidebarOpen;
            }
        });
    });
    
    // PROFESSIONAL TOP BAR: Performance-optimized initialization
    function initializeTopBar() {
        const topBar = document.querySelector('.top-bar');
        if (!topBar) return;
        
        // Optimize scroll performance with throttling
        let ticking = false;
        
        function updateTopBarOnScroll() {
            if (!ticking) {
                requestAnimationFrame(() => {
                    // Add scroll-based effects here if needed
                    ticking = false;
                });
                ticking = true;
            }
        }
        
        // Passive scroll listener for better performance
        window.addEventListener('scroll', updateTopBarOnScroll, { passive: true });
        
        // Handle sidebar collapsed state
        const body = document.body;
        if (body._x_dataStack && body._x_dataStack[0]) {
            const alpineData = body._x_dataStack[0];
            
            // Initialize collapsed state if not set
            if (typeof alpineData.sidebarCollapsed === 'undefined') {
                alpineData.sidebarCollapsed = false;
            }
            
            // Watch for sidebar collapse changes with proper getter/setter
            let _sidebarCollapsed = alpineData.sidebarCollapsed || false;
            
            Object.defineProperty(alpineData, 'sidebarCollapsed', {
                get() { 
                    return _sidebarCollapsed; 
                },
                set(value) {
                    _sidebarCollapsed = value;
                    
                    // Update top bar
                    topBar.classList.toggle('sidebar-collapsed', value);
                    
                    // Update main content margin
                    const mainContainer = document.querySelector('.lg\\:ml-72');
                    if (mainContainer) {
                        if (value) {
                            mainContainer.classList.add('sidebar-collapsed');
                        } else {
                            mainContainer.classList.remove('sidebar-collapsed');
                        }
                    }
                    
                    // Trigger Alpine reactivity
                    if (window.Alpine) {
                        window.Alpine.store('sidebar', { collapsed: value });
                    }
                },
                enumerable: true,
                configurable: true
            });
        }
        
        // Optimize dropdown interactions
        const dropdowns = topBar.querySelectorAll('[x-data*="open"]');
        dropdowns.forEach(dropdown => {
            const button = dropdown.querySelector('button');
            const menu = dropdown.querySelector('[x-show="open"]');
            
            if (button && menu) {
                // Preload dropdown positioning
                button.addEventListener('mouseenter', () => {
                    menu.style.visibility = 'hidden';
                    menu.style.display = 'block';
                    // Force layout calculation
                    menu.offsetHeight;
                    menu.style.display = '';
                    menu.style.visibility = '';
                }, { once: true });
            }
        });
        
        // Accessibility improvements
        const menuButtons = topBar.querySelectorAll('[aria-expanded]');
        menuButtons.forEach(button => {
            button.addEventListener('click', () => {
                const expanded = button.getAttribute('aria-expanded') === 'true';
                button.setAttribute('aria-expanded', !expanded);
            });
        });
    }
    
    // SIDEBAR TOGGLE FIX: Ensure sidebar collapse works properly
    function initializeSidebarToggle() {
        // Simple approach: directly handle the toggle
        setTimeout(() => {
            const sidebar = document.querySelector('.sidebar-desktop');
            const mainContainer = document.querySelector('.lg\\:ml-72');
            const topBar = document.querySelector('.top-bar');
            
            if (sidebar) {
                // Initialize as expanded
                sidebar.classList.add('sidebar-expanded');
                
                // Find toggle buttons and add event listeners
                const toggleButtons = document.querySelectorAll('button[title*="sidebar"], button[title*="Sidebar"]');
                
                toggleButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
                        const newState = !isCollapsed;
                        
                        // Update CSS classes
                        if (isCollapsed) {
                            // Expand
                            sidebar.classList.remove('sidebar-collapsed');
                            sidebar.classList.add('sidebar-expanded');
                            mainContainer?.classList.remove('sidebar-collapsed');
                            topBar?.classList.remove('sidebar-collapsed');
                        } else {
                            // Collapse
                            sidebar.classList.remove('sidebar-expanded');
                            sidebar.classList.add('sidebar-collapsed');
                            mainContainer?.classList.add('sidebar-collapsed');
                            topBar?.classList.add('sidebar-collapsed');
                        }
                        
                        // Update Alpine.js state for x-if templates
                        const body = document.body;
                        if (body._x_dataStack && body._x_dataStack[0]) {
                            body._x_dataStack[0].sidebarCollapsed = newState;
                            
                            // Force Alpine.js to re-evaluate templates
                            if (window.Alpine) {
                                window.Alpine.nextTick(() => {
                                    // Trigger reactivity
                                    const event = new CustomEvent('alpine:updated');
                                    document.dispatchEvent(event);
                                });
                            }
                        }
                        
                        // Sidebar toggled
                    });
                });
            }
        }, 500);
    }
</script>
