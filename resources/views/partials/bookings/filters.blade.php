<div class="bg-white rounded-2xl p-4 shadow-sm">
    <select x-model="selectedProperty" 
            class="w-full bg-transparent border-none text-gray-800 font-medium focus:ring-0">
        <option value="">All Properties</option>
        <template x-for="property in properties" :key="property.id">
            <option :value="property.name" x-text="property.name"></option>
        </template>
    </select>
</div>