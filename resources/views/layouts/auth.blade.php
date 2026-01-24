<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-linear-to-br from-slate-900 via-slate-800 to-slate-900 antialiased font-sans">
    <div x-data="{ 
        show: false, 
        name: '',
        init() {
            window.addEventListener('user-registered', (event) => {
                this.name = event.detail.name;
                this.show = true;
                setTimeout(() => this.show = false, 5000);
            });
        }
    }" x-show="show" x-transition class="fixed bottom-5 right-5 bg-blue-600 text-white p-4 rounded-lg shadow-lg z-50">
        Welcome, <span x-text="name"></span>!
    </div>

    <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </div>
</body>

</html>