@extends('layouts.mobile')

@section('title', 'Properties - Hospitality Manager')
@section('page-title', 'Properties')

@section('content')
    <div class="space-y-4 sm:space-y-6">
        <!-- Header with Add Button -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <h2 class="heading-1">Your Properties</h2>
            <a href="{{ route('properties.create') }}" class="btn-primary text-center">
                <svg class="h-4 w-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New Property
            </a>
        </div>

        @if($properties->isEmpty())
            <!-- Empty State -->
            <div class="card-action text-center">
                <svg class="h-12 w-12 sm:h-16 sm:w-16 mx-auto mb-4 text-white opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H3m2 0h4M9 7h6m-6 4h6m-6 4h6"></path>
                </svg>
                <h3 class="heading-2 spacer-sm">No Properties Yet</h3>
                <p class="body-text spacer-md">Create your first property to get started with managing your hospitality business.</p>
                <a href="{{ route('properties.create') }}" class="btn-secondary">
                    Create First Property
                </a>
            </div>
        @else
            <!-- Properties Grid -->
            <div class="space-y-4">
                @foreach($properties as $property)
                    <div class="glass-card overflow-hidden">
                        @php
                            $mainImage = $property->photos()->where('is_main', true)->first();
                        @endphp
                        @if($mainImage && file_exists(public_path($mainImage->file_path)))
                            <div class="relative h-32 sm:h-48 w-full">
                                <img src="{{ asset($mainImage->file_path) }}" 
                                     alt="{{ $property->name }}" 
                                     class="w-full h-full object-cover"
                                >
                            </div>
                        @elseif($mainImage)
                            <div class="relative h-32 sm:h-48 w-full bg-gray-200 flex items-center justify-center">
                                <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        <div class="p-4 sm:p-6">
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-3 mb-3">
                                <div class="flex-1">
                                    <h3 class="heading-3">{{ $property->name }}</h3>
                                    <p class="small-text text-accent">{{ $property->category->name ?? 'N/A' }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('properties.edit', $property) }}" class="btn-icon">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                        </svg>
                                    </a>
                                    <span class="@if($property->status === 'pending') status-pending
                                        @elseif($property->status === 'active') status-active
                                        @else status-inactive @endif">
                                        {{ ucfirst($property->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($property->description)
                                <div class="glass-card p-3 mb-4">
                                    <p class="small-text text-secondary">{{ $property->description }}</p>
                                </div>
                            @endif
                            
                            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 text-xs text-secondary">
                                <span>Created {{ $property->created_at->format('M d, Y') }}</span>
                                @if($property->approved_at)
                                    <span>Approved {{ $property->approved_at->format('M d, Y') }}</span>
                                @endif
                            </div>
                            
                            @if($property->status === 'active')
                                <div class="divider"></div>
                                <div class="grid grid-cols-2 gap-2">
                                    <a href="{{ route('properties.edit', $property) }}#accommodations" class="btn-secondary text-xs py-2 text-center">
                                        Accommodations
                                    </a>
                                    <a href="{{ route('bookings.index') }}?property={{ $property->id }}" class="btn-secondary text-xs py-2 text-center">
                                        Bookings
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection