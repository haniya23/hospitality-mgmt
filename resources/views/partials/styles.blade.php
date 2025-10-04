{{-- Professional Styles Partial --}}

<!-- Google Fonts -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/ui-lightness/jquery-ui.css">

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    [x-cloak] { display: none !important; }
    
    /* SCROLL FIX: Ensure proper scrolling behavior */
    html, body {
        height: auto !important;
        min-height: 100vh;
        overflow-x: hidden;
        overflow-y: auto !important;
    }
    
    /* Ensure body scroll is never disabled */
    body.modal-open {
        overflow-y: auto !important;
        padding-right: 0 !important;
    }
    
    /* TOP BAR SCROLL FIX: Ensure top bar doesn't interfere with body scroll */
    .fixed {
        will-change: auto;
        pointer-events: auto;
    }
    
    /* Ensure main content area allows natural scrolling */
    main {
        position: relative;
        z-index: 1;
    }
    
    /* Force body scroll to always work */
    body {
        touch-action: auto !important;
        -webkit-overflow-scrolling: touch !important;
    }
    
    /* Prevent any fixed elements from blocking scroll */
    .fixed {
        pointer-events: auto !important;
    }
    
    .fixed * {
        pointer-events: auto !important;
    }
    
    /* ===================================
       PROFESSIONAL TOP BAR STYLES
       Future-proof, responsive, scroll-friendly
       =================================== */
    
    /* Top Bar Container */
    .top-bar {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 50;
        background: white;
        border-bottom: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        will-change: transform;
        contain: layout style;
        height: 4rem; /* Fixed height for consistent spacing */
    }
    
    /* Mobile Navigation */
    .top-bar__mobile {
        display: block;
    }
    
    .top-bar__mobile-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1rem;
        min-height: 4rem;
    }
    
    .top-bar__menu-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.5rem;
        background: #f3f4f6;
        border: none;
        color: #4b5563;
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .top-bar__menu-btn:hover {
        background: #e5e7eb;
    }
    
    .top-bar__brand {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex: 1;
        justify-content: center;
    }
    
    .top-bar__logo {
        width: 2rem;
        height: 2rem;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.875rem;
    }
    
    .top-bar__brand-text {
        display: flex;
        flex-direction: column;
    }
    
    .top-bar__title {
        font-size: 1.125rem;
        font-weight: 700;
        color: #111827;
        margin: 0;
        line-height: 1.2;
    }
    
    .top-bar__greeting {
        font-size: 0.75rem;
        color: #6b7280;
        margin: 0;
        display: none;
    }
    
    .top-bar__actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .top-bar__user-info {
        display: none;
        text-align: right;
    }
    
    .top-bar__user-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: #111827;
    }
    
    .top-bar__user-plan {
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .top-bar__dropdown {
        position: relative;
    }
    
    .top-bar__avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .top-bar__avatar:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-1px);
    }
    
    .top-bar__menu {
        position: absolute;
        right: 0;
        top: calc(100% + 0.5rem);
        width: 12rem;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
        padding: 0.25rem;
        z-index: 60;
    }
    
    .top-bar__menu-header {
        padding: 0.75rem;
        border-bottom: 1px solid #f3f4f6;
        margin-bottom: 0.25rem;
    }
    
    .top-bar__menu-user {
        font-size: 0.875rem;
        font-weight: 600;
        color: #111827;
    }
    
    .top-bar__menu-plan {
        font-size: 0.75rem;
        font-weight: 500;
        margin-top: 0.125rem;
    }
    
    .top-bar__menu-form {
        width: 100%;
    }
    
    .top-bar__menu-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #374151;
        text-decoration: none;
        border-radius: 0.5rem;
        transition: all 0.15s ease;
        border: none;
        background: none;
        cursor: pointer;
        text-align: left;
    }
    
    .top-bar__menu-item:hover {
        background: #f9fafb;
        color: #111827;
    }
    
    .top-bar__menu-item--danger {
        color: #dc2626;
    }
    
    .top-bar__menu-item--danger:hover {
        background: #fef2f2;
        color: #b91c1c;
    }
    
    /* Desktop Navigation */
    .top-bar__desktop {
        display: none;
    }
    
    .top-bar__desktop-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 1.5rem;
        min-height: 4rem;
        margin-left: 18rem;
        transition: margin-left 0.3s ease;
    }
    
    .top-bar__page-info {
        flex: 1;
    }
    
    .top-bar__page-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }
    
    .top-bar__breadcrumbs {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 0.25rem;
    }
    
    .top-bar__desktop-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .top-bar__user-section {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .top-bar__user-details {
        text-align: right;
    }
    
    .top-bar__user-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: #111827;
    }
    
    .top-bar__user-status {
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .top-bar__user-dropdown {
        position: relative;
    }
    
    .top-bar__user-avatar {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .top-bar__user-avatar:hover {
        background: linear-gradient(135deg, #059669, #047857);
        transform: translateY(-1px);
    }
    
    .top-bar__user-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 0.5rem);
        width: 12rem;
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        border: 1px solid #e5e7eb;
        padding: 0.25rem;
        z-index: 60;
    }
    
    .top-bar__user-menu-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: #374151;
        text-decoration: none;
        border-radius: 0.5rem;
        transition: all 0.15s ease;
        border: none;
        background: none;
        cursor: pointer;
        text-align: left;
    }
    
    .top-bar__user-menu-item:hover {
        background: #f9fafb;
        color: #111827;
    }
    
    .top-bar__user-menu-item--danger {
        color: #dc2626;
    }
    
    .top-bar__user-menu-item--danger:hover {
        background: #fef2f2;
        color: #b91c1c;
    }
    
    /* Responsive Breakpoints */
    @media (min-width: 640px) {
        .top-bar__greeting {
            display: block;
        }
        
        .top-bar__user-info {
            display: block;
        }
    }
    
    @media (min-width: 1024px) {
        .top-bar__mobile {
            display: none;
        }
        
        .top-bar__desktop {
            display: block;
        }
    }
    
    /* Sidebar Collapsed State */
    .sidebar-collapsed .top-bar__desktop-content {
        margin-left: 4rem;
    }
    
    /* Main container when sidebar is collapsed */
    @media (min-width: 1024px) {
        .lg\:ml-72.sidebar-collapsed {
            margin-left: 4rem !important;
        }
    }
    
    /* Layout Spacing Fixes */
    
    /* Top Navigation Spacing - Mobile */
    @media (max-width: 1023px) {
        /* Header section - starts below top bar */
        .lg\:ml-72 > div:first-child {
            padding-top: 5rem !important; /* Top bar height (4rem) + extra space (1rem) */
        }
        
        /* Main content container - no additional top padding needed */
        main {
            padding-top: 0 !important;
        }
    }
    
    /* Bottom Navigation Spacing */
    @media (max-width: 1023px) {
        /* Main content needs bottom spacing for floating nav */
        main {
            padding-bottom: 6rem !important; /* Space for floating bottom nav */
        }
        
        /* Ensure fixed elements don't overlap with bottom nav */
        .fixed {
            bottom: 0 !important; /* No bottom spacing */
        }
        
        /* Exception: Only accommodation popup needs bottom spacing */
        .fixed[x-data*="accommodation"],
        .property-accommodation-modal.fixed,
        .accommodation-modal.fixed {
            bottom: 6rem !important; /* Add space for bottom nav */
        }
    }
    
    /* Desktop Layout */
    @media (min-width: 1024px) {
        /* Main content container - starts below top bar */
        .lg\:ml-72 {
            margin-left: 18rem;
        }
        
        /* Header section - starts below top bar */
        .lg\:ml-72 > div:first-child {
            padding-top: 5rem; /* Top bar height (4rem) + extra space (1rem) */
        }
        
        /* When sidebar is collapsed */
        body:has([x-data*="sidebarCollapsed"]:is([x-data*="true"])) .lg\:ml-72 {
            margin-left: 4rem;
        }
    }
    
    /* Sidebar positioning - below top bar */
    @media (min-width: 1024px) {
        .sidebar,
        [class*="lg:fixed"][class*="lg:top-16"] {
            top: 4rem;
            height: calc(100vh - 4rem);
        }
        
        /* Sidebar width transitions */
        .sidebar-desktop.sidebar-expanded {
            width: 18rem !important;
            padding: 1.25rem !important;
        }
        
        .sidebar-desktop.sidebar-collapsed {
            width: 4rem !important;
            padding: 0.5rem !important;
        }
        
        .sidebar-desktop {
            transition: width 0.3s ease, padding 0.3s ease;
        }
        
        /* Visual feedback for sidebar state */
        
        /* When collapsed - hide expanded content, show collapsed content */
        .sidebar-desktop.sidebar-collapsed [x-show="!sidebarCollapsed"],
        .sidebar-desktop.sidebar-collapsed template[x-if="!sidebarCollapsed"] + *,
        .sidebar-desktop.sidebar-collapsed .sidebar-expanded-content {
            display: none !important;
        }
        
        .sidebar-desktop.sidebar-collapsed [x-show="sidebarCollapsed"],
        .sidebar-desktop.sidebar-collapsed template[x-if="sidebarCollapsed"] + *,
        .sidebar-desktop.sidebar-collapsed .sidebar-collapsed-content {
            display: flex !important;
        }
        
        /* When expanded - show expanded content, hide collapsed content */
        .sidebar-desktop.sidebar-expanded [x-show="sidebarCollapsed"],
        .sidebar-desktop.sidebar-expanded template[x-if="sidebarCollapsed"] + *,
        .sidebar-desktop.sidebar-expanded .sidebar-collapsed-content {
            display: none !important;
        }
        
        .sidebar-desktop.sidebar-expanded [x-show="!sidebarCollapsed"],
        .sidebar-desktop.sidebar-expanded template[x-if="!sidebarCollapsed"] + *,
        .sidebar-desktop.sidebar-expanded .sidebar-expanded-content {
            display: block !important;
        }
        
        /* Force Alpine templates to respect our CSS state */
        .sidebar-desktop.sidebar-collapsed template[x-if="sidebarCollapsed"] {
            display: block !important;
        }
        
        .sidebar-desktop.sidebar-expanded template[x-if="!sidebarCollapsed"] {
            display: block !important;
        }
    }
    
    /* Screen Reader Only */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }
    
    /* Select2 Custom Styling */
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
    
    /* Modal Positioning Fixes */
    @media (max-width: 1023px) {
        /* Specific fix for property accommodation popup only */
        .property-accommodation-modal,
        .accommodation-modal,
        [x-data*="accommodation"] {
            margin-bottom: 6rem !important;
            z-index: 50 !important;
        }
        
        /* Ensure accommodation popup submit buttons are visible */
        .accommodation-submit,
        .property-accommodation-modal button[type="submit"],
        .accommodation-modal button[type="submit"] {
            margin-bottom: 4rem !important; /* Increased from 2rem to 4rem */
            padding-bottom: 4rem !important; /* Increased from 2rem to 4rem */
            z-index: 51 !important;
        }
    }
    
    /* Mobile Responsive Accommodation Popup - Match Location Popup Style */
    @media (max-width: 768px) {
        /* Accommodation Modal Mobile Responsive - Same as Location */
        #accommodationModal {
            padding: 0.5rem !important;
        }
        
        #accommodationModal .flex.min-h-full {
            align-items: center !important; /* Match location popup */
            padding: 0.5rem !important; /* Match location popup */
        }
        
        #accommodationModal .relative.w-full.max-w-2xl {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            border-radius: 0.75rem !important; /* Match location popup */
            max-height: 90vh !important;
        }
        
        /* Modal Header Mobile */
        #accommodationModal .flex.items-center.justify-between.p-6 {
            padding: 1rem !important;
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 0.5rem !important;
        }
        
        #accommodationModal h3 {
            font-size: 1.125rem !important;
            line-height: 1.25rem !important;
        }
        
        #accommodationModal p {
            font-size: 0.875rem !important;
        }
        
        /* Modal Content Mobile */
        #accommodationModal .flex-1.overflow-y-auto.p-6 {
            padding: 1rem !important;
            max-height: calc(90vh - 8rem) !important;
        }
        
        /* Form Grid Mobile */
        #accommodationModal .grid.grid-cols-1.md\\:grid-cols-2 {
            grid-template-columns: 1fr !important;
            gap: 1rem !important;
        }
        
        /* Amenities Grid Mobile */
        #accommodationModal .grid.grid-cols-2.md\\:grid-cols-3.lg\\:grid-cols-4 {
            grid-template-columns: 1fr !important;
            gap: 0.75rem !important;
        }
        
        #accommodationModal .grid.grid-cols-2.md\\:grid-cols-3.lg\\:grid-cols-4 label {
            padding: 0.5rem !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 0.5rem !important;
            background: #f9fafb !important;
        }
        
        /* Modal Footer Mobile */
        #accommodationModal .flex.items-center.justify-end.gap-3.p-6 {
            padding: 1rem !important;
            flex-direction: row !important; /* Changed from column to row */
            gap: 0.75rem !important;
            margin-bottom: 4rem !important; /* Added extra scroll space */
        }
        
        #accommodationModal .flex.items-center.justify-end.gap-3.p-6 button {
            width: auto !important; /* Changed from 100% to auto */
            padding: 0.75rem 1rem !important;
            font-size: 0.875rem !important;
            margin-bottom: 2rem !important; /* Added extra space after buttons */
            flex: 1 !important; /* Equal width buttons */
        }
        
        /* Input Fields Mobile */
        #accommodationModal input,
        #accommodationModal textarea,
        #accommodationModal select {
            font-size: 16px !important; /* Prevents zoom on iOS */
            padding: 0.75rem !important;
        }
        
        /* File Input Mobile */
        #accommodationModal input[type="file"] {
            padding: 0.5rem !important;
        }
        
        /* Textarea Mobile */
        #accommodationModal textarea {
            min-height: 6rem !important;
        }
    }
    
    /* Small Mobile Screens - Match Location Popup */
    @media (max-width: 480px) {
        #accommodationModal .relative.w-full.max-w-2xl {
            max-height: 95vh !important;
            border-radius: 0.75rem !important; /* Match location popup */
        }
        
        #accommodationModal .flex-1.overflow-y-auto.p-6 {
            max-height: calc(95vh - 6rem) !important;
        }
        
        #accommodationModal .flex.items-center.justify-end.gap-3.p-6 {
            padding: 0.75rem !important;
            margin-bottom: 4rem !important; /* Added extra scroll space for small screens */
            flex-direction: row !important; /* Keep buttons in one row */
        }
        
        #accommodationModal .flex.items-center.justify-end.gap-3.p-6 button {
            margin-bottom: 2rem !important; /* Added extra space after buttons */
            flex: 1 !important; /* Equal width buttons */
        }
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
    
    /* Global Form Bottom Spacing for Mobile */
    @media (max-width: 768px) {
        /* Forms don't need extra bottom padding since main has it */
        form {
            padding-bottom: 0 !important;
        }
        
        /* Specific form containers */
        .max-w-4xl,
        .max-w-7xl,
        .max-w-6xl {
            padding-bottom: 0 !important;
        }
        
        /* Action button containers */
        .flex.justify-end,
        .flex.flex-col.sm\\:flex-row.justify-end {
            padding-bottom: 0 !important;
        }
        
        /* Submit button containers */
        button[type="submit"] {
            margin-bottom: 1rem !important;
        }
    }
    
    /* Desktop - Normal spacing */
    @media (min-width: 769px) {
        form {
            padding-bottom: 2rem !important;
        }
        
        .max-w-4xl,
        .max-w-7xl,
        .max-w-6xl {
            padding-bottom: 2rem !important;
        }
        
        .flex.justify-end,
        .flex.flex-col.sm\\:flex-row.justify-end {
            padding-bottom: 2rem !important;
        }
    }
</style>
