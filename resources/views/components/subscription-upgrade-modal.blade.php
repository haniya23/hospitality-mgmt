<div x-data="subscriptionUpgradeModal()" x-show="show" 
     x-transition:enter="ease-out duration-300" 
     x-transition:enter-start="opacity-0" 
     x-transition:enter-end="opacity-100" 
     x-transition:leave="ease-in duration-200" 
     x-transition:leave-start="opacity-100" 
     x-transition:leave-end="opacity-0" 
     class="fixed inset-0 z-50 overflow-y-auto"
     x-cloak
     style="background-color: rgba(0,0,0,0.5);">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" @click="close()">
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" @click.stop>
            <div class="bg-white px-6 pt-6 pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-rocket text-white text-lg"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-bold text-gray-900 mb-2" x-text="modalData.title"></h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4" x-text="modalData.description"></p>
                            
                            <!-- Plan Comparison -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-4">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <div class="font-semibold text-gray-700 mb-2">Current Plan</div>
                                        <div class="text-gray-600" x-text="modalData.currentPlan"></div>
                                        <div class="text-gray-500" x-text="modalData.currentLimits"></div>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-purple-700 mb-2">New Plan</div>
                                        <div class="text-purple-600 font-semibold" x-text="modalData.newPlan"></div>
                                        <div class="text-purple-500" x-text="modalData.newLimits"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pricing -->
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-4 mb-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-purple-600" x-text="modalData.price"></div>
                                    <div class="text-sm text-gray-500" x-text="modalData.billing"></div>
                                    <div class="text-xs text-green-600 font-semibold mt-1" x-show="modalData.savings" x-text="modalData.savings"></div>
                                </div>
                            </div>
                            
                            <!-- Benefits -->
                            <div class="mb-4">
                                <div class="text-sm font-semibold text-gray-700 mb-2">What you'll get:</div>
                                <ul class="text-sm text-gray-600 space-y-1">
                                    <template x-for="benefit in modalData.benefits" :key="benefit">
                                        <li class="flex items-center">
                                            <i class="fas fa-check text-green-500 mr-2 text-xs"></i>
                                            <span x-text="benefit"></span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-6 py-4 sm:flex sm:flex-row-reverse">
                <button @click="confirmUpgrade()" 
                        :disabled="loading"
                        class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-6 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-base font-medium text-white hover:from-purple-700 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    <span x-show="!loading">
                        <i class="fas fa-credit-card mr-2"></i>
                        <span x-text="modalData.buttonText"></span>
                    </span>
                    <span x-show="loading" class="flex items-center justify-center">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Processing...
                    </span>
                </button>
                <button @click="close()" 
                        class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function subscriptionUpgradeModal() {
    return {
        show: false,
        loading: false,
        modalData: {
            title: '',
            description: '',
            currentPlan: '',
            currentLimits: '',
            newPlan: '',
            newLimits: '',
            price: '',
            billing: '',
            savings: '',
            benefits: [],
            buttonText: 'Upgrade Now',
            plan: '',
            onConfirm: null
        },
        
        init() {
            // Listen for upgrade modal events
            window.addEventListener('show-upgrade-modal', (event) => {
                this.showModal(event.detail);
            });
        },
        
        showModal(data) {
            this.modalData = { ...this.modalData, ...data };
            this.show = true;
        },
        
        close() {
            this.show = false;
            this.loading = false;
        },
        
        async confirmUpgrade() {
            this.loading = true;
            
            try {
                if (this.modalData.onConfirm) {
                    await this.modalData.onConfirm();
                } else {
                    // Default upgrade action
                    await this.defaultUpgrade();
                }
            } catch (error) {
                console.error('Upgrade error:', error);
                showError('Upgrade Failed', 'Something went wrong. Please try again.');
            } finally {
                this.loading = false;
            }
        },
        
        async defaultUpgrade() {
            const response = await fetch('/api/subscription/create-order', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    plan: this.modalData.plan,
                    billing_interval: 'month',
                    quantity: 1
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.payment_url) {
                window.location.href = data.payment_url;
            } else {
                throw new Error(data.message || 'Failed to create payment order');
            }
        }
    }
}
</script>
