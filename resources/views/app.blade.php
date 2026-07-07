<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title inertia>{{ config('app.name', 'Eios CMS') }}</title>
    <meta name="description" content="Eios CMS - Hệ thống quản trị nội dung đa quốc gia chuyên nghiệp và tối ưu.">
    <meta name="robots" content="noindex, nofollow">
    <link rel="canonical" href="{{ url()->current() }}">

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>

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
        #app { display: flex; flex-direction: column; height: 100%; flex: 1 1 auto; min-height: 0; }
    </style>

    @routes
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @inertiaHead
</head>
<body class="flex h-full flex-col bg-gray-50 font-sans text-gray-900 antialiased dark:bg-gray-900 dark:text-gray-100">
    @inertia
</body>
</html>
