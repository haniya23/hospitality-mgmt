@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Staff Management Templates</h1>
        <p class="text-gray-600">Choose a predefined template that suits your property structure</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid md:grid-cols-2 gap-6">
        @foreach($templates as $key => $template)
        <div class="bg-white rounded-lg shadow-md border border-gray-200">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $template['name'] }}</h3>
                    <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                        {{ ucfirst(str_replace('_', ' ', $template['type'])) }}
                    </span>
                </div>
                
                <p class="text-gray-600 mb-4">{{ $template['description'] }}</p>
                
                <div class="mb-4">
                    <h4 class="font-medium text-gray-900 mb-2">Hierarchy Structure:</h4>
                    <div class="bg-gray-50 p-3 rounded">
                        @include('staff.templates.tree', ['structure' => $template['structure'], 'level' => 0])
                    </div>
                </div>

                <div class="mb-4 text-sm text-gray-600">
                    @if($template['max_properties'])
                        <div>Max Properties: {{ $template['max_properties'] }}</div>
                    @endif
                    @if($template['max_accommodations'])
                        <div>Max Accommodations: {{ $template['max_accommodations'] }}</div>
                    @endif
                </div>

                <button onclick="openApplyModal('{{ $key }}', '{{ $template['name'] }}')" 
                        class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    Apply Template
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Apply Template Modal -->
<div id="applyModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <form action="{{ route('staff.templates.apply') }}" method="POST">
            @csrf
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Apply Staff Template</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Template</label>
                    <input type="text" id="templateName" readonly class="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50">
                    <input type="hidden" name="template_type" id="templateType">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Property</label>
                    <select name="property_id" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Choose a property...</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-4">
                    <p class="text-sm text-yellow-800">
                        <strong>Note:</strong> This will create staff roles and permissions based on the selected template.
                    </p>
                </div>
            </div>

            <div class="px-6 py-3 bg-gray-50 flex justify-end space-x-3">
                <button type="button" onclick="closeApplyModal()" 
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded hover:bg-gray-300 transition">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                    Apply Template
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openApplyModal(templateType, templateName) {
    document.getElementById('templateType').value = templateType;
    document.getElementById('templateName').value = templateName;
    document.getElementById('applyModal').classList.remove('hidden');
    document.getElementById('applyModal').classList.add('flex');
}

function closeApplyModal() {
    document.getElementById('applyModal').classList.add('hidden');
    document.getElementById('applyModal').classList.remove('flex');
}
</script>
@endsection