@props([
    'options' => [],
    'placeholder' => 'Select an option',
    'wireModel' => null,
    'searchable' => false
])

<div>
    <div x-data="{
        open: false,
        search: '',
        selected: @entangle($wireModel).live,
        options: {{ json_encode($options) }},
        get filteredOptions() {
            if (!this.search) return this.options;
            return this.options.filter(option => 
                option.name.toLowerCase().includes(this.search.toLowerCase())
            );
        },
        selectOption(option) {
            this.selected = option.id;
            this.open = false;
            this.search = '';
        },
        getSelectedText() {
            const option = this.options.find(opt => opt.id == this.selected);
            return option ? option.name : '{{ $placeholder }}';
        }
    }" class="relative">
        
        <button 
            type="button"
            @click="open = !open"
            @click.away="open = false"
            class="w-full border border-gray-200 rounded-xl shadow-sm py-3 px-4 text-left text-gray-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 bg-white hover:border-gray-300"
            :class="{ 'ring-2 ring-emerald-500 border-transparent': open }"
        >
            <div class="flex items-center justify-between">
                <span x-text="getSelectedText()" :class="{ 'text-gray-400': !selected }"></span>
                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </div>
        </button>

        <div 
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-xl shadow-lg max-h-60 overflow-hidden"
            style="display: none;"
        >
            @if($searchable)
            <div class="p-3 border-b border-gray-100">
                <input 
                    type="text"
                    x-model="search"
                    placeholder="Search..."
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                >
            </div>
            @endif

            <div class="max-h-48 overflow-y-auto">
                <template x-for="option in filteredOptions" :key="option.id">
                    <button
                        type="button"
                        @click="selectOption(option)"
                        class="w-full px-4 py-3 text-left text-sm hover:bg-emerald-50 hover:text-emerald-700 transition-colors duration-150 flex items-center justify-between"
                        :class="{ 'bg-emerald-100 text-emerald-800 font-medium': selected == option.id }"
                    >
                        <span x-text="option.name"></span>
                        <svg x-show="selected == option.id" class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </template>
                
                <div x-show="filteredOptions.length === 0" class="px-4 py-3 text-sm text-gray-500 text-center">
                    No options found
                </div>
            </div>
        </div>
    </div>
</div>