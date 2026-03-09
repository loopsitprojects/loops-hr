<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-32 flex flex-col gap-16">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-12 mb-10">
            <div>
                <div class="flex items-center gap-5 mb-4">
                    <div class="w-2.5 h-12 bg-brand-teal rounded-full shadow-[0_0_20px_rgba(20,184,166,0.4)]"></div>
                    <h1 class="text-5xl font-black text-brand-navy dark:text-white uppercase tracking-tighter leading-tight">
                        HR Analytics <span class="text-brand-teal/80">Dashboard</span>
                    </h1>
                </div>
                <p class="text-[11px] text-slate-400 dark:text-slate-500 uppercase tracking-[0.5em] font-black ml-8 opacity-80">Insights & Recruitment Performance</p>
            </div>

            <!-- Dashboard Controls -->
            <div x-data="{ 
                department_id: '{{ $departmentId }}', 
                month: '{{ $month }}',
                year: '{{ $year }}',
                report_type: '{{ $reportType }}',
                designation_filter: '{{ $designationFilter }}',
                showMonthPicker: false,
                showYearPicker: false,
                monthNames: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                get monthYearLabel() {
                    if (this.report_type === 'annual') return this.year;
                    const date = new Date(this.month + '-01');
                    return this.monthNames[date.getMonth()] + ' ' + date.getFullYear();
                }
            }" class="">
                <form action="{{ route('analytics.index') }}" method="GET" class="flex items-center gap-4">
                    <!-- Period Toggle -->
                    <div class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-xl shadow-inner">
                        <button type="button" 
                            @click="report_type = 'monthly'; $nextTick(() => { $el.closest('form').submit() })"
                            :class="report_type === 'monthly' ? 'bg-white dark:bg-slate-700 text-brand-teal shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'"
                            class="px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all">
                            Monthly
                        </button>
                        <button type="button" 
                            @click="report_type = 'annual'; $nextTick(() => { $el.closest('form').submit() })"
                            :class="report_type === 'annual' ? 'bg-white dark:bg-slate-700 text-brand-teal shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'"
                            class="px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all">
                            Annual
                        </button>
                        <input type="hidden" name="report_type" x-model="report_type">
                    </div>

                    <!-- Designation Filter Toggle -->
                    <div class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-xl shadow-inner">
                        <button type="button" 
                            @click="designation_filter = 'active'; $nextTick(() => { $el.closest('form').submit() })"
                            :class="designation_filter === 'active' ? 'bg-white dark:bg-slate-700 text-brand-teal shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'"
                            class="px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all">
                            Active
                        </button>
                        <button type="button" 
                            @click="designation_filter = 'all'; $nextTick(() => { $el.closest('form').submit() })"
                            :class="designation_filter === 'all' ? 'bg-white dark:bg-slate-700 text-brand-teal shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300'"
                            class="px-4 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-wider transition-all">
                            All
                        </button>
                        <input type="hidden" name="designation_filter" x-model="designation_filter">
                    </div>

                    <!-- Department Filter -->
                    @if(auth()->user()->isAdmin() || auth()->user()->isHR())
                        <div class="relative">
                            <select 
                                name="department_id" 
                                x-model="department_id" 
                                @change="$el.closest('form').submit()" 
                                class="bg-white dark:bg-slate-800 border-0 rounded-xl px-4 py-2.5 text-[10px] font-black uppercase tracking-wider focus:ring-0 focus:outline-none transition-all text-slate-600 dark:text-slate-200 min-w-[180px] appearance-none cursor-pointer shadow-sm">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div class="bg-white dark:bg-slate-800 border-0 rounded-xl px-4 py-2.5 text-[10px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-200 min-w-[180px] shadow-sm">
                            {{ auth()->user()->department->name ?? 'No Department' }}
                        </div>
                    @endif

                    <!-- Period Specific Picker -->
                    <div class="relative group" @click.away="showMonthPicker = false; showYearPicker = false">
                        <button type="button" 
                            @click="report_type === 'monthly' ? showMonthPicker = !showMonthPicker : showYearPicker = !showYearPicker"
                            class="bg-white dark:bg-slate-800 border-0 rounded-xl px-4 py-2.5 text-[10px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-200 flex items-center gap-3 min-w-[130px] hover:text-brand-teal focus:outline-none focus:ring-0 transition-all shadow-sm">
                            <span x-text="monthYearLabel"></span>
                            <svg class="w-3.5 h-3.5 ml-auto text-slate-400 group-hover:text-brand-teal transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                        </button>

                        <!-- Month Picker -->
                        <div x-show="showMonthPicker" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            class="absolute right-0 mt-3 w-64 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-premium p-4 z-50 overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-teal to-blue-500"></div>
                            
                            <!-- Year Selector for Monthly View -->
                            <div class="flex items-center justify-between mb-4 mt-2 px-1">
                                <button type="button" @click="const d = new Date(month + '-01'); d.setFullYear(d.getFullYear() - 1); month = d.toISOString().slice(0, 7)" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                                </button>
                                <span class="text-[10px] font-black uppercase text-brand-navy dark:text-white tracking-[0.2em]" x-text="new Date(month + '-01').getFullYear()"></span>
                                <button type="button" @click="const d = new Date(month + '-01'); d.setFullYear(d.getFullYear() + 1); month = d.toISOString().slice(0, 7)" class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg text-slate-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-3 gap-1.5">
                                <template x-for="(name, index) in monthNames" :key="index">
                                    <button type="button" 
                                        @click="const d = new Date(month + '-01'); d.setMonth(index); month = d.toISOString().slice(0, 7); showMonthPicker = false; $nextTick(() => { $el.closest('form').submit() })"
                                        :class="new Date(month + '-01').getMonth() === index ? 'bg-brand-teal text-white shadow-md' : 'hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400'"
                                        class="py-2.5 text-[9px] font-black uppercase rounded-xl transition-all"
                                        x-text="name">
                                    </button>
                                </template>
                            </div>
                            <input type="hidden" name="month" x-model="month">
                        </div>

                        <!-- Year Picker -->
                        <div x-show="showYearPicker" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            class="absolute right-0 mt-3 w-48 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-premium p-4 z-50 overflow-hidden">
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-brand-teal to-blue-500"></div>
                            <select name="year" x-model="year" @change="$el.closest('form').submit()"
                                class="w-full bg-slate-50 dark:bg-slate-900 border-none rounded-xl text-xs font-bold text-brand-navy dark:text-slate-200 focus:ring-brand-teal">
                                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <!-- Download Button with Format Options -->
                    <div x-data="{ showExportMenu: false }" @click.away="showExportMenu = false" class="relative">
                        <button type="button" @click="showExportMenu = !showExportMenu"
                            title="Download Report"
                            class="w-10 h-10 bg-brand-navy dark:bg-brand-teal rounded-xl flex items-center justify-center text-white hover:scale-105 active:scale-95 transition-all shadow-lg shadow-brand-teal/20 group">
                            <svg class="w-5 h-5 group-hover:-translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                        </button>

                        <!-- Export Format Dropdown -->
                        <div x-show="showExportMenu" x-cloak
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            class="absolute right-0 mt-2 w-40 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl shadow-premium overflow-hidden z-50">
                            <a :href="'{{ route('analytics.export') }}?department_id=' + department_id + '&month=' + month + '&year=' + year + '&report_type=' + report_type + '&designation_filter=' + designation_filter + '&format=pdf'"
                                class="flex items-center gap-3 px-4 py-3 text-xs font-bold uppercase tracking-wide text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                PDF
                            </a>
                            <a :href="'{{ route('analytics.export') }}?department_id=' + department_id + '&month=' + month + '&year=' + year + '&report_type=' + report_type + '&designation_filter=' + designation_filter + '&format=csv'"
                                class="flex items-center gap-3 px-4 py-3 text-xs font-bold uppercase tracking-wide text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors border-t border-slate-100 dark:border-slate-800">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                CSV
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div> 
        <!-- Summary Tiles -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-3xl mb-2">
            <!-- Total Applications -->
            <div class="bg-white dark:bg-slate-900 border border-white/10 dark:border-slate-800 rounded-xl p-5 shadow-md group hover:border-brand-teal/30 transition-all relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-brand-teal/5 rounded-full -mr-4 -mt-4 group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex items-center gap-3 relative z-10">
                    <div class="w-10 h-10 rounded-lg bg-brand-teal/10 flex items-center justify-center group-hover:rotate-6 transition-transform">
                        <svg class="w-5 h-5 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-[0.15em] mb-0.5">Total Applicants</p>
                        <h3 class="text-2xl font-black text-brand-navy dark:text-white tracking-tighter">{{ $overviewStats['total_apps'] }}</h3>
                    </div>
                </div>
            </div>

            <!-- Active Designations -->
            <div class="bg-white dark:bg-slate-900 border border-white/10 dark:border-slate-800 rounded-xl p-5 shadow-md group hover:border-blue-500/30 transition-all relative overflow-hidden">
                <div class="absolute top-0 right-0 w-16 h-16 bg-blue-500/5 rounded-full -mr-4 -mt-4 group-hover:scale-150 transition-transform duration-700"></div>
                <div class="flex items-center gap-3 relative z-10">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center group-hover:rotate-6 transition-transform">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-black uppercase text-slate-400 dark:text-slate-500 tracking-[0.15em] mb-0.5">Active Designations</p>
                        <h3 class="text-2xl font-black text-brand-navy dark:text-white tracking-tighter">{{ $overviewStats['active_designations'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    <div>
       <p class="opacity-0">Insights & Recruitment Performance</p>
    </div>
    
        <!-- Main Data Card -->
        <div class="bg-white dark:bg-slate-900 border border-white/5 dark:border-slate-800/50 rounded-[2rem] p-8 shadow-premium overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-10 mb-12">
                <div class="flex items-center gap-6">
                    <div class="w-14 h-14 rounded-[1.5rem] bg-brand-teal/10 flex items-center justify-center shadow-inner">
                        <svg class="w-7 h-7 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-brand-navy dark:text-white uppercase tracking-tighter">Detailed Insights</h2>
                        <div class="h-1 w-12 bg-brand-teal mt-1 rounded-full opacity-50"></div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full border-separate border-spacing-y-2">
                    <thead>
                        <tr class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                            <th class="px-3 py-4 text-left">Designation</th>
                            <th class="px-2 py-2 text-center text-[10px] font-black uppercase tracking-tighter text-slate-400">Total Applicants</th>
                            <th class="px-2 py-2 text-center text-[10px] font-black uppercase tracking-tighter text-slate-400">Shortlisted</th>
                            <th class="px-2 py-2 text-center text-[10px] font-black uppercase tracking-tighter text-slate-400">Tasks</th>
                            <th class="px-2 py-2 text-center text-[10px] font-black uppercase tracking-tighter text-slate-400">Test Sent</th>
                            <th class="px-2 py-2 text-center text-[10px] font-black uppercase tracking-tighter text-slate-400">Test Rec</th>
                            <th class="px-2 py-2 text-center text-[10px] font-black uppercase tracking-tighter text-slate-400">1st Int</th>
                            <th class="px-2 py-2 text-center text-[10px] font-black uppercase tracking-tighter text-slate-400">2nd Int</th>
                            <th class="px-2 py-2 text-center text-[10px] font-black uppercase tracking-tighter text-slate-400">Offer</th>
                            <th class="px-2 py-2 text-center text-[10px] font-black uppercase tracking-tighter text-slate-400">Accepted</th>
                            <th class="px-2 py-2 text-center text-[10px] font-black uppercase tracking-tighter text-slate-400">Joined</th>
                            <th class="px-2 py-2 text-center text-[10px] font-black uppercase tracking-tighter text-slate-400">Rej</th>
                            <th class="px-2 py-2 text-center text-[10px] font-black uppercase tracking-tighter text-slate-400">Avg Speed</th>
                            <th class="px-2 py-2 text-center text-[10px] font-black uppercase tracking-tighter text-slate-400">Avg Salary</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($designationBreakdown->groupBy('department_name') as $departmentName => $designations)
                            <!-- Department Header -->
                            <tr class="bg-slate-100/50 dark:bg-slate-800/50">
                                <td colspan="13" class="px-4 py-3 text-xs font-black uppercase tracking-widest text-brand-teal dark:text-brand-teal border-y border-slate-200 dark:border-slate-800">
                                    {{ $departmentName }}
                                </td>
                            </tr>

                            @foreach($designations as $row)
                                <tr class="group transition-all">
                                    <td class="px-3 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-l border-y border-slate-100 dark:border-slate-800">
                                        <div class="flex items-center gap-2 pl-4">
                                            <div class="w-1 h-1 bg-slate-400 rounded-full"></div>
                                            <span class="text-[11px] font-bold text-slate-600 dark:text-slate-300 uppercase tracking-tight leading-tight">{{ $row->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-2 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-y border-slate-100 dark:border-slate-800 text-center">
                                        <span class="text-xs font-black text-slate-700 dark:text-slate-300">{{ $row->total_applications }}</span>
                                    </td>
                                    <td class="px-2 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-y border-slate-100 dark:border-slate-800 text-center">
                                        <span class="text-xs font-black text-brand-teal">{{ $row->stages['shortlisted'] ?? 0 }}</span>
                                    </td>
                                    <td class="px-2 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-y border-slate-100 dark:border-slate-800 text-center">
                                        <span class="text-xs font-black text-slate-700 dark:text-slate-300">
                                            {{ $row->total_tasks }}
                                        </span>
                                    </td>
                                    <td class="px-2 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-y border-slate-100 dark:border-slate-800 text-center">
                                        <span class="text-xs font-black text-indigo-500">{{ $row->stages['test_sent'] ?? 0 }}</span>
                                    </td>
                                    <td class="px-2 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-y border-slate-100 dark:border-slate-800 text-center">
                                        <span class="text-xs font-black text-indigo-600">{{ $row->stages['test_received'] ?? 0 }}</span>
                                    </td>
                                    <td class="px-2 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-y border-slate-100 dark:border-slate-800 text-center">
                                        <span class="text-xs font-black text-brand-teal">{{ $row->stages['1st_interview'] ?? 0 }}</span>
                                    </td>
                                    <td class="px-2 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-y border-slate-100 dark:border-slate-800 text-center">
                                        <span class="text-xs font-black text-emerald-500">{{ $row->stages['2nd_interview'] ?? 0 }}</span>
                                    </td>
                                    <td class="px-2 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-y border-slate-100 dark:border-slate-800 text-center">
                                        <span class="text-xs font-black text-amber-500">{{ $row->stages['offer_sent'] ?? 0 }}</span>
                                    </td>
                                    <td class="px-2 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-y border-slate-100 dark:border-slate-800 text-center">
                                        <span class="text-xs font-black text-blue-500">{{ $row->stages['offer_accepted'] ?? 0 }}</span>
                                    </td>
                                    <td class="px-2 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-y border-slate-100 dark:border-slate-800 text-center">
                                        <span class="text-xs font-black text-green-600">{{ $row->stages['joined'] ?? 0 }}</span>
                                    </td>
                                    <td class="px-2 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-y border-slate-100 dark:border-slate-800 text-center">
                                        <span class="text-xs font-black text-rose-500">{{ $row->stages['rejected'] ?? 0 }}</span>
                                    </td>
                                    <td class="px-2 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-y border-slate-100 dark:border-slate-800 text-center">
                                        <span class="text-xs font-black text-amber-600">{{ $row->avg_time_to_hire ?? 0 }}d</span>
                                    </td>
                                    <td class="px-2 py-5 bg-slate-50/50 dark:bg-slate-800/30 border-r border-y border-slate-100 dark:border-slate-800 text-center text-center">
                                        <span class="text-xs font-black text-blue-600">{{ $row->avg_expected_salary > 0 ? number_format($row->avg_expected_salary) : '-' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Visual Intelligence Section -->
        <div class="">
            <div class="flex items-center gap-6 mb-12">
                <div class="w-1.5 h-10 bg-brand-teal rounded-full"></div>
                <div>
                    <h2 class="text-3xl font-black text-brand-navy dark:text-white uppercase tracking-tighter">Recruitment Intelligence</h2>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-[0.3em] font-black">Visual Pipeline & Performance Trends</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-8 pb-20">
                <!-- Departmental Comparison -->
                <div class="bg-white dark:bg-slate-900 border border-white/5 dark:border-slate-800/50 rounded-[2.5rem] p-8 shadow-premium overflow-hidden">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h3 class="text-xs font-black uppercase tracking-widest text-slate-400 dark:text-slate-500 mb-1">Department Comparison</h3>
                            <p class="text-[10px] text-slate-500">Volume vs Average Hiring Speed (Days)</p>
                        </div>
                    </div>
                    <div class="h-[400px]">
                        <canvas id="deptChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart.js Default Styling
            Chart.defaults.font.family = "'Inter', sans-serif";
            Chart.defaults.color = document.documentElement.classList.contains('dark') ? '#94a3b8' : '#64748b';

            const isDark = document.documentElement.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';

            // Departmental Comparison (Grouped Bar)
            const deptCtx = document.getElementById('deptChart').getContext('2d');
            const deptData = @json($deptComparison);
            
            new Chart(deptCtx, {
                type: 'bar',
                data: {
                    labels: deptData.labels,
                    datasets: [
                        {
                            label: 'Applicants',
                            data: deptData.volumes,
                            backgroundColor: '#14b8a6',
                            borderRadius: 12,
                            barThickness: 30
                        },
                        {
                            label: 'Avg Days to Hire',
                            data: deptData.speeds,
                            backgroundColor: '#06b6d4',
                            borderRadius: 12,
                            barThickness: 30
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { 
                                font: { weight: 'bold', size: 10 }, 
                                padding: 20,
                                usePointStyle: true,
                                pointStyle: 'circle'
                            }
                        },
                        tooltip: {
                            backgroundColor: isDark ? '#1e293b' : '#ffffff',
                            titleColor: isDark ? '#ffffff' : '#1e293b',
                            bodyColor: isDark ? '#94a3b8' : '#64748b',
                            borderColor: 'rgba(20, 184, 166, 0.3)',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 12,
                        }
                    },
                    scales: {
                        x: { 
                            grid: { display: false },
                            ticks: { font: { size: 10, weight: '900' } }
                        },
                        y: {
                            grid: { color: gridColor },
                            ticks: { font: { size: 9, weight: 'bold' } }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
