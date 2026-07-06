<x-app-layout>
    @section('title', 'Loops-HR')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Loops-HR') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- HR Status Mini-Cards -->
            <div class="flex flex-wrap gap-3 mb-8">
                <!-- Total Candidates -->
                <div class="flex items-center gap-3 bg-white dark:bg-slate-900 px-4 py-3 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-md transition-all duration-200 group">
                    <div class="p-2 rounded-xl bg-orange-50 dark:bg-orange-500/20 text-orange-500 dark:text-orange-400 group-hover:bg-orange-500 group-hover:text-white transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-0.5">Total Candidates</p>
                        <p class="text-xl font-black text-brand-navy dark:text-white leading-none">{{ \App\Models\Candidate::where('is_archived', false)->count() }}</p>
                    </div>
                </div>

                <!-- Archived Candidates -->
                <div class="flex items-center gap-3 bg-white dark:bg-slate-900 px-4 py-3 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-md transition-all duration-200 group">
                    <div class="p-2 rounded-xl bg-slate-100 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 group-hover:bg-slate-500 group-hover:text-white transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-0.5">Archived</p>
                        <p class="text-xl font-black text-brand-navy dark:text-white leading-none">{{ \App\Models\Candidate::where('is_archived', true)->count() }}</p>
                    </div>
                </div>

                <!-- Active Job Roles -->
                <div class="flex items-center gap-3 bg-white dark:bg-slate-900 px-4 py-3 rounded-2xl shadow-sm border border-gray-100 dark:border-slate-800 hover:shadow-md transition-all duration-200 group">
                    <div class="p-2 rounded-xl bg-indigo-50 dark:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 group-hover:bg-indigo-600 group-hover:text-white transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-0.5">Active Job Roles</p>
                        <p class="text-xl font-black text-brand-navy dark:text-white leading-none">{{ \App\Models\Designation::where('is_active', true)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl shadow-premium border border-gray-50 dark:border-slate-800 transition-colors duration-300 mb-8">
                <div class="flex items-center justify-between">
                    <div class="p-2 text-brand-navy dark:text-gray-200 font-medium text-lg">
                        {{ __("Welcome back,") }} <span class="text-brand-teal dark:text-brand-accent font-black">{{ Auth::user()->name }}</span>! {{ __("Your HR dashboard is ready.") }}
                    </div>
                    
                    @if(auth()->user()->isAdmin() || auth()->user()->isHR())
                        <div class="flex items-center gap-4">
                            @if($isCalendarConnected)
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center gap-2 px-4 py-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-xl border border-emerald-100 dark:border-emerald-800">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-xs font-black uppercase tracking-widest">Connected</span>
                                    </div>
                                    
                                    <form action="{{ route('google.calendar.disconnect') }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs font-bold text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 underline decoration-red-500/30 hover:decoration-red-500 transition-all">
                                            Disconnect
                                        </button>
                                    </form>
                                </div>
                            @else
                                <a href="{{ route('google.calendar.redirect') }}" class="flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-slate-800 hover:bg-gray-50 dark:hover:bg-slate-700 text-brand-navy dark:text-white rounded-xl border border-gray-200 dark:border-slate-700 transition-all shadow-sm hover:shadow-md group">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/a/a5/Google_Calendar_icon_%282020%29.svg" alt="Google Calendar" class="w-5 h-5 group-hover:scale-110 transition-transform">
                                    <span class="text-xs font-black uppercase tracking-widest">Connect Google Calendar</span>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Active Designations List -->
            <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl shadow-premium border border-gray-50 dark:border-slate-800 transition-colors duration-300">
                <h3 class="text-xl font-black text-brand-navy dark:text-white mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Active Designations by Department
                </h3>
                
                @php
                    $activeDesignations = \App\Models\Designation::where('is_active', true)
                        ->with('department')
                        ->orderBy('name', 'asc')
                        ->get()
                        ->groupBy('department.name');
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($activeDesignations as $deptName => $designations)
                        <div class="border border-gray-100 dark:border-slate-800 rounded-2xl p-5 hover:bg-gray-50 dark:hover:bg-slate-800/50 transition-colors duration-200">
                            @php
                                $dept = $designations->first()->department ?? null;
                                $user = auth()->user();
                                $hasPrivilege = $dept && ! (($user->isHOD() || $user->isManagers()) && $user->department_id != $dept->id);
                            @endphp
                            
                            @if($hasPrivilege)
                                <h4 class="text-brand-teal dark:text-brand-accent font-black uppercase tracking-wider text-xs mb-4">
                                    <a href="{{ route('recruitment.department', $dept) }}" class="hover:underline hover:text-brand-teal dark:hover:text-brand-accent transition-colors duration-200">
                                        {{ $deptName ?? 'Unassigned' }}
                                    </a>
                                </h4>
                            @else
                                <h4 class="text-brand-teal dark:text-brand-accent font-black uppercase tracking-wider text-xs mb-4">
                                    {{ $deptName ?? 'Unassigned' }}
                                </h4>
                            @endif
                            
                            <ul class="space-y-5">
                                @foreach($designations as $designation)
                                    @php $count = $designation->candidates_count ?? $designation->candidates()->count(); @endphp
                                    <li class="py-0.5">
                                        @if($hasPrivilege && $dept)
                                            <a href="{{ route('recruitment.designation', [$dept, $designation]) }}" class="flex items-center justify-between w-full group/item">
                                                <span class="text-brand-navy dark:text-slate-300 font-medium group-hover/item:text-brand-teal transition-colors duration-200">{{ $designation->name }}</span>
                                                <span class="font-black text-sm {{ $count > 0 ? 'text-brand-teal dark:text-brand-accent' : 'text-gray-300 dark:text-slate-700' }} ml-4 transition-all duration-300">
                                                    {{ $count }}
                                                </span>
                                            </a>
                                        @else
                                            <div class="flex items-center justify-between w-full group/item">
                                                <span class="text-brand-navy dark:text-slate-300 font-medium group-hover/item:text-brand-teal transition-colors duration-200">{{ $designation->name }}</span>
                                                <span class="font-black text-sm {{ $count > 0 ? 'text-brand-teal dark:text-brand-accent' : 'text-gray-300 dark:text-slate-700' }} ml-4 transition-all duration-300">
                                                    {{ $count }}
                                                </span>
                                            </div>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @empty
                        <div class="col-span-full py-10 text-center text-gray-400 dark:text-slate-500 italic">
                            No active designations found.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
