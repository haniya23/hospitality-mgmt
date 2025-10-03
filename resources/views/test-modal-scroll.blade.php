@extends('layouts.app')

@section('title', 'Modal Scroll Lock Test')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Modal Scroll Lock Test</h1>
    
    <div class="space-y-4">
        <button @click="showAlpineModal = true" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Test Alpine.js Modal (with scroll lock)
        </button>
        
        <button onclick="openTestModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            Test Vanilla JS Modal (with scroll lock)
        </button>
        
        <button onclick="openModalWithoutScrollLock()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
            Test Modal WITHOUT Scroll Lock (for comparison)
        </button>
    </div>
    
    <!-- Test content to make page scrollable -->
    <div class="mt-12 space-y-8">
        @for($i = 1; $i <= 20; $i++)
        <div class="bg-gray-100 p-8 rounded-lg">
            <h2 class="text-xl font-bold mb-4">Test Section {{ $i }}</h2>
            <p class="text-gray-600">
                This is test content to make the page scrollable. 
                When you open a modal, the background should not scroll.
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
            </p>
            <p class="text-gray-600 mt-4">
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
                nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in 
                reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            </p>
        </div>
        @endfor
    </div>
</div>

<!-- Alpine.js Modal with Scroll Lock -->
<div x-data="{ showAlpineModal: false, ...simpleModalScrollLock() }" 
     x-init="setupScrollLock('showAlpineModal')"
     x-show="showAlpineModal" 
     x-on:keydown.escape.window="showAlpineModal = false"
     style="display: none; z-index: 99999 !important;"
     class="fixed inset-0 overflow-y-auto backdrop-blur-sm bg-black/40">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-md mx-auto bg-white rounded-2xl shadow-2xl p-6">
            <h3 class="text-lg font-bold mb-4">Alpine.js Modal with Scroll Lock</h3>
            <p class="text-gray-600 mb-6">
                This modal should prevent background scrolling. 
                Try scrolling while this modal is open.
            </p>
            <button @click="showAlpineModal = false" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Close Modal
            </button>
        </div>
    </div>
</div>

<!-- Vanilla JS Modal with Scroll Lock -->
<div id="testModal" class="fixed inset-0 overflow-y-auto backdrop-blur-sm bg-black/40 hidden" 
     style="z-index: 99999 !important;" 
     x-data x-init="$el.addEventListener('show', () => lockBodyScroll()); $el.addEventListener('hide', () => unlockBodyScroll());">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-md mx-auto bg-white rounded-2xl shadow-2xl p-6">
            <h3 class="text-lg font-bold mb-4">Vanilla JS Modal with Scroll Lock</h3>
            <p class="text-gray-600 mb-6">
                This modal should also prevent background scrolling. 
                Try scrolling while this modal is open.
            </p>
            <button onclick="closeTestModal()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Close Modal
            </button>
        </div>
    </div>
</div>

<!-- Modal WITHOUT Scroll Lock (for comparison) -->
<div id="testModalNoLock" class="fixed inset-0 overflow-y-auto backdrop-blur-sm bg-black/40 hidden" 
     style="z-index: 99999 !important;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative w-full max-w-md mx-auto bg-white rounded-2xl shadow-2xl p-6">
            <h3 class="text-lg font-bold mb-4">Modal WITHOUT Scroll Lock</h3>
            <p class="text-gray-600 mb-6">
                This modal does NOT prevent background scrolling. 
                You should be able to scroll the background while this modal is open.
            </p>
            <button onclick="closeModalWithoutScrollLock()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Close Modal
            </button>
        </div>
    </div>
</div>

<script>
function openTestModal() {
    const modal = document.getElementById('testModal');
    modal.classList.remove('hidden');
    modal.dispatchEvent(new CustomEvent('show'));
}

function closeTestModal() {
    const modal = document.getElementById('testModal');
    modal.classList.add('hidden');
    modal.dispatchEvent(new CustomEvent('hide'));
}

function openModalWithoutScrollLock() {
    document.getElementById('testModalNoLock').classList.remove('hidden');
}

function closeModalWithoutScrollLock() {
    document.getElementById('testModalNoLock').classList.add('hidden');
}
</script>
@endsection

