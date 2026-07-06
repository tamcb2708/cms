<!doctype html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO Best Practices -->
    <title>@yield('title', 'Dashboard') — {{ config('app.name', 'Eios CMS') }}</title>
    <meta name="description" content="@yield('meta_description', 'Eios CMS - Hệ thống quản trị nội dung đa quốc gia chuyên nghiệp và tối ưu.')">
    <meta name="robots" content="@yield('meta_robots', 'noindex, nofollow')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Dashboard') — {{ config('app.name', 'Eios CMS') }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name', 'Eios CMS') }}">

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/vn.js"></script>
    <style>
        body { background-color: #f8fafc !important; }
        .dark body { background-color: #000000 !important; }
        .dark .dark\:bg-gray-900 { background-color: #0a0a0a !important; }
        .dark .dark\:bg-gray-800 { background-color: #121212 !important; }
        .dark .dark\:bg-gray-800\/50 { background-color: rgba(18, 18, 18, 0.5) !important; }
        .dark .dark\:bg-gray-800\/80 { background-color: rgba(18, 18, 18, 0.8) !important; }
        .dark .dark\:bg-gray-700 { background-color: #1a1a1a !important; }
        .dark .dark\:border-gray-800 { border-color: rgba(255, 255, 255, 0.1) !important; }
        .dark .dark\:border-gray-700 { border-color: rgba(255, 255, 255, 0.1) !important; }
        .dark .dark\:divide-gray-800 > :not([hidden]) ~ :not([hidden]) { border-color: rgba(255, 255, 255, 0.1) !important; }
        .dark .dark\:divide-gray-700 > :not([hidden]) ~ :not([hidden]) { border-color: rgba(255, 255, 255, 0.1) !important; }
    </style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 font-sans text-gray-900 antialiased dark:text-gray-100 flex flex-col">
    <div class="flex h-full flex-1 overflow-hidden">
        @include('layouts.partials.sidebar')

        <div class="flex min-w-0 flex-1 flex-col">
            @include('layouts.partials.topbar')

            <main class="flex-1 overflow-y-auto px-6 py-6 sm:px-8">
                @yield('content')
            </main>
        </div>
    </div>

    @include('layouts.partials.toast')

    @stack('scripts')
</body>
</html>
