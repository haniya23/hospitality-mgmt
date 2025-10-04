<div x-data="toastNotification()" x-init="init()" class="fixed top-4 right-4 left-4 sm:left-auto z-50 space-y-2">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show" 
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden mx-auto sm:mx-0"
             x-cloak>
            <div class="p-3 sm:p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i x-show="toast.type === 'success'" class="fas fa-check-circle text-green-400 text-lg sm:text-xl"></i>
                        <i x-show="toast.type === 'error'" class="fas fa-exclamation-circle text-red-400 text-lg sm:text-xl"></i>
                        <i x-show="toast.type === 'warning'" class="fas fa-exclamation-triangle text-yellow-400 text-lg sm:text-xl"></i>
                        <i x-show="toast.type === 'info'" class="fas fa-info-circle text-blue-400 text-lg sm:text-xl"></i>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm sm:text-base font-medium text-gray-900" x-text="toast.title"></p>
                        <p class="mt-1 text-xs sm:text-sm text-gray-500" x-text="toast.message"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="removeToast(toast.id)" 
                                class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 p-1 sm:p-2">
                            <span class="sr-only">Close</span>
                            <i class="fas fa-times text-sm sm:text-base"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
function toastNotification() {
    return {
        toasts: [],
        nextId: 1,
        
        init() {
            // Listen for custom toast events
            window.addEventListener('toast', (event) => {
                this.show(event.detail.type, event.detail.title, event.detail.message, event.detail.duration);
            });
        },
        
        show(type = 'info', title = '', message = '', duration = 5000) {
            const toast = {
                id: this.nextId++,
                type: type,
                title: title,
                message: message,
                show: true
            };
            
            this.toasts.push(toast);
            
            // Auto remove after duration
            setTimeout(() => {
                this.removeToast(toast.id);
            }, duration);
        },
        
        removeToast(id) {
            const index = this.toasts.findIndex(toast => toast.id === id);
            if (index > -1) {
                this.toasts[index].show = false;
                setTimeout(() => {
                    this.toasts.splice(index, 1);
                }, 100);
            }
        }
    }
}

// Global toast functions
window.showToast = function(type, title, message, duration) {
    window.dispatchEvent(new CustomEvent('toast', {
        detail: { type, title, message, duration }
    }));
};

window.showSuccess = function(title, message, duration) {
    window.showToast('success', title, message, duration);
};

window.showError = function(title, message, duration) {
    window.showToast('error', title, message, duration);
};

window.showWarning = function(title, message, duration) {
    window.showToast('warning', title, message, duration);
};

window.showInfo = function(title, message, duration) {
    window.showToast('info', title, message, duration);
};

// Subscription upgrade modal functions
window.showUpgradeModal = function() {
    window.dispatchEvent(new CustomEvent('show-upgrade-modal', {
        detail: {
            title: 'Upgrade to Professional',
            description: 'Unlock more properties and advanced features for your growing business.',
            currentPlan: 'Starter Plan',
            currentLimits: '1 property, 3 accommodations',
            newPlan: 'Professional Plan',
            newLimits: '5 properties, 15 accommodations',
            price: '₹999',
            billing: 'per month',
            savings: 'Save ₹8,000/year',
            benefits: [
                'Up to 5 properties (vs 1 current)',
                '15 accommodations (vs 3 current)',
                'B2B partner management',
                'Advanced analytics & reports',
                'Priority support',
                'New features early access'
            ],
            buttonText: 'Upgrade to Professional',
            plan: 'professional'
        }
    }));
};

window.showAddAccommodationsModal = function() {
    window.dispatchEvent(new CustomEvent('show-upgrade-modal', {
        detail: {
            title: 'Add More Accommodations',
            description: 'Need more accommodation slots? Add them instantly to your Professional plan.',
            currentPlan: 'Professional Plan',
            currentLimits: '{{ auth()->user()->getUsagePercentage()['accommodations']['used'] }} / {{ auth()->user()->getUsagePercentage()['accommodations']['max'] }} accommodations used',
            newPlan: 'Professional Plan + Add-ons',
            newLimits: 'Add up to 50 more accommodations',
            price: '₹99',
            billing: 'per accommodation per month',
            savings: 'Billed monthly with your subscription',
            benefits: [
                'Additional accommodation slots',
                'Same features as your plan',
                'Instant activation',
                'Cancel anytime',
                'Billed monthly with subscription'
            ],
            buttonText: 'Add Accommodations',
            plan: 'additional_accommodation',
            onConfirm: async function() {
                // This will be handled by the subscription plans page
                window.location.href = '{{ route("subscription.plans") }}';
            }
        }
    }));
};
</script>
