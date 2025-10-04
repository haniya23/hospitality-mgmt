{{-- Professional Scripts Partial --}}

<!-- Core JavaScript Libraries -->
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- Custom Modal Scroll Lock Script -->
<script src="{{ asset('js/modal-scroll-lock.js') }}"></script>

<script>
    // Global scroll lock functions for vanilla JS modals
    let originalScrollY = 0;
    let originalBodyOverflow = '';
    
    function lockBodyScroll() {
        // Store original scroll position and body overflow
        originalScrollY = window.scrollY;
        originalBodyOverflow = document.body.style.overflow;
        
        // Lock body scroll
        document.body.style.overflow = 'hidden';
        document.body.style.position = 'fixed';
        document.body.style.top = `-${originalScrollY}px`;
        document.body.style.width = '100%';
        
        // Prevent scroll on touch devices
        document.body.style.touchAction = 'none';
    }
    
    function unlockBodyScroll() {
        // Restore body styles
        document.body.style.overflow = originalBodyOverflow;
        document.body.style.position = '';
        document.body.style.top = '';
        document.body.style.width = '';
        document.body.style.touchAction = '';
        
        // Restore scroll position
        window.scrollTo(0, originalScrollY);
    }
</script>
