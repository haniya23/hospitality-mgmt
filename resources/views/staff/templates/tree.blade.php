@foreach($structure as $key => $node)
    <div class="flex items-center mb-2" style="margin-left: {{ $level * 20 }}px;">
        @if($level > 0)
            <div class="w-4 h-px bg-gray-300 mr-2"></div>
        @endif
        
        <div class="flex items-center">
            @if($key === 'owner')
                <div class="w-3 h-3 bg-purple-500 rounded-full mr-2"></div>
            @elseif($node['role'] === 'manager')
                <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
            @elseif($node['role'] === 'supervisor')
                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
            @else
                <div class="w-3 h-3 bg-gray-400 rounded-full mr-2"></div>
            @endif
            
            <span class="text-sm font-medium text-gray-700">
                {{ ucfirst($key === 'owner' ? 'Owner' : $node['role']) }}
            </span>
            
            @if(isset($node['permissions']) && is_array($node['permissions']))
                <div class="ml-2 flex flex-wrap gap-1">
                    @foreach(array_slice($node['permissions'], 0, 2) as $permission)
                        <span class="px-1 py-0.5 text-xs bg-gray-200 text-gray-600 rounded">
                            {{ str_replace('_', ' ', $permission) }}
                        </span>
                    @endforeach
                    @if(count($node['permissions']) > 2)
                        <span class="px-1 py-0.5 text-xs bg-gray-200 text-gray-600 rounded">
                            +{{ count($node['permissions']) - 2 }}
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>
    
    @if(isset($node['children']))
        @include('staff.templates.tree', ['structure' => $node['children'], 'level' => $level + 1])
    @endif
@endforeach