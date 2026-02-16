@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-300 focus:border-brand-teal dark:focus:border-brand-teal focus:ring-brand-teal rounded-md shadow-sm']) }}>
