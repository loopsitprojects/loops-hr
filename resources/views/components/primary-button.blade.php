<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brand-navy dark:bg-brand-teal border border-transparent rounded-md font-semibold text-xs text-white dark:text-slate-900 uppercase tracking-widest hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-brand-teal transition ease-in-out duration-150 shadow-md active:scale-95']) }}>
    {{ $slot }}
</button>
