@props(['class' => 'h-16 w-auto'])

<div class="flex items-center justify-center">
    <!-- Light Mode Logo -->
    <img src="https://ai.loopsintegrated.co/logo/LoopsBlack.png" 
         onerror="this.onerror=null; this.src='{{ asset('LoopsBlack.png') }}';" 
         alt="Loops Integrated" 
         class="{{ $class }} object-contain dark:hidden">
    <!-- Dark Mode Logo -->
    <img src="https://ai.loopsintegrated.co/logo/LoopsWhite.png" 
         onerror="this.onerror=null; this.src='{{ asset('LoopsWhite.png') }}';" 
         alt="Loops Integrated" 
         class="{{ $class }} object-contain hidden dark:block">
</div>

