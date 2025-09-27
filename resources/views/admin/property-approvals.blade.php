@extends('layouts.app')

@section('title', 'Property Approvals - Admin')
@section('page-title', 'Property Approvals')

@section('content')

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Property Approvals</h2>
                <p class="text-sm text-gray-600">{{ $pendingProperties->total() }} properties pending approval</p>
            </div>
            <a href="{{ route('admin.create-property') }}" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-2 rounded-xl text-sm font-medium hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                Create Property
            </a>
        </div>

        <!-- Pending Properties -->
        <div class="bg-white bg-opacity-80 backdrop-blur-md rounded-2xl shadow-lg overflow-hidden">
            @if($pendingProperties->isEmpty())
                <div class="px-6 py-8 text-center text-gray-500">
                    <svg class="h-12 w-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p>No pending properties for approval.</p>
                </div>
            @else
                <div class="divide-y divide-gray-200">
                    @foreach($pendingProperties as $property)
                        <div class="px-6 py-4">
                            <div class="space-y-3">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900">{{ $property->name }}</h4>
                                        <p class="text-sm text-gray-600">{{ $property->category->name }}</p>
                                        <p class="text-sm text-gray-600">Owner: {{ $property->owner->name }} • {{ $property->owner->mobile_number }}</p>
                                        @if($property->location)
                                            <p class="text-xs text-gray-500">{{ $property->location->city->name }}, {{ $property->location->city->district->name }}</p>
                                        @endif
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                                        Pending
                                    </span>
                                </div>
                                
                                @if($property->description)
                                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded-lg">{{ $property->description }}</p>
                                @endif
                                
                                <p class="text-xs text-gray-500">Submitted: {{ $property->created_at->format('M d, Y H:i') }}</p>
                                
                                <div class="flex space-x-3">
                                    <form method="POST" action="{{ route('admin.properties.approve', $property) }}" class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="w-full bg-gradient-to-r from-green-600 to-emerald-600 text-white py-2 rounded-xl text-sm font-medium hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg">
                                            Approve
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.properties.reject', $property) }}" class="flex-1" onsubmit="return handleReject(event, this)">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="reason" id="reject-reason-{{ $property->id }}">
                                        <button type="submit" class="w-full bg-gradient-to-r from-red-600 to-pink-600 text-white py-2 rounded-xl text-sm font-medium hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-lg">
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($pendingProperties->hasPages())
            <div class="flex justify-center">
                {{ $pendingProperties->links() }}
            </div>
        @endif
    </div>

    <script>
        function handleReject(event, form) {
            event.preventDefault();
            const propertyId = form.querySelector('input[name="reason"]').id.split('-').pop();
            const reason = prompt('Please provide a reason for rejection:');
            if (reason && reason.trim()) {
                form.querySelector('input[name="reason"]').value = reason.trim();
                form.submit();
            }
            return false;
        }
    </script>
@endsection