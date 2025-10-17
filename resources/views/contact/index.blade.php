@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-green-50 to-emerald-100 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Contact Us</h1>
            <p class="text-xl text-gray-600">Get in touch with our team for subscription plans and support</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <!-- Contact Information -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Get In Touch</h2>
                
                <!-- WhatsApp Contact -->
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fab fa-whatsapp text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">WhatsApp</h3>
                            <p class="text-gray-600">Chat with us instantly</p>
                        </div>
                    </div>
                    <a href="https://wa.me/919400960223" 
                       target="_blank"
                       class="inline-flex items-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors">
                        <i class="fab fa-whatsapp mr-2"></i>
                        Chat on WhatsApp
                    </a>
                </div>

                <!-- Phone Contact -->
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-phone text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Phone</h3>
                            <p class="text-gray-600">Call us directly</p>
                        </div>
                    </div>
                    <a href="tel:+919400960223" 
                       class="inline-flex items-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-lg transition-colors">
                        <i class="fas fa-phone mr-2"></i>
                        +91 9400960223
                    </a>
                </div>

                <!-- Business Hours -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Business Hours</h3>
                    <div class="space-y-2 text-gray-600">
                        <div class="flex justify-between">
                            <span>Monday - Friday</span>
                            <span>9:00 AM - 6:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Saturday</span>
                            <span>10:00 AM - 4:00 PM</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Sunday</span>
                            <span>Closed</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription Plans -->
            <div class="bg-white rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Subscription Plans</h2>
                
                <!-- Starter Plan -->
                <div class="border border-gray-200 rounded-lg p-6 mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Starter Plan</h3>
                    <div class="text-3xl font-bold text-green-600 mb-2">₹299<span class="text-sm text-gray-500">/month</span></div>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Up to 5 accommodations</li>
                        <li>• Basic booking management</li>
                        <li>• Customer management</li>
                        <li>• Invoice generation</li>
                    </ul>
                </div>

                <!-- Professional Plan -->
                <div class="border-2 border-green-500 rounded-lg p-6 mb-6 relative">
                    <div class="absolute -top-3 left-4 bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                        POPULAR
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Professional Plan</h3>
                    <div class="text-3xl font-bold text-green-600 mb-2">₹999<span class="text-sm text-gray-500">/month</span></div>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Up to 15 accommodations</li>
                        <li>• Advanced booking features</li>
                        <li>• B2B partner management</li>
                        <li>• Staff management</li>
                        <li>• Analytics & reports</li>
                        <li>• Priority support</li>
                    </ul>
                </div>

                <div class="text-center">
                    <p class="text-gray-600 mb-4">Contact us to discuss your requirements and get started!</p>
                    <a href="https://wa.me/919400960223?text=Hi, I'm interested in Stay Loops subscription plans" 
                       target="_blank"
                       class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold rounded-lg transition-all">
                        <i class="fab fa-whatsapp mr-2"></i>
                        Get Started Now
                    </a>
                </div>
            </div>
        </div>

        <!-- Back to Dashboard -->
        <div class="text-center mt-8">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Dashboard
            </a>
        </div>
    </div>
</div>
@endsection