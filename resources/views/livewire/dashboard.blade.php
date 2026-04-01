<div class="min-h-screen bg-linear-to-br from-gray-900 via-gray-800 to-gray-900">
    <nav class="bg-gray-800/50 backdrop-blur-sm shadow-lg border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-8 py-5 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-linear-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center text-2xl shadow-lg">
                    💓
                </div>
                <h1 class="text-2xl font-bold text-white">Heartbeat Lab</h1>
            </div>

            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 bg-indigo-500/20 rounded-full flex items-center justify-center border border-indigo-500/30">
                        <span
                            class="text-indigo-400 font-semibold text-sm">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    </div>
                    <span class="text-gray-200 font-medium">{{ Auth::user()->name }}</span>
                </div>
                <livewire:auth.logout />
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-12 px-8" x-data="{ showCreateModal: false }"
        @site-created.window="showCreateModal = false">

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800/50 backdrop-blur-sm p-6 rounded-xl shadow-lg border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Total Sites</p>
                        <p class="text-3xl font-bold text-white mt-1">{{ $this->stats()['total'] }}</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-blue-500/20 rounded-lg flex items-center justify-center border border-blue-500/30">
                        <span class="text-2xl">🌐</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800/50 backdrop-blur-sm p-6 rounded-xl shadow-lg border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Online</p>
                        <p class="text-3xl font-bold text-green-400 mt-1">{{ $this->stats()['online'] }}</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-green-500/20 rounded-lg flex items-center justify-center border border-green-500/30">
                        <span class="text-2xl">✅</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800/50 backdrop-blur-sm p-6 rounded-xl shadow-lg border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Offline</p>
                        <p class="text-3xl font-bold text-red-400 mt-1">{{ $this->stats()['offline'] }}</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-red-500/20 rounded-lg flex items-center justify-center border border-red-500/30">
                        <span class="text-2xl">⚠️</span>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800/50 backdrop-blur-sm p-6 rounded-xl shadow-lg border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Uptime</p>
                        <p class="text-3xl font-bold text-white mt-1">{{ number_format($this->stats()['uptime'], 2) }}%
                        </p>
                    </div>
                    <div
                        class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center border border-purple-500/30">
                        <span class="text-2xl">📊</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg border border-gray-700 overflow-hidden">
            <div class="p-6 md:p-8 border-b border-gray-700 bg-gray-800/30">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-white">Monitored Websites</h2>
                        <p class="text-gray-400 mt-1">Manage and track all your website monitors</p>
                    </div>
                    <button @click="showCreateModal = true"
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-all shadow-lg hover:shadow-indigo-500/20 flex items-center gap-2">
                        <span class="text-xl">+</span>
                        Add New Site
                    </button>
                </div>
            </div>

            <div class="min-h-100">
                @if ($this->sites()->isEmpty())
                    <div class="py-20 text-center">
                        <div
                            class="w-20 h-20 bg-gray-700/30 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-600">
                            <span class="text-4xl">🌍</span>
                        </div>
                        <h3 class="text-xl font-semibold text-white mb-2">No websites monitored yet</h3>
                        <button @click="showCreateModal = true"
                            class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors inline-flex items-center gap-2">
                            Add Your First Site
                        </button>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-700/50">
                            <thead class="bg-gray-900/20">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    <th class="px-8 py-4">Site</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Uptime</th>
                                    <th class="px-8 py-4 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700/30">
                                @foreach ($this->sites() as $site)
                                    <tr class="hover:bg-white/5 transition-colors group" wire:key="row-{{ $site->id }}">
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-4">
                                                <div
                                                    class="w-10 h-10 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center">
                                                    <span class="text-lg">🌐</span>
                                                </div>
                                                <div class="min-w-0">
                                                    <div class="text-white font-medium truncate">{{ $site->name }}</div>
                                                    <div class="text-gray-500 text-xs truncate">{{ $site->url }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold {{ $site->is_online ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full {{ $site->is_online ? 'bg-green-400 animate-pulse' : 'bg-red-400' }}"></span>
                                                {{ $site->is_online ? 'ONLINE' : 'OFFLINE' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5 text-gray-200 font-mono">
                                            {{ number_format($site->uptime, 2) }}%
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <div class="flex items-center justify-end gap-3">
                                                <button wire:click="editSite('{{ $site->id }}')"
                                                    class="text-gray-400 hover:text-white transition-colors text-sm font-medium cursor-pointer">Edit</button>

                                                <button wire:click="confirmDelete('{{ $site->id }}')"
                                                    class="text-red-400/80 hover:text-red-400 transition-colors text-sm font-medium cursor-pointer">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Create Modal -->
        <div x-show="showCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/70" @click="showCreateModal = false" x-transition.opacity></div>
            <div class="relative bg-gray-800 rounded-2xl border border-gray-700 w-full max-w-md p-8" @click.stop
                x-transition.scale.95>
                <livewire:sites.create-site />
            </div>
        </div>

        <!-- Edit Modal -->
        @if($selectedSiteId)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/70" wire:click="closeModals" x-transition.opacity></div>
                <div class="relative bg-gray-800 rounded-2xl border border-gray-700 w-full max-w-md p-8" @click.stop
                    x-transition.scale.95>
                    <livewire:sites.update-site :siteId="$selectedSiteId" :key="'edit-' . $selectedSiteId" lazy />
                </div>
            </div>
        @endif

        <!-- Delete Modal -->
        @if($deleteId)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/70" wire:click="closeModals" x-transition.opacity></div>
                <div class="relative bg-gray-800 rounded-2xl border border-gray-700 w-full max-w-md p-8" @click.stop
                    x-transition.scale.95>
                    <livewire:sites.delete-site :siteId="$deleteId" :key="'delete-' . $deleteId" lazy />
                </div>
            </div>
        @endif

    </main>
</div>