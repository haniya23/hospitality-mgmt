@extends('layouts.app')

@section('title', 'Setup Your Stay loops Account')

@section('content')
<div x-data="onboardingWizard()" x-init="init()" class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 py-8">
    <div class="max-w-4xl mx-auto px-4">
        <!-- Progress Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-home text-white text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome to Stay loops!</h1>
            <p class="text-gray-600">Let's set up your first property</p>
            
        <!-- Progress Steps -->
        <div class="flex justify-center mt-6 space-x-4">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                     :class="currentStep >= 1 ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500'">
                    1
                </div>
                <span class="ml-2 text-sm font-medium" :class="currentStep >= 1 ? 'text-green-600' : 'text-gray-500'">Property</span>
            </div>
            <div class="w-8 h-px bg-gray-300"></div>
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                     :class="currentStep >= 2 ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-500'">
                    2
                </div>
                <span class="ml-2 text-sm font-medium" :class="currentStep >= 2 ? 'text-green-600' : 'text-gray-500'">Complete</span>
            </div>
        </div>
        </div>

        <!-- Step 1: Property Creation -->
        <div x-show="currentStep === 1" class="bg-white rounded-2xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Add Your First Property</h2>
            
            <form @submit.prevent="createProperty()" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Name *</label>
                    <input type="text" x-model="propertyData.name" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                           placeholder="e.g., Mountain View Resort, City Hotel">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Type *</label>
                    <select x-model="propertyData.category_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <option value="">Select property type</option>
                        @foreach($propertyCategories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea x-model="propertyData.description" rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500"
                              placeholder="Describe your property..."></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white font-semibold rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                        Create Property
                    </button>
                </div>
            </form>
        </div>

        <!-- Step 2: Completion -->
        <div x-show="currentStep === 2" class="bg-white rounded-2xl shadow-lg p-8 text-center">
            <div class="w-20 h-20 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check text-white text-3xl"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Setup Complete!</h2>
            <p class="text-gray-600 mb-8">Your property has been created successfully. You're now ready to start taking bookings!</p>
            
            <div class="bg-green-50 border border-green-200 rounded-xl p-6 mb-8">
                <h3 class="text-lg font-semibold text-green-800 mb-2">What's Next?</h3>
                <ul class="text-left text-green-700 space-y-2">
                    <li>• Add accommodations to your property</li>
                    <li>• Set up pricing rules and availability</li>
                    <li>• Start accepting bookings from guests</li>
                    <li>• Manage your bookings from the dashboard</li>
                </ul>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <button @click="addAccommodation()" 
                        class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-plus mr-2"></i>Add Accommodation
                </button>
                <button @click="completeSetup()" 
                        class="px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white font-semibold rounded-xl hover:from-green-600 hover:to-emerald-600 transition-all duration-200 shadow-lg hover:shadow-xl">
                    Go to Booking Dashboard
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function onboardingWizard() {
    return {
        currentStep: 1,
        createdProperty: null,
        propertyData: {
            name: '',
            category_id: '',
            description: ''
        },

        init() {
            console.log('Onboarding wizard initialized');
        },

        async createProperty() {
            console.log('Creating property with data:', this.propertyData);
            console.log('CSRF Token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            
            try {
                const response = await fetch('/api/properties', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        name: this.propertyData.name,
                        property_category_id: this.propertyData.category_id,
                        description: this.propertyData.description
                    })
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                if (response.ok) {
                    const responseText = await response.text();
                    console.log('Response text:', responseText);
                    
                    try {
                        const property = JSON.parse(responseText);
                        console.log('Parsed property:', property);
                        this.createdProperty = property;
                        this.currentStep = 2;
                    } catch (parseError) {
                        console.error('JSON parse error:', parseError);
                        alert('Error parsing response: ' + parseError.message);
                    }
                } else {
                    let errorMessage = 'Unknown error';
                    try {
                        const error = await response.json();
                        errorMessage = error.message || error.error || JSON.stringify(error);
                    } catch (e) {
                        const text = await response.text();
                        errorMessage = `HTTP ${response.status}: ${text.substring(0, 200)}`;
                    }
                    console.error('Property creation failed:', response.status, errorMessage);
                    alert('Error creating property: ' + errorMessage);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error creating property. Please try again.');
            }
        },


        completeSetup() {
            window.location.href = '/booking-dashboard';
        },

        addAccommodation() {
            // Redirect to property edit page to add accommodation
            window.location.href = `/properties/${this.createdProperty.uuid}/edit`;
        }
    }
}
</script>
@endsection
