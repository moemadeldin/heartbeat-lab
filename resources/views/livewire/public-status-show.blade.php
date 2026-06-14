<div class="min-h-screen bg-linear-to-br from-gray-900 via-gray-800 to-gray-900" wire:poll.2s="pollResult">
    <x-navbar>
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
    </x-navbar>

    <main class="max-w-3xl mx-auto py-16 px-8">
        @if ($loading && !$checked)
            <div class="animate-pulse">
                <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg border border-gray-700 overflow-hidden">
                    <div class="p-8 md:p-10">
                        <div class="flex items-start justify-between mb-8">
                            <div class="flex items-center gap-5">
                                <div class="w-14 h-14 rounded-xl bg-gray-700"></div>
                                <div class="space-y-3">
                                    <div class="h-6 w-48 bg-gray-700 rounded"></div>
                                    <div class="h-4 w-64 bg-gray-700 rounded"></div>
                                </div>
                            </div>
                            <div class="h-8 w-24 bg-gray-700 rounded-md"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gray-900/30 p-5 rounded-lg border border-gray-700/50">
                                <div class="h-4 w-24 bg-gray-700 rounded mb-3"></div>
                                <div class="h-8 w-20 bg-gray-700 rounded"></div>
                            </div>
                            <div class="bg-gray-900/30 p-5 rounded-lg border border-gray-700/50">
                                <div class="h-4 w-24 bg-gray-700 rounded mb-3"></div>
                                <div class="h-8 w-16 bg-gray-700 rounded"></div>
                            </div>
                            <div class="bg-gray-900/30 p-5 rounded-lg border border-gray-700/50">
                                <div class="h-4 w-24 bg-gray-700 rounded mb-3"></div>
                                <div class="h-8 w-20 bg-gray-700 rounded"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif ($checked && $error)
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg border border-red-700/50 overflow-hidden">
                <div class="p-8 md:p-10 text-center">
                    <div
                        class="w-16 h-16 bg-red-500/10 rounded-full flex items-center justify-center mx-auto mb-6 border border-red-500/20">
                        <span class="text-3xl">⚠️</span>
                    </div>
                    <h2 class="text-2xl font-bold text-white mb-2">Unable to Check Site</h2>
                    <p class="text-red-400">{{ $error }}</p>
                    <a href="{{ route('public.status') }}"
                        class="mt-6 inline-block text-indigo-400 hover:text-indigo-300 transition-colors text-sm font-medium">
                        &larr; Try another URL
                    </a>
                </div>
            </div>
        @elseif ($checked)
            <div class="bg-gray-800/50 backdrop-blur-sm rounded-xl shadow-lg border border-gray-700 overflow-hidden">
                <div class="p-8 md:p-10">
                    <div class="flex items-start justify-between mb-8">
                        <div class="flex items-center gap-5">
                            <div
                                class="w-14 h-14 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center flex-shrink-0">
                                <span class="text-2xl">🌐</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-white">{{ parse_url($url, PHP_URL_HOST) }}</h2>
                                <a href="{{ $url }}" target="_blank"
                                    class="text-gray-400 hover:text-indigo-400 transition-colors text-sm break-all">
                                    {{ $url }}
                                </a>
                            </div>
                        </div>

                        <span
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-bold flex-shrink-0 {{ $isOnline ? 'bg-green-500/10 text-green-400 border border-green-500/20' : 'bg-red-500/10 text-red-400 border border-red-500/20' }}">
                            <span
                                class="w-2 h-2 rounded-full {{ $isOnline ? 'bg-green-400 animate-pulse' : 'bg-red-400' }}"></span>
                            {{ $isOnline ? 'ONLINE' : 'OFFLINE' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="bg-gray-900/30 p-5 rounded-lg border border-gray-700/50">
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Response Time</p>
                            <p class="text-2xl font-bold text-white font-mono">
                                {{ $responseTime !== null ? number_format($responseTime, 0) . ' ms' : 'N/A' }}
                            </p>
                        </div>

                        <div class="bg-gray-900/30 p-5 rounded-lg border border-gray-700/50">
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">Status Code</p>
                            <p class="text-2xl font-bold text-white font-mono">
                                {{ $statusCode ?? 'N/A' }}
                            </p>
                        </div>

                        <div class="bg-gray-900/30 p-5 rounded-lg border border-gray-700/50">
                            <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider mb-1">SSL</p>
                            <p class="text-2xl font-bold text-white font-mono">
                                @if ($sslValid === true)
                                    <span class="text-green-400">{{ $sslDaysLeft }}d</span>
                                @elseif ($sslValid === false)
                                    <span class="text-red-400">Invalid</span>
                                @else
                                    N/A
                                @endif
                            </p>
                            @if ($sslIssuer)
                                <p class="text-gray-500 text-xs mt-1">{{ $sslIssuer }}</p>
                            @endif
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
        @endif
    </main>
</div>
