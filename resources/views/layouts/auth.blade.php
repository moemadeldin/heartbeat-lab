<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>
    <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-linear-to-br from-slate-900 via-slate-800 to-slate-900 antialiased font-sans">
    <div x-data="{ 
        show: false, 
        message: '',
        init() {
            window.addEventListener('user-registered', (event) => {
                this.message = event.detail.message;
                this.show = true;
                setTimeout(() => this.show = false, 5000);
            });
        }
    }" x-show="show" x-transition class="fixed bottom-5 right-5 bg-blue-600 text-white p-4 rounded-lg shadow-lg z-50">
        <span x-text="message"></span>
    </div>

    <x-navbar>
        <a href="{{ route('public.status') }}"
            class="text-gray-400 hover:text-white transition-colors text-sm font-medium">
            Check Status
        </a>
    </x-navbar>

    <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </div>
</body>

</html>