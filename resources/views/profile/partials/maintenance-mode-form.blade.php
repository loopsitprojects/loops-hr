@php
    $isMaintenanceActive = \App\Services\MaintenanceService::isEnabled();
@endphp

<section class="space-y-6">
    <header>
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                {{ __('System Maintenance Mode') }}
            </h2>
            <span class="px-3 py-1 text-xs font-black uppercase tracking-wider rounded-full {{ $isMaintenanceActive ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-400 border border-amber-300 dark:border-amber-700' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-700' }}">
                {{ $isMaintenanceActive ? __('Maintenance Active') : __('System Normal') }}
            </span>
        </div>

        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('When maintenance mode is enabled, non-super admin users cannot log in or access the application. Only Super Admins can log in and manage the platform.') }}
        </p>
    </header>

    <div class="p-4 rounded-xl {{ $isMaintenanceActive ? 'bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50' : 'bg-gray-50 dark:bg-slate-800/50 border border-gray-100 dark:border-slate-800' }}">
        <form method="POST" action="{{ route('maintenance.toggle') }}" onsubmit="return confirm('{{ $isMaintenanceActive ? 'Are you sure you want to DISABLE maintenance mode?' : 'Are you sure you want to ENABLE maintenance mode? Non-super admins will be blocked.' }}')">
            @csrf
            
            <div class="flex items-center justify-between">
                <div>
                    <span class="block text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $isMaintenanceActive ? __('Disable Maintenance Mode') : __('Enable Maintenance Mode') }}
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $isMaintenanceActive ? __('Restore public & staff login access') : __('Restrict access exclusively to Super Admin accounts') }}
                    </span>
                </div>

                <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider transition-all duration-300 shadow-sm active:scale-95 text-white {{ $isMaintenanceActive ? 'bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600' : 'bg-amber-600 hover:bg-amber-700 dark:bg-amber-500 dark:hover:bg-amber-600' }}">
                    {{ $isMaintenanceActive ? __('Disable Maintenance') : __('Enable Maintenance') }}
                </button>
            </div>
        </form>
    </div>
</section>
