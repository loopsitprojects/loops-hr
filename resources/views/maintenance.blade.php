<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Maintenance Mode - LoopsHR</title>
        <link rel="icon" href="{{ asset('loops-icon.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,900&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 dark:text-gray-100 antialiased bg-gray-50 dark:bg-slate-950 min-h-screen flex items-center justify-center p-4">
        <div class="max-w-md w-full text-center space-y-8">
            <!-- Logo -->
            <div class="flex justify-center">
                <div class="flex items-center justify-center">
                    <img src="https://ai.loopsintegrated.co/logo/LoopsBlack.png" alt="Loops Integrated" class="h-14 w-auto object-contain dark:hidden">
                    <img src="https://ai.loopsintegrated.co/logo/LoopsWhite.png" alt="Loops Integrated" class="h-14 w-auto object-contain hidden dark:block">
                </div>
            </div>

            <!-- Card -->
            <div class="bg-white dark:bg-slate-900 rounded-[2rem] p-8 shadow-xl border border-gray-100 dark:border-slate-800 space-y-6">
                <!-- Icon -->
                <div class="mx-auto w-16 h-16 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <div class="space-y-2">
                    <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                        System Under Maintenance
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        LoopsHR is currently undergoing scheduled maintenance. Please check back shortly.
                    </p>
                </div>

                <div class="pt-4 border-t border-gray-100 dark:border-slate-800 flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('login') }}" class="px-6 py-2.5 bg-brand-navy dark:bg-brand-teal text-white dark:text-slate-900 rounded-xl text-xs font-bold uppercase tracking-wider hover:opacity-90 transition-all duration-200">
                        Back to Login
                    </a>
                </div>
            </div>

            <p class="text-xs text-gray-400 dark:text-gray-600">
                &copy; {{ date('Y') }} Loops HR. All rights reserved.
            </p>
        </div>
    </body>
</html>
