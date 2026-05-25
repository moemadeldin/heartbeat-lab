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
                <a href="{{ route('public.status') }}"
                    class="text-gray-400 hover:text-white transition-colors text-sm font-medium">
                    &larr; Check Another Site
                </a>

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

    <main class="max-w-3xl mx-auto py-16 px-8">
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg border border-gray-700 overflow-hidden">
            <div class="p-8 md:p-10">
                <div class="flex items-start justify-between mb-8">
                    <div class="flex items-center gap-5">
                        <div
                            class="w-14 h-14 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl">🌐</span>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white">{{ $site->name }}</h2>
                            <a href="{{ $site->url }}" target="_blank"
                                class="text-gray-400 hover:text-indigo-400 transition-colors text-sm break-all">
                                {{ $site->url }}
                            </a>
                        </div>
                    </div>

                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-bold flex-shrink-0 {{ $site->is_online ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                        <span
                            class="w-2 h-2 rounded-full {{ $site->is_online ? 'bg-green-400 animate-pulse' : 'bg-red-400' }}"></span>
                        {{ $site->is_online ? 'ONLINE' : 'OFFLINE' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gray-900/30 p-5 rounded-lg border border-gray-700/50">
                        <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Uptime</p>
                        <p class="text-2xl font-bold text-white font-mono">{{ number_format($site->uptime, 2) }}%</p>
                    </div>

                    <div class="bg-gray-900/30 p-5 rounded-lg border border-gray-700/50">
                        <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Response Time</p>
                        <p class="text-2xl font-bold text-white font-mono">
                            {{ $site->response_time !== null ? number_format($site->response_time, 0) . ' ms' : 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-900/30 p-5 rounded-lg border border-gray-700/50">
                        <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Status Code</p>
                        <p class="text-2xl font-bold text-white font-mono">
                            {{ $site->status_code ?? 'N/A' }}
                        </p>
                    </div>

                    <div class="bg-gray-900/30 p-5 rounded-lg border border-gray-700/50">
                        <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Last Checked</p>
                        <p class="text-2xl font-bold text-white">
                            {{ $site->last_checked_at?->diffForHumans() ?? 'Never' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('public.status') }}"
                class="text-gray-500 hover:text-white transition-colors text-sm">
                &larr; Check the status of another site
            </a>
        </div>
    </main>
</div>
