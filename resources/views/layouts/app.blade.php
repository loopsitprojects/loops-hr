<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $pageTitle = trim($__env->yieldContent('title'));
            if (empty($pageTitle)) {
                $pageTitle = config('app.name', 'Laravel');
            } else {
                $appName = config('app.name');
                if ($appName && str_ends_with($pageTitle, ' | ' . $appName)) {
                    $pageTitle = substr($pageTitle, 0, -strlen(' | ' . $appName));
                }
            }
        @endphp
        <title>{{ $pageTitle }}</title>
        <link rel="icon" href="{{ asset('loops-icon.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Flatpickr for modern datetime picker -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <link rel="stylesheet" id="flatpickr-dark" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css" disabled>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

        <script>
            const flatpickrDark = document.getElementById('flatpickr-dark');
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
                if(flatpickrDark) flatpickrDark.removeAttribute('disabled');
            } else {
                document.documentElement.classList.remove('dark');
                if(flatpickrDark) flatpickrDark.setAttribute('disabled', 'true');
            }

            // Observer to handle dynamic theme changes
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === "class") {
                        const isDark = document.documentElement.classList.contains('dark');
                        if (flatpickrDark) {
                            if (isDark) {
                                flatpickrDark.removeAttribute('disabled');
                            } else {
                                flatpickrDark.setAttribute('disabled', 'true');
                            }
                        }
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });
        </script>

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased text-brand-navy dark:text-gray-100 transition-colors duration-300">
        <div class="min-h-screen bg-surface-light dark:bg-slate-950">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-slate-900 border-b border-gray-100/50 dark:border-slate-800 transition-colors duration-300">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-6">
                    @if(session('success'))
                        <div class="session-alert mb-6 bg-emerald-50 dark:bg-emerald-500/10 border-l-4 border-emerald-400 p-4 rounded-xl shadow-premium transition-all animate-in fade-in slide-in-from-top-4 duration-500">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-[10px] font-black text-emerald-800 dark:text-emerald-400 uppercase tracking-[0.2em]">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="session-alert mb-6 bg-red-50 dark:bg-red-500/10 border-l-4 border-red-400 p-4 rounded-xl shadow-sm transition-all animate-in fade-in slide-in-from-top-4 duration-500">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-[10px] font-black text-red-800 dark:text-red-400 uppercase tracking-[0.2em]">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{ $slot }}
            </main>
        </div>
        @stack('modals')
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const alerts = document.querySelectorAll('.session-alert');
                alerts.forEach(alert => {
                    setTimeout(() => {
                        alert.style.transition = 'opacity 1s ease-out, transform 1s ease-out';
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-10px)';
                        setTimeout(() => alert.remove(), 1000);
                    }, 5000);
                });
            });
        </script>
    </body>
</html>
