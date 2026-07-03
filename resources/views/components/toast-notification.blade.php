<div x-data="toastNotification()" x-init="init()" class="fixed top-4 right-4 z-50 space-y-2">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show" 
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
             x-cloak>
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i x-show="toast.type === 'success'" class="fas fa-check-circle text-green-400"></i>
                        <i x-show="toast.type === 'error'" class="fas fa-exclamation-circle text-red-400"></i>
                        <i x-show="toast.type === 'warning'" class="fas fa-exclamation-triangle text-yellow-400"></i>
                        <i x-show="toast.type === 'info'" class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900" x-text="toast.title"></p>
                        <p class="mt-1 text-sm text-gray-500" x-text="toast.message"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="removeToast(toast.id)" 
                                class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <span class="sr-only">Close</span>
                            <i class="fas fa-times"></i>
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

// Subscription upgrade modal functions removed
</script>
