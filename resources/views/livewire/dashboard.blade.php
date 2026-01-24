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
                            class="text-indigo-400 font-semibold text-sm">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                    <span class="text-gray-200 font-medium">{{ auth()->user()->name }}</span>
                </div>
                <livewire:auth.logout />
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-12 px-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800/50 backdrop-blur-sm p-6 rounded-xl shadow-lg border border-gray-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Total Sites</p>
                        <p class="text-3xl font-bold text-white mt-1">0</p>
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
                        <p class="text-3xl font-bold text-green-400 mt-1">0</p>
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
                        <p class="text-3xl font-bold text-red-400 mt-1">0</p>
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
                        <p class="text-3xl font-bold text-white mt-1">--</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-purple-500/20 rounded-lg flex items-center justify-center border border-purple-500/30">
                        <span class="text-2xl">📊</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg border border-gray-700">
            <div class="p-8 border-b border-gray-700">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-white">Monitored Websites</h2>
                        <p class="text-gray-400 mt-1">Manage and track all your website monitors</p>
                    </div>
                    <button
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl flex items-center gap-2">
                        <span class="text-xl">+</span>
                        Add New Site
                    </button>
                </div>
            </div>

            <div class="p-16">
                <div class="text-center max-w-md mx-auto">
                    <div
                        class="w-24 h-24 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-600">
                        <span class="text-5xl">🌍</span>
                    </div>
                    <h3 class="text-xl font-semibold text-white mb-2">No websites monitored yet</h3>
                    <p class="text-gray-400 mb-8">Get started by adding your first website to monitor its uptime and
                        performance.</p>
                    <button
                        class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-colors inline-flex items-center gap-2 shadow-lg hover:shadow-xl">
                        <span class="text-xl">+</span>
                        Add Your First Site
                    </button>
                </div>
            </div>
        </div>
    </main>
</div>