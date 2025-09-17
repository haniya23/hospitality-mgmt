<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="p-4 border-b border-gray-100">
        <h3 class="text-lg font-bold text-gray-800">Pricing Rules</h3>
    </div>
    <div class="divide-y divide-gray-100">
        <template x-for="rule in rules" :key="rule.id">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-semibold text-gray-800" x-text="rule.name"></h4>
                        <p class="text-sm text-gray-500" x-text="rule.type"></p>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold text-gray-800" x-text="rule.multiplier + 'x'"></div>
                        <span class="px-3 py-1 rounded-full text-xs font-medium"
                              :class="rule.status === 'active' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-600'"
                              x-text="rule.status"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>