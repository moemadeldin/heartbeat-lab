<div>
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-2xl font-bold text-white">Delete Site</h3>
            <button type="button" @click="$dispatch('close-modal')"
                class="cursor-pointer text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <p class="text-gray-400 text-sm">Are you sure you want to delete <strong
                class="text-white">{{ $site->name }}</strong>?</p>
        <p class="text-red-400 text-sm mt-2">⚠️ This action cannot be undone.</p>
    </div>

    <div class="bg-gray-900/30 border border-gray-700 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center">
                <span class="text-lg">🌐</span>
            </div>
            <div>
                <div class="text-white font-medium">{{ $site->name }}</div>
                <div class="text-gray-500 text-xs">{{ $site->url }}</div>
            </div>
        </div>
    </div>

    <div class="flex gap-3">
        <button type="button" @click="$dispatch('close-modal')"
            class="cursor-pointer flex-1 px-4 py-3 bg-gray-700 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colors">
            Cancel
        </button>
        <button type="button" wire:click="delete"
            class="cursor-pointer flex-1 px-4 py-3 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors shadow-lg">
            Delete Site
        </button>
    </div>
</div>