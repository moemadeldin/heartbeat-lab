@props(['headingLink' => route('public.status')])

<nav class="bg-gray-800/50 backdrop-blur-sm shadow-lg border-b border-gray-700">
    <div class="max-w-7xl mx-auto px-8 py-5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-linear-to-br from-indigo-500 to-indigo-600 rounded-lg flex items-center justify-center text-2xl shadow-lg">
                💓
            </div>
            <h1 class="text-2xl font-bold text-white">
                <a href="{{ $headingLink }}">Heartbeat Lab</a>
            </h1>
        </div>

        <div class="flex items-center gap-6">
            {{ $slot }}
        </div>
    </div>
</nav>
