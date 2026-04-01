<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-2xl font-bold text-white">Add New Site</h3>
            <button type="button" @click="$dispatch('close-modal')"
                class="cursor-pointer text-gray-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <p class="text-gray-400 text-sm">Start monitoring a new website's uptime</p>
    </div>

    <!-- Form -->
    <form wire:submit="store" class="space-y-5">
        <!-- Site Name -->
        <div>
            <label class="block text-sm font-semibold text-gray-200 mb-2">Site Name</label>
            <input wire:model.defer="name" type="text" placeholder="My Awesome Site"
                class="block w-full rounded-lg bg-gray-900/50 border border-gray-600 text-white px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-500">
            @error('name')
                <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Site URL -->
        <div>
            <label class="block text-sm font-semibold text-gray-200 mb-2">Website URL</label>
            <input wire:model.defer="url" type="url" placeholder="https://example.com"
                class="block w-full rounded-lg bg-gray-900/50 border border-gray-600 text-white px-4 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all placeholder-gray-500">
            @error('url')
                <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Buttons -->
        <div class="flex gap-3 pt-4">
            <button type="button" @click="$dispatch('close-modal')"
                class="cursor-pointer flex-1 px-4 py-3 bg-gray-700 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors">
                Cancel
            </button>
            <button type="submit"
                class="cursor-pointer flex-1 px-4 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl">
                Add Site
            </button>
        </div>
    </form>
</div>