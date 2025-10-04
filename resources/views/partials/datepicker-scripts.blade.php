{{-- Datepicker Initialization Scripts --}}

<script>
    // Global datepicker initialization
    // Ensure jQuery is loaded before using it
    function initializeDatepickersWhenReady() {
        if (typeof $ === 'undefined') {
            // jQuery not loaded yet, try again in 100ms
            setTimeout(initializeDatepickersWhenReady, 100);
            return;
        }
        
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
    }
    
    // Start the initialization
    initializeDatepickersWhenReady();
</script>
