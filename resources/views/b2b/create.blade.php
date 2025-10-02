@extends('layouts.app')

@section('title', 'Add B2B Partner')

@section('header')
    <div x-data="b2bCreateData()" x-init="init()">
        @include('partials.b2b.header')
    </div>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sm:p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Add New B2B Partner</h2>
            <p class="text-gray-600 mt-1">Create a new business partnership</p>
        </div>

        <form action="{{ route('b2b.store') }}" method="POST" class="space-y-6" id="b2bCreateForm">
            @csrf
            
            <!-- Partner Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Partner Information</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="partner_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Partner Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="partner_name" name="partner_name" value="{{ old('partner_name') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('partner_name') border-red-500 @enderror" 
                               placeholder="e.g., Global Travel Agency" required>
                        @error('partner_name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="partner_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Partner Type <span class="text-red-500">*</span>
                        </label>
                        <select id="partner_type" name="partner_type" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent select2-dropdown @error('partner_type') border-red-500 @enderror" required>
                            <option value="">Select Type</option>
                            <option value="Travel Agent" {{ old('partner_type') == 'Travel Agent' ? 'selected' : '' }}>Travel Agent</option>
                            <option value="OTA" {{ old('partner_type') == 'OTA' ? 'selected' : '' }}>OTA (Online Travel Agency)</option>
                            <option value="Corporate" {{ old('partner_type') == 'Corporate' ? 'selected' : '' }}>Corporate</option>
                            <option value="Hotel Chain" {{ old('partner_type') == 'Hotel Chain' ? 'selected' : '' }}>Hotel Chain</option>
                            <option value="Tour Operator" {{ old('partner_type') == 'Tour Operator' ? 'selected' : '' }}>Tour Operator</option>
                        </select>
                        @error('partner_type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Contact Information</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Person <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('contact_person') border-red-500 @enderror" 
                               placeholder="e.g., John Smith" required>
                        @error('contact_person')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mobile_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Mobile Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('mobile_number') border-red-500 @enderror" 
                               placeholder="e.g., +91 9876543210" required>
                        @error('mobile_number')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email (Optional)</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror" 
                           placeholder="e.g., contact@travelagency.com">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Commission Settings -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Commission Settings</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="commission_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            Commission Rate (%)
                        </label>
                        <input type="number" id="commission_rate" name="commission_rate" value="{{ old('commission_rate', 10) }}" 
                               min="0" max="100" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('commission_rate') border-red-500 @enderror" 
                               placeholder="10.00">
                        @error('commission_rate')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="default_discount_pct" class="block text-sm font-medium text-gray-700 mb-2">
                            Default Discount (%)
                        </label>
                        <input type="number" id="default_discount_pct" name="default_discount_pct" value="{{ old('default_discount_pct', 5) }}" 
                               min="0" max="100" step="0.01"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('default_discount_pct') border-red-500 @enderror" 
                               placeholder="5.00">
                        @error('default_discount_pct')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Automatic Features</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>A reserved customer will be automatically created for this partner</li>
                                <li>The partner will receive a default PIN (0000) for login</li>
                                <li>Partner status will be set to "Pending" until activated</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('b2b.index') }}" 
                   class="w-full sm:w-auto px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-center">
                    Cancel
                </a>
                <button type="submit" id="submitBtn"
                        class="w-full sm:w-auto px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl">
                    <span id="submitBtnText">Create B2B Partner</span>
                    <span id="submitBtnLoader" class="hidden">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function b2bCreateData() {
    return {
        // Dummy data for the stat cards to prevent Alpine.js errors
        partners: [],
        
        get activePartners() {
            return 0; // No partners on create page
        },
        
        get totalBookings() {
            return 0; // No bookings on create page
        },
        
        get totalPartners() {
            return 0; // No partners on create page
        },
        
        init() {
            // Initialize any needed functionality
            console.log('B2B Create page initialized');
            this.initializeSelect2();
            this.setupFormDebugging();
        },

        initializeSelect2() {
            // Initialize Select2 for partner type dropdown
            $('#partner_type').select2({
                placeholder: 'Select Type',
                allowClear: false,
                width: '100%'
            });

            // Handle Select2 change event and sync with form validation
            $('#partner_type').on('change', function() {
                console.log('🔍 DEBUG: Select2 partner_type changed to:', $(this).val());
                
                // Trigger validation check
                const form = document.getElementById('b2bCreateForm');
                if (form) {
                    const isFormValid = form.checkValidity();
                    console.log('🔍 DEBUG: Form valid after Select2 change:', isFormValid);
                }
            });

            console.log('✅ DEBUG: Select2 initialized for partner_type');
        },

        setupFormDebugging() {
            const form = document.getElementById('b2bCreateForm');
            const submitBtn = document.getElementById('submitBtn');
            const submitBtnText = document.getElementById('submitBtnText');
            const submitBtnLoader = document.getElementById('submitBtnLoader');
            const debugStatus = document.getElementById('debugStatus');
            const formValidationStatus = document.getElementById('formValidationStatus');
            const submitButtonStatus = document.getElementById('submitButtonStatus');

            console.log('🔍 DEBUG: Form elements found:', {
                form: !!form,
                submitBtn: !!submitBtn,
                submitBtnText: !!submitBtnText,
                submitBtnLoader: !!submitBtnLoader
            });

            // Update debug status
            if (debugStatus) {
                debugStatus.innerHTML = `✅ Debug mode active - Form: ${!!form ? '✅' : '❌'}, Submit Button: ${!!submitBtn ? '✅' : '❌'}`;
            }

            if (!form) {
                console.error('❌ DEBUG: Form not found!');
                if (debugStatus) debugStatus.innerHTML = '❌ Form element not found!';
                return;
            }

            if (!submitBtn) {
                console.error('❌ DEBUG: Submit button not found!');
                if (debugStatus) debugStatus.innerHTML = '❌ Submit button not found!';
                return;
            }

            // Update submit button status
            if (submitButtonStatus) {
                submitButtonStatus.innerHTML = `Submit button status: ${submitBtn.disabled ? 'Disabled' : 'Enabled'}`;
            }

            // Add click event listener to submit button
            submitBtn.addEventListener('click', (e) => {
                console.log('🖱️ DEBUG: Submit button clicked!');
                console.log('🔍 DEBUG: Event details:', {
                    type: e.type,
                    target: e.target,
                    defaultPrevented: e.defaultPrevented
                });

                if (submitButtonStatus) {
                    submitButtonStatus.innerHTML = '🖱️ Submit button clicked!';
                }

                // Prevent default to handle validation first
                e.preventDefault();

                // Check if form is valid
                const isValid = form.checkValidity();
                console.log('✅ DEBUG: Form validity:', isValid);

                if (formValidationStatus) {
                    formValidationStatus.innerHTML = `Form validation: ${isValid ? '✅ Valid' : '❌ Invalid'}`;
                }

                if (!isValid) {
                    console.log('❌ DEBUG: Form is invalid, showing validation errors');
                    form.reportValidity();
                    
                    // Show which fields are invalid
                    const invalidFields = form.querySelectorAll(':invalid');
                    console.log('❌ DEBUG: Invalid fields:', Array.from(invalidFields).map(f => f.name));
                    
                    if (formValidationStatus) {
                        const fieldNames = Array.from(invalidFields).map(f => f.name).join(', ');
                        formValidationStatus.innerHTML = `❌ Invalid fields: ${fieldNames}`;
                    }
                    return;
                }

                // Show loading state
                submitBtnText.classList.add('hidden');
                submitBtnLoader.classList.remove('hidden');
                submitBtn.disabled = true;

                console.log('⏳ DEBUG: Loading state activated');
                console.log('📤 DEBUG: About to submit form programmatically');
                
                if (submitButtonStatus) {
                    submitButtonStatus.innerHTML = '⏳ Loading state activated - Submitting form...';
                }

                // Submit the form programmatically
                setTimeout(() => {
                    console.log('📤 DEBUG: Submitting form now...');
                    try {
                        form.submit();
                        
                        // Set a timeout to detect if submission hangs
                        setTimeout(() => {
                            console.log('⚠️ DEBUG: Form submission timeout - checking if still on same page');
                            if (window.location.href === 'http://hospitality-mgmt.test/b2b/create') {
                                console.error('❌ DEBUG: Form submission appears to have failed - still on create page');
                                
                                // Reset button state
                                submitBtnText.classList.remove('hidden');
                                submitBtnLoader.classList.add('hidden');
                                submitBtn.disabled = false;
                                
                                if (submitButtonStatus) {
                                    submitButtonStatus.innerHTML = '❌ Form submission timed out!';
                                }
                            }
                        }, 5000); // 5 second timeout
                        
                    } catch (error) {
                        console.error('❌ DEBUG: Form submission error:', error);
                        
                        // Reset button state on error
                        submitBtnText.classList.remove('hidden');
                        submitBtnLoader.classList.add('hidden');
                        submitBtn.disabled = false;
                        
                        if (submitButtonStatus) {
                            submitButtonStatus.innerHTML = '❌ Form submission failed!';
                        }
                        
                        alert('Form submission failed. Please try again.');
                    }
                }, 100); // Small delay to ensure UI updates
            });

            // Add form submit event listener
            form.addEventListener('submit', (e) => {
                console.log('📤 DEBUG: Form submit event triggered!');
                console.log('🔍 DEBUG: Form data:', new FormData(form));
                
                if (submitButtonStatus) {
                    submitButtonStatus.innerHTML = '📤 Form submit event triggered!';
                }
                
                // Log all form fields
                const formData = new FormData(form);
                console.log('📋 DEBUG: Form fields:');
                for (let [key, value] of formData.entries()) {
                    console.log(`  ${key}: ${value}`);
                }

                // Check for required fields
                const requiredFields = form.querySelectorAll('[required]');
                console.log('🔍 DEBUG: Required fields check:');
                requiredFields.forEach(field => {
                    console.log(`  ${field.name}: ${field.value ? '✅ filled' : '❌ empty'}`);
                });
            });

            // Add error handling for failed submissions
            form.addEventListener('error', (e) => {
                console.error('❌ DEBUG: Form error:', e);
                
                if (submitButtonStatus) {
                    submitButtonStatus.innerHTML = '❌ Form submission error occurred!';
                }
                
                // Reset button state
                submitBtnText.classList.remove('hidden');
                submitBtnLoader.classList.add('hidden');
                submitBtn.disabled = false;
            });

            // Add real-time validation checker
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                field.addEventListener('input', () => {
                    const isFormValid = form.checkValidity();
                    if (formValidationStatus) {
                        formValidationStatus.innerHTML = `Real-time validation: ${isFormValid ? '✅ Valid' : '❌ Invalid'}`;
                    }
                });

                // Special handling for select fields
                if (field.tagName === 'SELECT') {
                    field.addEventListener('change', () => {
                        console.log(`🔍 DEBUG: Select field ${field.name} changed to: "${field.value}"`);
                        const isFormValid = form.checkValidity();
                        if (formValidationStatus) {
                            formValidationStatus.innerHTML = `Select changed - Form valid: ${isFormValid ? '✅ Valid' : '❌ Invalid'}`;
                        }
                    });
                }
            });

            // Specific debugging for partner_type field
            const partnerTypeField = document.getElementById('partner_type');
            if (partnerTypeField) {
                console.log('🔍 DEBUG: Partner type field found:', {
                    value: partnerTypeField.value,
                    required: partnerTypeField.required,
                    validity: partnerTypeField.validity,
                    validationMessage: partnerTypeField.validationMessage
                });

                // Log when partner type changes
                partnerTypeField.addEventListener('change', () => {
                    console.log('🔍 DEBUG: Partner type changed:', {
                        newValue: partnerTypeField.value,
                        isValid: partnerTypeField.checkValidity(),
                        validationMessage: partnerTypeField.validationMessage
                    });
                });
            } else {
                console.error('❌ DEBUG: Partner type field not found!');
            }

            console.log('✅ DEBUG: Form debugging setup complete');
        }
    }
}

// Additional debugging - check if page loads correctly
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DEBUG: DOM Content Loaded');
    console.log('🔍 DEBUG: Current URL:', window.location.href);
    console.log('🔍 DEBUG: Form action URL:', document.getElementById('b2bCreateForm')?.action);
    
    // Check for any JavaScript errors
    window.addEventListener('error', function(e) {
        console.error('💥 DEBUG: JavaScript Error:', {
            message: e.message,
            filename: e.filename,
            lineno: e.lineno,
            colno: e.colno,
            error: e.error
        });
    });

    // Check for network errors
    window.addEventListener('unhandledrejection', function(e) {
        console.error('🌐 DEBUG: Unhandled Promise Rejection:', e.reason);
    });
});
</script>
@endpush