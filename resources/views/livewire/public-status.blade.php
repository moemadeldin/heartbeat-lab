<div class="min-h-screen bg-linear-to-br from-gray-900 via-gray-800 to-gray-900">
    <nav class="bg-gray-800/50 backdrop-blur-sm shadow-lg border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-8 py-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 bg-linear-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center text-2xl shadow-lg">
                    💓
                </div>
                <h1 class="text-2xl font-bold text-white">Heartbeat Lab</h1>
            </div>

            <div class="flex items-center gap-6">
                @auth
                    <a href="{{ route('dashboard') }}"
                        class="text-gray-400 hover:text-white transition-colors text-sm font-medium">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}"
                        class="text-gray-400 hover:text-white transition-colors text-sm font-medium">
                        Register
                    </a>
                    <a href="{{ route('login') }}"
                        class="text-gray-400 hover:text-white transition-colors text-sm font-medium">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-2xl mx-auto py-24 px-8 text-center">
        <div
            class="w-20 h-20 bg-indigo-500/10 rounded-full flex items-center justify-center mx-auto mb-8 border border-indigo-500/20">
            <span class="text-4xl">🔍</span>
        </div>

        <h2 class="text-4xl font-bold text-white mb-4">Check Site Status</h2>
        <p class="text-gray-400 text-lg mb-12 max-w-lg mx-auto">
            Enter any website URL to check its current status in real-time.
        </p>

        <form wire:submit="search" class="flex flex-col sm:flex-row gap-4 max-w-xl mx-auto">
            <div class="flex-1">
                <input
                    wire:model="url"
                    type="url"
                    placeholder="https://example.com"
                    class="w-full px-6 py-4 bg-gray-800/70 border border-gray-700 rounded-xl text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 text-lg transition-all">

                @error('url')
                    <p class="mt-3 text-red-400 text-sm text-left">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="px-8 py-4 bg-indigo-600 text-white rounded-xl font-semibold hover:bg-indigo-700 transition-all shadow-lg hover:shadow-indigo-500/20 text-lg whitespace-nowrap cursor-pointer">
                Check Status
            </button>
        </form>

        <p class="mt-16 text-gray-500 text-sm">
            Public status check — no account required.
        </p>
    </main>
</div>
