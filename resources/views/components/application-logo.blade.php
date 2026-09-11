@props(['class' => 'h-10 w-auto'])

<div class="flex items-center justify-center">
    <!-- Light Mode Logo -->
    <img src="{{ asset('crew-logo.png') }}" alt="Crew - People, connected." class="{{ $class }} object-contain dark:hidden">
    <!-- Dark Mode Logo -->
    <img src="{{ asset('crew-logo-dark.png') }}" alt="Crew - People, connected." class="{{ $class }} object-contain hidden dark:block">
</div>

