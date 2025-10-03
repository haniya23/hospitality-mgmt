/**
 * Modal Scroll Lock Mixin for Alpine.js
 * Prevents background scrolling when modals are open
 */
window.modalScrollLock = () => ({
    originalScrollY: 0,
    originalBodyOverflow: '',
    
    /**
     * Lock body scroll and store original state
     */
    lockScroll() {
        // Store original scroll position and body overflow
        this.originalScrollY = window.scrollY;
        this.originalBodyOverflow = document.body.style.overflow;
        
        // Lock body scroll
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.top = `-${this.originalScrollY}px`;
        document.body.style.width = '100%';
        
        // Prevent scroll on touch devices
        document.body.style.touchAction = 'none';
    },
    
    /**
     * Unlock body scroll and restore original state
     */
    unlockScroll() {
        // Restore body styles
        document.body.style.overflow = this.originalBodyOverflow;
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.touchAction = '';
        
        // Restore scroll position
        window.scrollTo(0, this.originalScrollY);
    },
    
    /**
     * Initialize scroll lock watcher for a modal state
     */
    initScrollLock(modalStateProperty = 'show') {
        // Watch for modal state changes
        this.$watch(modalStateProperty, (value) => {
            if (value) {
                this.lockScroll();
            } else {
                this.unlockScroll();
            }
        });
        
        // Clean up on component destroy
        this.$el.addEventListener('alpine:destroyed', () => {
            this.unlockScroll();
        });
        
        // Clean up on page unload
        window.addEventListener('beforeunload', () => {
            this.unlockScroll();
        });
    }
});

/**
 * Enhanced modal component with scroll lock
 * Usage: x-data="modalWithScrollLock()"
 */
window.modalWithScrollLock = () => ({
    ...modalScrollLock(),
    show: false,
    
    init() {
        this.initScrollLock('show');
    },
    
    open() {
        this.show = true;
    },
    
    close() {
        this.show = false;
    },
    
    toggle() {
        this.show = !this.show;
    }
});

/**
 * Simple scroll lock utility for existing modals
 * Usage: x-data="{ show: false, ...modalScrollLock() }"
 */
window.simpleModalScrollLock = () => ({
    ...modalScrollLock(),
    
    /**
     * Call this in your modal's init() method
     */
    setupScrollLock(modalStateProperty = 'show') {
        this.initScrollLock(modalStateProperty);
    }
});

