<div>
    <div class="mb-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-2xl font-bold text-white">Update Site</h3>
            <button type="button" @click="$dispatch('close-modal')"
                class="cursor-pointer text-gray-400 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <p class="text-gray-400 text-sm">Update the display name for {{ $site->url }}</p>
    </div>

    <form wire:submit="update" class="space-y-5">
        <div>
            <label class="block text-sm font-semibold text-gray-200 mb-2">Site Name</label>
            <input wire:model="name" type="text"
                class="block w-full rounded-lg bg-gray-900/50 border border-gray-600 text-white px-4 py-3 focus:ring-2 focus:ring-indigo-500">
            @error('name') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex gap-3 pt-4">
            <button type="button" @click="$dispatch('close-modal')"
                class="cursor-pointer flex-1 px-4 py-3 bg-gray-700 text-white rounded-lg font-semibold hover:bg-gray-600 transition-colorsg">Cancel</button>
            <button type="submit"
                class="cursor-pointer flex-1 px-4 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl">Update
                Site</button>
        </div>
    </form>
</div>