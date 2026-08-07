<x-app-layout>
    @section('title', $designation->title . ' - ' . $department->name . ' | recruitment')
    <x-slot name="header">
        <div class="flex justify-between items-center px-2 gap-6">
            <div>
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-2 text-[10px] font-bold uppercase tracking-widest">
                        <li>
                            <a href="{{ route('recruitment.index') }}" class="text-slate-400 dark:text-slate-500 hover:text-brand-teal transition-colors">Recruitment</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-2.5 h-2.5 text-slate-300 dark:text-slate-700 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <a href="{{ route('recruitment.department', $department) }}" class="text-slate-400 dark:text-slate-500 hover:text-brand-teal transition-colors">{{ $department->name }}</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-2.5 h-2.5 text-slate-300 dark:text-slate-700 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="text-brand-teal dark:text-brand-accent">{{ $designation->name }}</span>
                        </li>
                    </ol>
                </nav>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $designation->name }}
                </h2>
                <p class="text-xs text-slate-500 mt-1 uppercase tracking-widest">{{ $department->name }} Division</p>
            </div>
            
                <form action="{{ route('recruitment.designation', [$department, $designation]) }}" method="GET" class="relative group">
                    @if($showArchived)
                        <input type="hidden" name="archived" value="1">
                    @endif
                    
                    <div class="relative flex items-center">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}" 
                               placeholder="Search by name or email..." 
                               class="block w-full px-4 py-2 bg-transparent text-slate-700 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 border border-slate-300 dark:border-slate-700/50 rounded-xl text-xs font-medium focus:ring-1 focus:ring-brand-teal focus:border-brand-teal transition-all">
                    </div>
                </form>

            <div class="flex flex-wrap items-center gap-4 justify-end">
                @if(auth()->user()->isAdmin() || auth()->user()->isHR())
                <form id="bulk-delete-form" 
                      action="{{ $showArchived ? route('recruitment.bulkUnarchive') : route('recruitment.bulkArchive') }}" 
                      method="POST" 
                      class="hidden flex items-center gap-2">
                    @csrf
                    @if($showArchived)
                        <button type="submit" 
                           formaction="{{ route('recruitment.bulkUnarchive') }}"
                           onclick="return confirm('Are you sure you want to restore the selected candidates from the archive?');" 
                           class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-emerald-600 hover:bg-emerald-700 text-white rounded-md text-xs font-semibold uppercase tracking-widest transition-all duration-300 shadow-sm">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                            </svg>
                            {{ __('Revert Selected') }} (<span class="selected-count">0</span>)
                        </button>
                        <button type="submit" 
                           formaction="{{ route('recruitment.bulkDestroy') }}"
                           onclick="return confirm('Are you sure you want to PERMANENTLY DELETE the selected candidates? This action cannot be undone.');" 
                           class="inline-flex items-center px-4 py-2 bg-red-500 border border-red-500 hover:bg-red-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest transition-all duration-300 shadow-sm">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            {{ __('Delete Selected') }} (<span class="selected-count">0</span>)
                        </button>
                    @else
                        <button type="submit" 
                           formaction="{{ route('recruitment.bulkArchive') }}"
                           onclick="return confirm('Are you sure you want to archive the selected candidates?');" 
                           class="inline-flex items-center px-4 py-2 bg-amber-500 border border-amber-500 hover:bg-amber-600 text-white rounded-md text-xs font-semibold uppercase tracking-widest transition-all duration-300 shadow-sm">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                            </svg>
                            {{ __('Archive Selected') }} (<span class="selected-count">0</span>)
                        </button>
                    @endif
                    <div id="bulk-delete-inputs"></div>
                </form>
                @endif

                <a href="{{ request()->fullUrlWithQuery(['archived' => $showArchived ? null : 1]) }}" 
                   class="inline-flex items-center px-4 py-2 {{ $showArchived ? 'bg-brand-navy text-white' : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200' }} border border-slate-300 dark:border-slate-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-300 shadow-sm">
                    <svg class="w-3.5 h-3.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                    </svg>
                    {{ $showArchived ? 'View Active' : 'View Archive' }}
                </a>

                <div class="relative">
                    @if(auth()->user()->isAdmin() || auth()->user()->isHR())
                    <button onclick="toggleBulkUploadModal()" 
                       class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-300 shadow-sm">
                        {{ __('Bulk Upload') }}
                    </button>
                    @endif
                    
                    <!-- Bulk Upload Dropdown -->
                    <div id="bulkUploadModal" class="hidden absolute right-0 top-full mt-2 z-50">
                        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl w-64 border border-slate-200 dark:border-slate-700">
                            <!-- Modal Header -->
                            <div class="flex items-center justify-between p-2.5 border-b border-slate-100 dark:border-slate-800">
                                <h3 class="text-[10px] font-bold text-brand-navy dark:text-white uppercase tracking-widest">Bulk CV Upload</h3>
                                <button onclick="toggleBulkUploadModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Modal Body -->
                            <div class="p-2.5">
                                <form id="bulkUploadForm" method="POST" action="{{ route('recruitment.bulkStore') }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="department_id" value="{{ $department->id }}">
                                    <input type="hidden" name="designation_id" value="{{ $designation->id }}">

                                    <!-- Multi-Upload Dropzone -->
                                    <div class="relative group">
                                        <input type="file" id="bulkCvFiles" name="cvs[]" accept=".pdf" multiple required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <div class="border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-lg p-4 text-center transition-all duration-300 group-hover:border-brand-teal/50 group-hover:bg-slate-50 dark:group-hover:bg-slate-800/50">
                                            <div class="w-8 h-8 bg-brand-teal/10 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                                                <svg class="w-4 h-4 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                            </div>
                                            <p class="text-[9px] font-bold text-brand-navy dark:text-white uppercase tracking-widest mb-1">Select CVs</p>
                                            <p class="text-[8px] text-slate-400 uppercase tracking-wider">Multiple PDFs</p>
                                        </div>
                                    </div>

                                    <!-- Status Message -->
                                    <div id="bulkUploadStatus" class="mt-2 p-1.5 bg-slate-50 dark:bg-slate-800 rounded-lg hidden text-center"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    @if(auth()->user()->isAdmin() || auth()->user()->isHR())
                    <button onclick="openUploadModal()" 
                       class="inline-flex items-center px-4 py-2 bg-brand-navy dark:bg-brand-teal text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:opacity-90 transition-all duration-300 shadow-sm">
                        {{ __('New Candidate') }}
                    </button>
                    @endif
                    
                    <!-- Upload Modal (Dropdown Style) -->
                    <div id="uploadModal" class="hidden absolute right-0 top-full mt-2 z-50">
                        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl w-64 border border-slate-200 dark:border-slate-700">
                            <!-- Modal Header -->
                            <div class="flex items-center justify-between p-2.5 border-b border-slate-100 dark:border-slate-800">
                                <h3 class="text-[10px] font-bold text-brand-navy dark:text-white uppercase tracking-widest">Upload CV</h3>
                                <button onclick="closeUploadModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Modal Body -->
                            <div class="p-2.5">
                                <form id="uploadForm" method="POST" action="{{ route('recruitment.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="department_id" value="{{ $department->id }}">
                                    <input type="hidden" name="designation_id" value="{{ $designation->id }}">

                                    <!-- Upload Dropzone -->
                                    <div class="relative group">
                                        <input type="file" id="cvFile" name="cv" accept=".pdf" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <div class="border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-lg p-4 text-center transition-all duration-300 group-hover:border-brand-teal/50 group-hover:bg-slate-50 dark:group-hover:bg-slate-800/50">
                                            <div class="w-8 h-8 bg-brand-teal/10 rounded-lg flex items-center justify-center mx-auto mb-2 group-hover:scale-110 transition-transform">
                                                <svg class="w-4 h-4 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                </svg>
                                            </div>
                                            <p class="text-[9px] font-bold text-brand-navy dark:text-white uppercase tracking-widest mb-1">Drop PDF</p>
                                            <p class="text-[8px] text-slate-400 uppercase tracking-wider">Auto-extracts</p>
                                        </div>
                                    </div>

                                    <!-- Status Message -->
                                    <div id="uploadStatus" class="mt-2 p-1.5 bg-slate-50 dark:bg-slate-800 rounded-lg hidden text-center"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-12">
            @if(request()->has('candidate_id'))
                <div class="mb-6 bg-brand-teal/10 border border-brand-teal/20 p-4 rounded-2xl flex items-center justify-between animate-in fade-in slide-in-from-top-4 duration-500">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-teal/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-brand-navy dark:text-white uppercase tracking-widest">Isolated View</h4>
                            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-bold uppercase tracking-wider">Showing specific candidate from notification</p>
                        </div>
                    </div>
                    <a href="{{ request()->url() }}" class="px-4 py-2 bg-brand-navy dark:bg-slate-800 text-white dark:text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-800 transition-all shadow-sm">
                        View All Candidates
                    </a>
                </div>
            @endif
            
            <!-- Status Filter -->
            <div class="mb-8 overflow-x-auto no-scrollbar pb-2">
                <div class="flex items-center gap-2 min-w-max">
                    <a href="{{ request()->fullUrlWithQuery(['stage' => 'all', 'page' => null]) }}" 
                       class="h-8 px-4 flex items-center justify-center rounded-full text-[9px] whitespace-nowrap text-center leading-3 font-black uppercase tracking-[0.1em] transition-all duration-300 {{ $currentStage == 'all' ? 'shadow-lg shadow-teal-500/20 scale-105' : 'bg-white dark:bg-slate-900 text-slate-400 dark:text-slate-500 border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50' }}"
                       {!! $currentStage == 'all' ? 'style="background-color: #0d9488 !important; color: white !important;"' : '' !!}>
                        All Candidates
                    </a>
                    @foreach($stages as $key => $label)
                        <a href="{{ request()->fullUrlWithQuery(['stage' => $key, 'page' => null]) }}" 
                           class="h-8 px-4 flex items-center justify-center rounded-full text-[9px] whitespace-nowrap text-center leading-3 font-black uppercase tracking-[0.1em] transition-all duration-300 {{ $currentStage == $key ? 'shadow-lg shadow-teal-500/20 scale-105' : 'bg-white dark:bg-slate-900 text-slate-400 dark:text-slate-500 border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50' }}"
                           {!! $currentStage == $key ? 'style="background-color: #0d9488 !important; color: white !important;"' : '' !!}>
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 transition-colors duration-300 rounded-3xl shadow-sm overflow-hidden border border-slate-100 dark:border-slate-800">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full table-fixed border-separate border-spacing-y-2">
                        <thead>
                            <tr class="text-[10px] font-black uppercase tracking-[0.15em] text-slate-400 dark:text-slate-300">
                                <th class="pb-3 pl-4 w-[3%]">
                                    <input type="checkbox" id="select-all" class="rounded border-slate-300 text-brand-navy focus:ring-brand-navy dark:border-slate-700 dark:bg-slate-900 dark:checked:bg-brand-navy">
                                </th>
                                <th class="pb-3 text-left w-[11%]">Name</th>
                                <th class="pb-3 text-left w-[9%]">Email</th>
                                <th class="pb-3 text-left w-[8%]">Phone</th>
                                <th class="pb-3 text-left w-[6%]">Salary</th>
                                <th class="pb-3 text-center w-[10%]">Status</th>
                                <th class="pb-3 text-center w-[7%]">Pipeline</th>
                                <th class="pb-3 text-center w-[5%]">Rating</th>
                                <th class="pb-3 text-center w-[4%]">Fbk</th>
                                @if(auth()->user()->isAdmin() || auth()->user()->isHR())
                                    <th class="pb-3 text-center w-[4%]">Test</th>
                                    <th class="pb-3 text-center w-[4%]">Rej</th>
                                @endif
                                <th class="pb-3 text-center w-[4%]">Link</th>
                                @if(auth()->user()->isAdmin() || auth()->user()->isHR())
                                    <th class="pb-3 text-center w-[4%]">Sch</th>
                                @endif
                                <th class="pb-3 text-center w-[4%]">CV</th>
                                <th class="pb-3 text-center w-[4%]">PTF</th>
                                @if(auth()->user()->isAdmin() || auth()->user()->isHR())
                                    <th class="pb-3 text-center w-[4%]">{{ $showArchived ? 'Rst' : 'Arc' }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($candidates as $candidate)
                                <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="py-3 pl-4 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                                        <input type="checkbox" name="selected_candidates[]" value="{{ $candidate->id }}" class="candidate-checkbox rounded border-slate-300 text-brand-navy focus:ring-brand-navy dark:border-slate-700 dark:bg-slate-900 dark:checked:bg-brand-navy">
                                    </td>
                                    <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                                         <div class="text-sm font-bold text-brand-navy dark:text-white cursor-text focus:outline-none focus:ring-2 focus:ring-brand-teal/20 rounded-lg p-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all editable-text"
                                            @if(auth()->user()->isAdmin() || auth()->user()->isHR()) contenteditable="true" @endif
                                            data-candidate-id="{{ $candidate->id }}"
                                            data-field="name"
                                            spellcheck="false"
                                            onblur="updateField(this)">
                                            {{ $candidate->name }}
                                        </div>
                                    </td>
                                    <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                                        <div class="text-xs font-medium text-slate-500 dark:text-slate-400 cursor-text focus:outline-none focus:ring-2 focus:ring-brand-teal/20 rounded-lg p-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all editable-text max-w-[80px] truncate"
                                            @if(auth()->user()->isAdmin() || auth()->user()->isHR()) contenteditable="true" @endif
                                            data-candidate-id="{{ $candidate->id }}"
                                            data-field="email"
                                            spellcheck="false"
                                            onblur="updateField(this)"
                                            title="{{ $candidate->email }}">
                                            {{ $candidate->email }}
                                        </div>
                                    </td>
                                    <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                                        <div class="text-xs font-medium text-slate-400 dark:text-slate-500 cursor-text focus:outline-none focus:ring-2 focus:ring-brand-teal/20 rounded-lg p-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all editable-text"
                                            @if(auth()->user()->isAdmin() || auth()->user()->isHR()) contenteditable="true" @endif
                                            data-candidate-id="{{ $candidate->id }}"
                                            data-field="phone"
                                            spellcheck="false"
                                            onblur="updateField(this)">
                                            {{ $candidate->phone ?? '—' }}
                                        </div>
                                    </td>
                                    <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                                        <div class="text-xs font-bold text-slate-700 dark:text-white cursor-text focus:outline-none focus:ring-2 focus:ring-brand-teal/20 rounded-lg p-2 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-all editable-text"
                                            @if(auth()->user()->isAdmin() || auth()->user()->isHR()) contenteditable="true" @endif
                                            data-candidate-id="{{ $candidate->id }}"
                                            data-field="expected_salary"
                                            spellcheck="false"
                                            oninput="formatSalary(this)"
                                            onblur="updateField(this)">
                                            {{ $candidate->expected_salary ? (is_numeric(str_replace(',', '', $candidate->expected_salary)) ? number_format(str_replace(',', '', $candidate->expected_salary)) : $candidate->expected_salary) : '—' }}
                                        </div>
                                    </td>
                                    <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                                        <div class="relative flex items-center">
                                            <select data-candidate-id="{{ $candidate->id }}"
                                                data-field="stage"
                                                class="editable-field block w-full bg-transparent text-[10px] font-bold uppercase cursor-pointer focus:outline-none transition-colors pr-5 text-slate-700 dark:text-white disabled:cursor-not-allowed disabled:opacity-70"
                                                style="appearance: none !important; -webkit-appearance: none !important; background-color: transparent !important; border: none !important; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2394a3b8%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right center; background-size: 0.5rem auto;"
                                                {{ (auth()->user()->isAdmin() || auth()->user()->isHR() || auth()->user()->isHOD() || auth()->user()->isManagers() || auth()->user()->isManager()) ? '' : 'disabled' }}>
                                                @if(auth()->user()->isAdmin() || auth()->user()->isHR() || auth()->user()->isManager() || auth()->user()->isHOD())
                                                    {{-- Admin, HR, Operations Manager, and HOD see all options --}}
                                                    <option value="default" {{ $candidate->stage == 'default' ? 'selected' : '' }}>default</option>
                                                    <option value="shortlisted" {{ $candidate->stage == 'shortlisted' ? 'selected' : '' }}>shortlisted</option>
                                                    <option value="test_sent" {{ $candidate->stage == 'test_sent' ? 'selected' : '' }}>test sent</option>
                                                    <option value="test_received" {{ $candidate->stage == 'test_received' ? 'selected' : '' }}>test received</option>
                                                    <option value="1st_interview" {{ $candidate->stage == '1st_interview' ? 'selected' : '' }}>1st interview</option>
                                                    <option value="2nd_interview" {{ $candidate->stage == '2nd_interview' ? 'selected' : '' }}>2nd interview</option>
                                                    <option value="offer_sent" {{ $candidate->stage == 'offer_sent' ? 'selected' : '' }}>offer sent</option>
                                                    <option value="offer_accepted" {{ $candidate->stage == 'offer_accepted' ? 'selected' : '' }}>offer accepted</option>
                                                    <option value="joined" {{ $candidate->stage == 'joined' ? 'selected' : '' }}>joined</option>
                                                    <option value="rejected" {{ $candidate->stage == 'rejected' ? 'selected' : '' }}>rejected</option>
                                                @else
                                                    {{-- HOD and Managers see limited options --}}
                                                    @if(!in_array($candidate->stage, ['1st_interview', '2nd_interview', 'rejected']))
                                                        <option value="{{ $candidate->stage }}" selected>{{ str_replace('_', ' ', $candidate->stage) }}</option>
                                                     @endif
                                                    <option value="1st_interview" {{ $candidate->stage == '1st_interview' ? 'selected' : '' }}>1st interview</option>
                                                    <option value="2nd_interview" {{ $candidate->stage == '2nd_interview' ? 'selected' : '' }}>2nd interview</option>
                                                    <option value="rejected" {{ $candidate->stage == 'rejected' ? 'selected' : '' }}>rejected</option>
                                                @endif
                                            </select>
                                        </div>
                                    </td>
                                    <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-center">
                                        <div class="flex flex-col items-center leading-tight" id="pipeline-cell-{{ $candidate->id }}">
                                            @if($candidate->finalized_at)
                                                @if($candidate->stage == 'joined' || $candidate->stage == 'offer_accepted')
                                                    <span class="text-[8px] uppercase tracking-wide font-bold text-brand-teal">Hired</span>
                                                    <span class="text-[10px] font-bold text-slate-700 dark:text-white">{{ $candidate->created_at->diffForHumans($candidate->finalized_at, true) }}</span>
                                                @elseif($candidate->stage == 'rejected')
                                                    <span class="text-[8px] uppercase tracking-wide font-bold text-red-500">Rejected</span>
                                                    <span class="text-[10px] font-bold text-red-700 dark:text-red-400">{{ $candidate->created_at->diffForHumans($candidate->finalized_at, true) }}</span>
                                                @else
                                                    <span class="text-[8px] uppercase tracking-wide font-bold text-slate-400">Closed</span>
                                                    <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">{{ $candidate->created_at->diffForHumans($candidate->finalized_at, true) }}</span>
                                                @endif
                                            @else
                                                <span class="text-[8px] uppercase tracking-wide font-bold text-slate-400">Active</span>
                                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">{{ $candidate->created_at->diffForHumans(null, true) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-center">
                                        <div class="relative flex items-center justify-center">
                                            <select data-candidate-id="{{ $candidate->id }}"
                                                data-field="rating"
                                                class="editable-field block w-full bg-transparent text-[10px] font-black text-brand-teal bg-brand-teal/5 rounded-lg py-1 px-2 cursor-pointer focus:outline-none transition-all text-center pr-4"
                                                style="appearance: none !important; -webkit-appearance: none !important; background-color: rgba(20, 184, 166, 0.05) !important; border: none !important; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%2314b8a6%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 4px center; background-size: 0.4rem auto;"
                                                {{ (auth()->user()->isAdmin() || auth()->user()->isHR() || auth()->user()->isHOD() || auth()->user()->isManagers() || auth()->user()->isManager()) ? '' : 'disabled' }}>
                                                <option value="0" {{ ($candidate->rating ?? 0) == 0 ? 'selected' : '' }}>—</option>
                                                @for($i = 1; $i <= 5; $i++)
                                                    <option value="{{ $i }}" {{ ($candidate->rating ?? 0) == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                @endfor
                                            </select>
                                            <span class="text-[8px] text-slate-400 font-bold ml-1">/5</span>
                                        </div>
                                    </td>
                                    <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-center relative group/tooltip">
                                        <button class="feedback-trigger inline-flex items-center justify-center w-7 h-7 rounded-full transition-all {{ $candidate->feedbacks->isNotEmpty() ? 'bg-brand-teal/20 text-brand-teal ring-1 ring-brand-teal/30 hover:bg-brand-teal/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-600 hover:text-brand-teal hover:bg-brand-teal/10' }}"
                                            data-candidate-id="{{ $candidate->id }}"
                                            title="{{ $candidate->feedbacks->isNotEmpty() ? $candidate->feedbacks->count() . ' Feedback(s)' : 'Add feedback' }}"
                                            onclick="openFeedbackModal({{ $candidate->id }})">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                            </svg>
                                        </button>

                                        @if($candidate->feedbacks->isNotEmpty())
                                            <!-- Hover Tooltip -->
                                            <div class="feedback-tooltip pointer-events-none opacity-0 group-hover/tooltip:opacity-100 transition-opacity duration-200 absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 bg-slate-900/95 dark:bg-slate-950/95 backdrop-blur-md rounded-xl p-3 shadow-xl border border-slate-800/80 text-left z-50">
                                                <p class="text-[9px] font-black uppercase tracking-wider text-brand-teal mb-2 border-b border-slate-800 pb-1">Previous Feedbacks ({{ $candidate->feedbacks->count() }})</p>
                                                <div class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar">
                                                    @foreach($candidate->feedbacks as $f)
                                                        <div class="text-[10px] leading-tight">
                                                            <div class="flex justify-between items-center gap-1 font-bold text-slate-300">
                                                                <span>{{ $f->user->name }}</span>
                                                                <span class="text-[8px] text-slate-500 font-medium">{{ $f->created_at->diffForHumans() }}</span>
                                                            </div>
                                                            <p class="text-slate-400 font-medium mt-0.5 line-clamp-2">{{ $f->feedback }}</p>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <!-- Down Arrow -->
                                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900/95 dark:border-t-slate-950/95"></div>
                                            </div>
                                        @endif
                                    </td>

                                    @if(auth()->user()->isAdmin() || auth()->user()->isHR())
                                        {{-- Test Column --}}
                                        <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-center">
                                            <button onclick="openAssessmentModal({{ $candidate->id }}, '{{ $candidate->name }}')" 
                                                    class="inline-flex items-center justify-center {{ $candidate->assessments->count() > 0 ? 'text-emerald-500' : 'text-slate-400 dark:text-slate-500' }} hover:text-brand-teal transition-colors group" 
                                                    title="{{ $candidate->assessments->count() > 0 ? 'Assessment Sent (Click to send again)' : 'Send Assessment Task' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                                </svg>
                                            </button>
                                        </td>
                                        {{-- Rejection Column --}}
                                        <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-center">
                                            <button onclick="sendRejection({{ $candidate->id }}, '{{ $candidate->name }}')" 
                                                    class="inline-flex items-center justify-center {{ $candidate->status == 'Rejected' ? 'text-red-500' : 'text-slate-400 dark:text-slate-500' }} hover:text-red-600 transition-colors group" 
                                                    title="{{ $candidate->status == 'Rejected' ? 'Already Rejected' : 'Send Rejection Email' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    @endif

                                    {{-- Link Column (Submissions) --}}
                                    <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-center">
                                            @php 
                                                $submission = $candidate->assessments->whereIn('status', ['Submitted', 'Completed'])->first();
                                            @endphp
                                            <div class="flex items-center justify-center gap-2">
                                                @if($submission)
                                                    @if($submission->file_path)
                                                        <a href="{{ Storage::url($submission->file_path) }}" target="_blank" 
                                                        class="text-blue-400 hover:text-blue-500 transition-colors" 
                                                        title="Download Task File">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                    @if($submission->submission_link)
                                                        <a href="{{ $submission->submission_link }}" target="_blank" 
                                                        class="text-brand-teal hover:opacity-80 transition-colors" 
                                                        title="View Submission Link">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                            </svg>
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                    </td>

                                    @if(auth()->user()->isAdmin() || auth()->user()->isHR())
                                        {{-- Sch Column --}}
                                        <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-center">
                                            <button onclick="openScheduleModal({{ $candidate->id }}, '{{ $candidate->name }}')" 
                                                    class="{{ $candidate->interviews->count() > 0 ? 'text-emerald-500' : 'text-slate-400 dark:text-slate-500' }} hover:text-brand-teal transition-colors" 
                                                    title="{{ $candidate->interviews->count() > 0 ? 'Interview Scheduled (Click to schedule again)' : 'Schedule Interview' }}">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </button>
                                        </td>
                                    @endif

                                    {{-- CV Column (Visible to all) --}}
                                    <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-center">
                                        <a href="{{ rtrim(config('filesystems.disks.ftp_cvs.url'), '/') }}/{{ implode('/', array_map('rawurlencode', explode('/', ltrim($candidate->cv_path, '/')))) }}" target="_blank" class="inline-flex items-center justify-center text-slate-400 dark:text-slate-500 hover:text-brand-teal transition-colors" title="View CV">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </a>
                                    </td>

                                    {{-- PTF Column --}}
                                    <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-center">
                                        <button class="portfolio-trigger inline-flex items-center justify-center w-7 h-7 rounded-full transition-all {{ $candidate->portfolio ? 'bg-brand-teal/20 text-brand-teal ring-1 ring-brand-teal/30 hover:bg-brand-teal/30' : 'bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-600 hover:text-brand-teal hover:bg-brand-teal/10' }}"
                                            data-candidate-id="{{ $candidate->id }}"
                                            data-portfolio="{{ $candidate->portfolio }}"
                                            title="{{ $candidate->portfolio ?: 'Add portfolio link' }}"
                                            onclick="openPortfolioModal({{ $candidate->id }}, '{{ addslashes($candidate->portfolio) }}')">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                            </svg>
                                        </button>
                                    </td>

                                    @if(auth()->user()->isAdmin() || auth()->user()->isHR())
                                        {{-- Archive / Revert Column --}}
                                        <td class="py-3 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-center">
                                            @if($showArchived)
                                                <form action="{{ route('recruitment.unarchiveCandidate', $candidate) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            onclick="return confirm('Revert {{ addslashes($candidate->name) }} back to active recruitments?');" 
                                                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-200 dark:hover:bg-emerald-800/50 transition-all" 
                                                            title="Revert / Restore candidate to active list">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('recruitment.archiveCandidate', $candidate) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            onclick="return confirm('Archive {{ addslashes($candidate->name) }}?');" 
                                                            class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition-all" 
                                                            title="Archive candidate">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    @endif

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ (auth()->user()->isAdmin() || auth()->user()->isHR()) ? 16 : 11 }}" class="py-12 text-center text-xs font-bold text-slate-400 uppercase tracking-widest italic">
                                        No candidates found for this designation.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($candidates->hasPages())
                    <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-800/30 border-t border-gray-50 dark:border-slate-800">
                        {{ $candidates->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(Auth::user()->isAdmin() || Auth::user()->isHR() || Auth::user()->isHOD() || Auth::user()->isManagers() || Auth::user()->isManager())
    <style>
        /* Force dropdown option visibility */
        select option {
            background-color: #ffffff;
            color: #1e293b; /* slate-800 */
        }
        :is(.dark) select option {
            background-color: #0f172a; /* slate-900 */
            color: #f1f5f9; /* slate-100 */
        }

        select.editable-field {
            color: #334155 !important; /* slate-700 */
        }
        :is(.dark) select.editable-field {
            color: #f1f5f9 !important; /* slate-100 */
        }

        .editable-text:empty:before {
            content: attr(data-placeholder);
            color: #94a3b8;
            opacity: 0.6;
            font-style: italic;
        }

        .editable-text:empty {
            min-height: 2.5rem;
            min-width: 100px;
            display: flex;
            align-items: center;
            border: 1px dashed #cbd5e1; /* slate-300 */
            background-color: rgba(241, 245, 249, 0.5); /* slate-50 */
            border-radius: 0.75rem;
            padding: 0 0.75rem;
        }

        :is(.dark) .editable-text:empty {
            border-color: #334155; /* slate-700 */
            background-color: rgba(30, 41, 59, 0.3); /* slate-800 */
        }

        /* Modern Flatpickr Styling */
        .flatpickr-calendar {
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.5) !important;
            border-radius: 20px !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
            padding: 20px 15px !important;
            z-index: 10000000 !important; /* Higher than modal 9999999 */
        }

        .flatpickr-innerContainer {
            padding: 0 8px;
        }

        .dayContainer {
            padding: 0 2px;
        }

        :is(.dark) .flatpickr-calendar {
            background: rgba(15, 23, 42, 0.95) !important;
            border-color: rgba(30, 41, 59, 0.5) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
        }

        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, 
        .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, 
        .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, 
        .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
            background: #0d9488 !important; /* brand-teal */
            border-color: #0d9488 !important;
            color: white !important;
            border-radius: 12px;
        }

        .flatpickr-day:hover {
            background: rgba(13, 148, 136, 0.1) !important;
            border-color: transparent !important;
        }

        .flatpickr-months .flatpickr-month {
            color: #1e293b !important;
        }

        :is(.dark) .flatpickr-months .flatpickr-month {
            color: #f1f5f9 !important;
        }

        .flatpickr-current-month .flatpickr-monthDropdown-months {
            font-weight: 800 !important;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .flatpickr-time input:hover, .flatpickr-time .flatpickr-am-pm:hover, 
        .flatpickr-time input:focus, .flatpickr-time .flatpickr-am-pm:focus {
            background: rgba(13, 148, 136, 0.1) !important;
        }

        .flatpickr-calendar.hasTime .flatpickr-time {
            border-top: 1px solid rgba(226, 232, 240, 0.5) !important;
        }

        :is(.dark) .flatpickr-calendar.hasTime .flatpickr-time {
            border-top-color: rgba(30, 41, 59, 0.5) !important;
        }

        /* Interviewer Selection Pills - Legacy */
        .interviewer-pill.selected {
            background: rgba(45, 212, 191, 0.1) !important;
            border-color: #2dd4bf !important;
            box-shadow: 0 0 15px rgba(45, 212, 191, 0.15);
        }
        .interviewer-pill.selected .pill-indicator {
            background: #2dd4bf !important;
            box-shadow: 0 0 8px #2dd4bf;
        }

        /* Custom Searchable Dropdown */
        .dropdown-container {
            position: relative;
            width: 100%;
        }
        .dropdown-trigger {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 0.75rem 1rem;
            background: #f8fafc; /* slate-50 */
            border-radius: 1rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 4rem;
        }
        :is(.dark) .dropdown-trigger {
            background: #1e293b; /* slate-800 */
        }
        .dropdown-trigger:hover {
            background: #f1f5f9;
        }
        :is(.dark) .dropdown-trigger:hover {
            background: #334155;
        }
        .dropdown-trigger.active {
            box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.2);
        }
        .dropdown-trigger.active svg.transition-transform {
            transform: rotate(180deg);
        }
        
        /* Selected Tags Styles */
        .selected-tags-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            max-width: 90%;
        }
        .selected-tag {
            display: flex;
            align-items: center;
            gap: 0.375rem;
            background: #0d9488;
            color: white;
            padding: 0.25rem 0.625rem;
            border-radius: 0.625rem;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            animation: tagPop 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        @keyframes tagPop {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .selected-tag .remove-tag {
            cursor: pointer;
            width: 12px;
            height: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            font-size: 8px;
            transition: background 0.2s;
        }
        .selected-tag .remove-tag:hover {
            background: rgba(255, 255, 255, 0.4);
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 100; /* Increased z-index */
            margin-top: 0.5rem;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border: 1px solid #f1f5f9;
            overflow: hidden;
            display: none;
            max-height: 280px; /* Slightly reduced */
            flex-direction: column;
        }
        :is(.dark) .dropdown-menu {
            background: #0f172a; /* slate-900 */
            border-color: #1e293b;
        }
        .dropdown-menu.show {
            display: flex;
            animation: dropdownSlideIn 0.2s ease-out;
        }
        @keyframes dropdownSlideIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .dropdown-search-wrapper {
            padding: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }
        :is(.dark) .dropdown-search-wrapper {
            border-color: #1e293b;
        }
        .dropdown-options {
            overflow-y: auto !important;
            max-height: 220px;
            scrollbar-width: thin;
        }
        .dropdown-option {
            padding: 0.75rem 1.25rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .dropdown-option:hover {
            background: #f8fafc;
        }
        :is(.dark) .dropdown-option:hover {
            background: #1e293b;
        }
        .dropdown-option.selected {
            background: rgba(13, 148, 136, 0.05);
        }
        .dropdown-option.selected .check-mark {
            display: block;
        }
        .check-mark {
            display: none;
            color: #0d9488;
        }

        /* Enhanced Availability Loader */
        .availability-loader-overlay {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 1rem;
            gap: 1rem;
        }
        .loading-shimmer {
            width: 100%;
            height: 40px;
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite linear;
            border-radius: 0.75rem;
        }
        :is(.dark) .loading-shimmer {
            background: linear-gradient(90deg, #1e293b 25%, #334155 50%, #1e293b 75%);
            background-size: 200% 100%;
        }
        @keyframes shimmer {
            from { background-position: 200% 0; }
            to { background-position: -200% 0; }
        }
        
        /* Pulse Animation for Loader */
        .pulse-loader {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #0d9488;
            animation: pulse 1.5s infinite cubic-bezier(0.4, 0, 0.6, 1);
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.1); }
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
    
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateField(element) {
            const candidateId = element.dataset.candidateId;
            const fieldName = element.dataset.field;
            let value = element.textContent.trim();

            // Strip commas for salary before saving
            if (fieldName === 'expected_salary') {
                value = value.replace(/,/g, '');
            }

            element.style.backgroundColor = 'rgba(59, 130, 246, 0.1)';

            fetch(`/recruitment/candidate/${candidateId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ field: fieldName, value: value })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    element.style.backgroundColor = 'rgba(34, 197, 94, 0.15)';
                    setTimeout(() => { element.style.backgroundColor = ''; }, 800);
                } else {
                    element.style.backgroundColor = 'rgba(239, 68, 68, 0.15)';
                    const errorMsg = data.error || data.message || 'Failed to update';
                    alert('Error: ' + errorMsg);
                    console.error('Update failed:', data);
                    setTimeout(() => { element.style.backgroundColor = ''; }, 1500);
                }
            })
            .catch(error => {
                element.style.backgroundColor = 'rgba(239, 68, 68, 0.15)';
                alert('Failed to update. Please try again.');
            });
        }

        document.querySelectorAll('select.editable-field').forEach(select => {
            select.addEventListener('change', function() {
                const candidateId = this.dataset.candidateId;
                const fieldName = this.dataset.field;
                const value = this.value;

                this.style.opacity = '0.5';

                fetch(`/recruitment/candidate/${candidateId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ field: fieldName, value: value })
                })
                .then(response => response.json())
                .then(data => {
                    this.style.opacity = '1';
                    if (data.success) {
                        if (data.pipeline_html) {
                            const pipelineCell = document.getElementById(`pipeline-cell-${candidateId}`);
                            if (pipelineCell) pipelineCell.innerHTML = data.pipeline_html;
                        }
                    } else {
                        const errorMsg = data.error || data.message || 'Failed to update';
                        alert('Error: ' + errorMsg);
                        console.error('Update failed:', data);
                    }
                })
                .catch(error => {
                    this.style.opacity = '1';
                    alert('Failed to update. Please try again.');
                });
            });
        });

        // Bulk Selection Logic
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.candidate-checkbox');
        const bulkDeleteForm = document.getElementById('bulk-delete-form');
        const bulkDeleteInputs = document.getElementById('bulk-delete-inputs');
        const selectedCountSpan = document.getElementById('selected-count');

        function updateBulkActions() {
            const selected = Array.from(checkboxes).filter(cb => cb.checked);
            const count = selected.length;
            
            document.querySelectorAll('.selected-count').forEach(span => span.textContent = count);
            if (selectedCountSpan) selectedCountSpan.textContent = count;
            
            if (count > 0 && bulkDeleteForm) {
                bulkDeleteForm.classList.remove('hidden');
                
                // Clear and repopulate hidden inputs
                bulkDeleteInputs.innerHTML = '';
                selected.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_candidates[]';
                    input.value = cb.value;
                    bulkDeleteInputs.appendChild(input);
                });
            } else if (bulkDeleteForm) {
                bulkDeleteForm.classList.add('hidden');
            }
        }

        selectAll?.addEventListener('change', function() {
            checkboxes.forEach(cb => cb.checked = this.checked);
            updateBulkActions();
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkActions);
        });
    </script>
    @endif

    <script>
        // Fix select text color in dark mode (Tailwind dark: classes unreliable on native selects)
        function applySelectColors() {
            const isDark = document.documentElement.classList.contains('dark');
            const color = isDark ? '#f1f5f9' : '#334155';
            document.querySelectorAll('select.editable-field').forEach(function(sel) {
                sel.style.color = color;
            });
        }
        document.addEventListener('DOMContentLoaded', applySelectColors);
        // Keep in sync if user toggles theme
        new MutationObserver(applySelectColors).observe(
            document.documentElement, { attributes: true, attributeFilter: ['class'] }
        );
    </script>

    <script>
        // Upload Modal Functions
        function openUploadModal() {
            closeBulkUploadModal(); // Close bulk if open
            const modal = document.getElementById('uploadModal');
            modal.classList.remove('hidden');
        }

        function closeUploadModal() {
            const modal = document.getElementById('uploadModal');
            modal.classList.add('hidden');
            document.getElementById('uploadForm')?.reset();
            const status = document.getElementById('uploadStatus');
            if (status) status.classList.add('hidden');
        }

        function toggleBulkUploadModal() {
            closeUploadModal(); // Close single if open
            const modal = document.getElementById('bulkUploadModal');
            modal.classList.toggle('hidden');
        }

        function closeBulkUploadModal() {
            const modal = document.getElementById('bulkUploadModal');
            modal.classList.add('hidden');
            document.getElementById('bulkUploadForm')?.reset();
            const status = document.getElementById('bulkUploadStatus');
            if (status) status.classList.add('hidden');
        }

        // Handle clicks outside the dropdowns to close them
        document.addEventListener('mousedown', function(e) {
            const uploadModal = document.getElementById('uploadModal');
            const uploadButton = document.querySelector('button[onclick="openUploadModal()"]');
            const bulkModal = document.getElementById('bulkUploadModal');
            const bulkButton = document.querySelector('button[onclick="toggleBulkUploadModal()"]');
            
            if (uploadModal && !uploadModal.contains(e.target) && !uploadButton.contains(e.target)) {
                closeUploadModal();
            }
            if (bulkModal && !bulkModal.contains(e.target) && !bulkButton.contains(e.target)) {
                closeBulkUploadModal();
            }
        });

        // Handle CV Uploads
        document.addEventListener('DOMContentLoaded', function() {
            // Single Upload
            const uploadForm = document.getElementById('uploadForm');
            const cvFileInput = document.getElementById('cvFile');
            
            cvFileInput?.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const statusDiv = document.getElementById('uploadStatus');
                    statusDiv.classList.remove('hidden');
                    statusDiv.innerHTML = `
                        <div class="flex items-center justify-center">
                            <svg class="w-4 h-4 text-brand-teal animate-spin mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="text-xs text-slate-600 dark:text-slate-300">Processing...</span>
                        </div>
                    `;
                    uploadForm.submit();
                }
            });

            // Bulk Upload
            const bulkUploadForm = document.getElementById('bulkUploadForm');
            const bulkCvFilesInput = document.getElementById('bulkCvFiles');

            bulkCvFilesInput?.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    const statusDiv = document.getElementById('bulkUploadStatus');
                    statusDiv.classList.remove('hidden');
                    statusDiv.innerHTML = `
                        <div class="flex items-center justify-center">
                            <svg class="w-4 h-4 text-brand-teal animate-spin mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span class="text-xs text-slate-600 dark:text-slate-300 italic">Processing ${this.files.length} CVs...</span>
                        </div>
                    `;
                    bulkUploadForm.submit();
                }
            });
        });
    </script>

    <script>
        let currentCandidateId = null;
        let availableTests = [];

        async function openAssessmentModal(candidateId, candidateName) {
            currentCandidateId = candidateId;
            document.getElementById('assessment-candidate-name').textContent = candidateName;
            document.getElementById('assessmentModal').classList.remove('hidden');
            
            // Fetch tests if not already loaded
            if (availableTests.length === 0) {
                try {
                    const response = await fetch('/recruitment/tests-data');
                    availableTests = await response.json();
                    const selector = document.getElementById('test-selector');
                    availableTests.forEach(test => {
                        const option = document.createElement('option');
                        option.value = test.id;
                        option.textContent = test.name;
                        selector.appendChild(option);
                    });
                } catch (error) {
                    console.error('Failed to fetch tests:', error);
                }
            }
        }

        function closeAssessmentModal() {
            document.getElementById('assessmentModal').classList.add('hidden');
            document.getElementById('test-selector').value = '';
            document.getElementById('email-preview-container').classList.add('hidden');
            document.getElementById('send-assessment-btn').disabled = true;
        }

        function updateEmailPreview() {
            const testId = document.getElementById('test-selector').value;
            const previewContainer = document.getElementById('email-preview-container');
            const sendBtn = document.getElementById('send-assessment-btn');
            const candidateName = document.getElementById('assessment-candidate-name').textContent;
            
            if (testId) {
                const test = availableTests.find(t => t.id == testId);
                document.getElementById('preview-subject').textContent = test.subject;
                
                let previewBody = test.content.replace('Dear Candidate', 'Dear ' + candidateName);
                previewBody = previewBody.replace('[Insert Upload Link Here]', '{{ route("assessment.show", "TOKEN") }}');
                
                document.getElementById('preview-body').textContent = previewBody;
                previewContainer.classList.remove('hidden');
                sendBtn.disabled = false;
            } else {
                previewContainer.classList.add('hidden');
                sendBtn.disabled = true;
            }
        }

        async function sendAssessment() {
            const testId = document.getElementById('test-selector').value;
            const sendBtn = document.getElementById('send-assessment-btn');
            
            sendBtn.disabled = true;
            sendBtn.textContent = 'Sending...';

            try {
                const response = await fetch(`/recruitment/candidate/${currentCandidateId}/send-assessment`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ test_id: testId })
                });

                const data = await response.json();
                
                if (data.success) {
                    alert('Assessment task sent successfully!');
                    closeAssessmentModal();
                    window.location.reload(); // Reload to show updated status
                } else {
                    alert('Error: ' + (data.message || 'Failed to send assessment'));
                    sendBtn.disabled = false;
                    sendBtn.textContent = 'Send Task';
                }
            } catch (error) {
                console.error('Error sending assessment:', error);
                alert('An unexpected error occurred. Please try again.');
                sendBtn.disabled = false;
                sendBtn.textContent = 'Send Task';
            }
        }
        
        // Schedule Interview Modal Functions
        let datePicker = null;
        let timePicker = null;
        
        function openScheduleModal(candidateId, candidateName) {
            document.getElementById('schedule-candidate-id').value = candidateId;
            document.getElementById('schedule-candidate-name').textContent = candidateName;
            document.getElementById('scheduleModal').classList.remove('hidden');
            
            // Initialize Flatpickr Date Picker
            if (!datePicker) {
                datePicker = flatpickr("#scheduled_date_input", {
                    dateFormat: "Y-m-d",
                    minDate: "today",
                    static: true, 
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    onChange: function(selectedDates, dateStr, instance) {
                        updateScheduledAt();
                        checkHodAvailability();
                    }
                });
            } else {
                datePicker.set('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            }

            // Initialize Flatpickr Time Picker
            if (!timePicker) {
                timePicker = flatpickr("#scheduled_time_input", {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: "h:i K",
                    time_24hr: false,
                    minuteIncrement: 15,
                    static: true,
                    theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                    onChange: function(selectedDates, dateStr, instance) {
                        updateScheduledAt();
                    }
                });
            } else {
                timePicker.set('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
            }
            
            // Reset fields
            const select = document.getElementById('hod_selector');
            const dateInput = document.getElementById('scheduled_date_input');
            const timeInput = document.getElementById('scheduled_time_input');
            
            // Clear multiple select
            select.selectedIndex = -1;
            Array.from(select.options).forEach(opt => opt.selected = false);
            
            // Clear visual selections
            document.querySelectorAll('.dropdown-option').forEach(opt => {
                opt.classList.remove('selected');
            });
            updateDropdownTrigger();
            
            dateInput.disabled = true;
            dateInput.classList.add('cursor-not-allowed', 'opacity-50');
            dateInput.placeholder = 'Select interviewer(s) first';
            
            timeInput.disabled = true;
            timeInput.classList.add('cursor-not-allowed', 'opacity-50');
            timeInput.placeholder = 'Select date first';
            
            // Clear search
            const searchInput = document.getElementById('dropdown-search');
            if (searchInput) {
                searchInput.value = '';
                filterInterviewers('');
            }
            
            document.getElementById('availability-preview').classList.add('hidden');
            
            // Add listener to duration selector if not exists
            const durSelect = document.querySelector('select[name="duration"]');
            if (durSelect && !durSelect.dataset.hasListener) {
                durSelect.addEventListener('change', checkConflict);
                durSelect.dataset.hasListener = "true";
            }
        }
        
        function updateScheduledAt() {
            const dateStr = document.getElementById('scheduled_date_input').value;
            const timeStr = document.getElementById('scheduled_time_input').value;
            
            if (dateStr && timeStr) {
                const combined = `${dateStr} ${timeStr}`;
                document.getElementById('scheduled_at_hidden').value = combined;
                // No longer trigger checkHodAvailability here to avoid noise before date selection
            }
        }

        function closeScheduleModal() {
            document.getElementById('scheduleModal').classList.add('hidden');
            if (datePicker) datePicker.clear();
            if (timePicker) timePicker.clear();
        }

        // Multi-interviewer UI Logic
        function toggleInterviewerSelection(id, el) {
            const select = document.getElementById('hod_selector');
            const option = select.querySelector(`option[value="${id}"]`);
            
            option.selected = !option.selected;
            if (el) el.classList.toggle('selected');
            
            updateDropdownTrigger();
            
            // Trigger availability check
            checkHodAvailability();
        }

        function updateDropdownTrigger() {
            const select = document.getElementById('hod_selector');
            const selectedOptions = Array.from(select.options).filter(opt => opt.selected);
            const container = document.getElementById('selected-tags-wrapper');
            const placeholder = document.getElementById('dropdown-placeholder');
            
            container.innerHTML = '';
            
            if (selectedOptions.length === 0) {
                placeholder.classList.remove('hidden');
                container.classList.add('hidden');
            } else {
                placeholder.classList.add('hidden');
                container.classList.remove('hidden');
                
                selectedOptions.forEach(opt => {
                    const tag = document.createElement('div');
                    tag.className = 'selected-tag';
                    tag.innerHTML = `
                        <span>${opt.getAttribute('data-name')}</span>
                        <span class="remove-tag" onclick="removeTag('${opt.value}', event)">✕</span>
                    `;
                    container.appendChild(tag);
                });
            }
        }

        function removeTag(id, event) {
            if (event) event.stopPropagation();
            const select = document.getElementById('hod_selector');
            const option = select.querySelector(`option[value="${id}"]`);
            const dropdownOption = document.querySelector(`.dropdown-option[data-id="${id}"]`);
            
            if (option) option.selected = false;
            if (dropdownOption) dropdownOption.classList.remove('selected');
            
            updateDropdownTrigger();
            checkHodAvailability();
        }

        function toggleDropdown() {
            const menu = document.getElementById('interviewer-dropdown-menu');
            const trigger = document.getElementById('interviewer-dropdown-trigger');
            const isOpen = menu.classList.contains('show');
            
            // Close all other dropdowns if any
            
            if (isOpen) {
                menu.classList.remove('show');
                trigger.classList.remove('active');
            } else {
                menu.classList.add('show');
                trigger.classList.add('active');
                document.getElementById('dropdown-search').focus();
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const container = document.querySelector('.dropdown-container');
            if (container && !container.contains(e.target)) {
                document.getElementById('interviewer-dropdown-menu').classList.remove('show');
                document.getElementById('interviewer-dropdown-trigger').classList.remove('active');
            }
        });

        function filterInterviewers(query) {
            const options = document.querySelectorAll('.dropdown-option');
            const sections = document.querySelectorAll('.role-group-section');
            query = query.toLowerCase();

            options.forEach(option => {
                const name = option.querySelector('.opt-name').textContent.toLowerCase();
                const role = option.querySelector('.opt-role').textContent.toLowerCase();
                if (name.includes(query) || role.includes(query)) {
                    option.style.display = 'flex';
                } else {
                    option.style.display = 'none';
                }
            });

            sections.forEach(section => {
                const visibleOptions = Array.from(section.querySelectorAll('.dropdown-option')).filter(o => o.style.display !== 'none');
                section.style.display = visibleOptions.length > 0 ? 'block' : 'none';
            });
        }

        // Multi-interviewer Availability Logic
        let allAvailData = {}; // Store events keyed by interviewer name

        async function checkHodAvailability() {
            const selector = document.getElementById('hod_selector');
            const selectedOptions = Array.from(selector.options).filter(opt => opt.selected);
            const dateInput = document.getElementById('scheduled_date_input');
            const timeInput = document.getElementById('scheduled_time_input');
            const dateStr = dateInput.value;
            
            const previewBox = document.getElementById('availability-preview');
            const contentBox = document.getElementById('avail-content');
            const loader = document.getElementById('avail-loader');
            const errorBox = document.getElementById('avail-error');
            const dateLabel = document.getElementById('avail-date-label');

            // Reset UI & Data
            contentBox.innerHTML = '';
            errorBox.classList.add('hidden');
            errorBox.textContent = '';
            allAvailData = {};
            
            if (selectedOptions.length === 0) {
                previewBox.classList.add('hidden');
                dateInput.disabled = true;
                dateInput.classList.add('cursor-not-allowed', 'opacity-50');
                timeInput.disabled = true;
                timeInput.classList.add('cursor-not-allowed', 'opacity-50');
                return;
            }

            // Enable Date picker
            dateInput.disabled = false;
            dateInput.classList.remove('cursor-not-allowed', 'opacity-50');
            dateInput.classList.add('cursor-pointer');
            dateInput.placeholder = 'Select date';

            if (!dateStr) {
                previewBox.classList.remove('hidden'); // Keep visible to show status
                dateLabel.textContent = 'PENDING';
                contentBox.innerHTML = `
                    <div class="col-span-full border-2 border-dashed border-slate-100 dark:border-slate-800 rounded-2xl p-8 text-center bg-white/50 dark:bg-slate-900/30">
                        <div class="w-12 h-12 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-relaxed">Please select an interview date<br/>to check interviewer availability</p>
                    </div>
                `;
                timeInput.disabled = true;
                timeInput.classList.add('cursor-not-allowed', 'opacity-50');
                return;
            }

            // Enable Time picker
            timeInput.disabled = false;
            timeInput.classList.remove('cursor-not-allowed', 'opacity-50');
            timeInput.classList.add('cursor-pointer');
            timeInput.placeholder = 'Select time';
            
            previewBox.classList.remove('hidden');
            dateLabel.textContent = dateStr;
            loader.classList.remove('hidden');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                // Fetch availability for all selected interviewers in parallel
                const results = await Promise.all(selectedOptions.map(async (option) => {
                    const response = await fetch('/recruitment/check-availability', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ hod_id: option.value, date: dateStr })
                    });
                    const data = await response.json();
                    const nameAttr = option.getAttribute('data-name');
                    const roleAttr = option.getAttribute('data-role');
                    const textParts = option.text.split('(');
                    
                    const name = (nameAttr && nameAttr.trim() !== '') ? nameAttr : textParts[0].trim();
                    const role = (roleAttr && roleAttr.trim() !== '') ? roleAttr : (textParts[1] ? textParts[1].replace(')', '').trim() : 'Interviewer');
                    
                    return { 
                        id: option.value, 
                        name: name, 
                        role: role,
                        data 
                    };
                }));

                loader.classList.add('hidden');
                
                const isDark = document.documentElement.classList.contains('dark');
                
                const busySlots = [];

                 results.forEach(res => {
                    const card = document.createElement('div');
                    card.className = isDark 
                        ? 'bg-slate-800 p-5 rounded-2xl border border-slate-700 shadow-sm transition-all hover:shadow-md'
                        : 'bg-white p-5 rounded-2xl border border-slate-100 shadow-sm transition-all hover:shadow-md';
                    
                    const displayName = res.name && res.name.trim() !== '' ? res.name : 'Interviewer';
                    const displayRole = res.role && res.role.trim() !== '' ? res.role : '';

                    let html = `
                        <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-teal to-teal-600 flex items-center justify-center text-white font-black shadow-lg shadow-brand-teal/20">
                                    ${displayName.charAt(0)}
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-brand-navy dark:text-white uppercase tracking-tighter leading-tight">${displayName}</h4>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">${displayRole}</p>
                                </div>
                            </div>
                        </div>
                    `;

                    if (res.data.error) {
                         html += `<div class="text-[9px] text-red-500 italic ${isDark ? 'bg-red-900/10 border-red-900/20' : 'bg-red-50 border-red-50'} p-2 rounded-lg border">${res.data.error}</div>`;
                    } else if (res.data.success && res.data.events) {
                        const events = res.data.events;
                        
                        // Collect busy slots
                        events.forEach(ev => {
                            const start = new Date(ev.start);
                            const end = new Date(ev.end);
                            
                            // Create time strings (HH:mm) for Flatpickr time-only disable
                            const fromTime = start.getHours().toString().padStart(2, '0') + ':' + start.getMinutes().toString().padStart(2, '0');
                            const toTime = end.getHours().toString().padStart(2, '0') + ':' + end.getMinutes().toString().padStart(2, '0');

                            busySlots.push({
                                from: fromTime,
                                to: toTime
                            });
                        });
                        
                        allAvailData[res.name] = events;
                        
                        if (events.length === 0) {
                             html += `<div class="flex items-center gap-2 text-[9px] text-emerald-500 font-bold ${isDark ? 'bg-emerald-900/10 border-emerald-900/20' : 'bg-emerald-50 border-emerald-50'} p-2 rounded-lg border">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Fully Available
                            </div>`;
                        } else {
                            html += `<div class="space-y-1.5">`;
                             events.forEach(ev => {
                                const start = new Date(ev.start).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                                const end = new Date(ev.end).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                                html += `<div class="flex items-center justify-between text-[9px] ${isDark ? 'bg-slate-900 border-slate-700' : 'bg-slate-50 border-slate-100'} px-4 py-3 rounded-xl border">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-1.5 h-1.5 rounded-full bg-amber-400 shadow-[0_0_10px_rgba(251,191,36,0.5)] flex-shrink-0"></div>
                                        <span class="font-black ${isDark ? 'text-cyan-400' : 'text-slate-700'} tracking-tight truncated">${start} - ${end}</span>
                                    </div>
                                    <span class="text-[8px] ${isDark ? 'text-slate-300' : 'text-slate-400'} font-black uppercase tracking-widest italic ml-2 flex-shrink-0">Busy</span>
                                </div>`;
                            });
                            html += `</div>`;
                        }
                    }
                    
                    card.innerHTML = html;
                    contentBox.appendChild(card);
                });

                // Update Time Picker with busy slots
                const timePicker = document.getElementById('scheduled_time_input')._flatpickr;
                if (timePicker) {
                    timePicker.set('disable', busySlots);
                }

                checkConflict(); // Final check

            } catch (error) {
                console.error('Multi-availability check error:', error);
                loader.classList.add('hidden');
                errorBox.textContent = 'Network error: Failed to fetch availability data for one or more interviewers.';
                errorBox.classList.remove('hidden');
            }
        }

        function checkConflict() {
            const dateTimeStr = document.getElementById('scheduled_at_hidden').value;
            const duration = parseInt(document.querySelector('select[name="duration"]').value) || 30;
            const errorBox = document.getElementById('avail-error');
            
            if (!dateTimeStr) return;

            const selectedStart = new Date(dateTimeStr);
            const selectedEnd = new Date(selectedStart.getTime() + duration * 60000);
            
            let conflicts = [];

            Object.entries(allAvailData).forEach(([name, events]) => {
                const hasOverlap = events.some(ev => {
                    const evStart = new Date(ev.start);
                    const evEnd = new Date(ev.end);
                    return (selectedStart < evEnd && selectedEnd > evStart);
                });
                
                if (hasOverlap) {
                    conflicts.push(name);
                }
            });

            if (conflicts.length > 0) {
                errorBox.innerHTML = `⚠️ Conflict: ${conflicts.join(', ')} is busy at this time.`;
                errorBox.classList.remove('hidden');
            } else {
                errorBox.classList.add('hidden');
                errorBox.textContent = '';
            }
        }

    </script>
    <!-- Update form to check for conflicts on submit -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scheduleForm = document.querySelector('#scheduleModal form');
            if (scheduleForm) {
                scheduleForm.addEventListener('submit', function(event) {
                    // First, check if time is selected (existing validation)
                    if (!document.getElementById('scheduled_time_input').value) {
                        alert('Please select a time for the interview');
                        event.preventDefault();
                        return;
                    }

                    // Then, check for conflicts
                    checkConflict();
                    const errorBox = document.getElementById('avail-error');
                    if (!errorBox.classList.contains('hidden')) {
                        alert('Cannot schedule interview due to interviewer conflicts. Please adjust the time or interviewers.');
                        event.preventDefault();
                    }
                });
            }
        });
    </script>
    @push('modals')
    <!-- Feedback Modal -->
    <div id="feedbackModal" class="hidden fixed inset-0 z-[9999999] overflow-y-auto" style="z-index: 9999999 !important;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" aria-hidden="true" onclick="closeFeedbackModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-100 dark:border-slate-800 relative z-[99] shadow-[0_0_50px_rgba(0,0,0,0.5)]">
                <div class="px-10 py-8 border-b border-slate-100 dark:border-slate-800 flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-black text-brand-navy dark:text-white uppercase tracking-tighter">Candidate Feedback</h3>
                        <p class="text-xs text-slate-400 uppercase tracking-widest mt-1">Reviewer: {{ auth()->user()->name }}</p>
                    </div>
                    <button onclick="closeFeedbackModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors mt-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-8">
                    <input type="hidden" id="feedback-candidate-id">
                    
                    <!-- Previous Feedbacks Feed -->
                    <div class="mb-6">
                        <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3">Previous Feedbacks</label>
                        <div id="previous-feedbacks-container" class="max-h-[250px] overflow-y-auto space-y-3 pr-2 custom-scrollbar">
                            <!-- Populated dynamically -->
                        </div>
                    </div>

                    <!-- New Feedback TextArea -->
                    <div class="border-t border-slate-100 dark:border-slate-800 pt-6">
                        <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3">Add Feedback</label>
                        <textarea id="feedback-textarea" rows="4" 
                            class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-brand-teal/20 transition-all text-slate-700 dark:text-white placeholder:text-slate-400 resize-none"
                            placeholder="Type your feedback here..."></textarea>
                    </div>
                </div>
                <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3 rounded-b-3xl">
                    <button onclick="closeFeedbackModal()" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">Cancel</button>
                    <button onclick="saveFeedback()" class="px-8 py-3 bg-brand-navy dark:bg-brand-teal text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:opacity-90 transition-all shadow-lg active:scale-95">Save Feedback</button>
                </div>
            </div>
        </div>
    </div>

    <!-- View Single Feedback Modal -->
    <div id="viewFeedbackModal" class="hidden fixed inset-0 z-[99999999] overflow-y-auto" style="z-index: 99999999 !important;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-md transition-opacity" aria-hidden="true" onclick="closeViewFeedbackModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-800 relative z-[99] shadow-[0_0_50px_rgba(0,0,0,0.5)]">
                <div class="px-10 py-8 border-b border-slate-100 dark:border-slate-800 flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-black text-brand-navy dark:text-white uppercase tracking-tighter" id="view-feedback-user-name">Feedback Details</h3>
                        <p class="text-xs text-slate-400 uppercase tracking-widest mt-1" id="view-feedback-user-role-date"></p>
                    </div>
                    <button onclick="closeViewFeedbackModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors mt-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-8">
                    <div id="view-feedback-content" class="text-sm text-slate-700 dark:text-slate-200 leading-relaxed whitespace-pre-wrap max-h-[400px] overflow-y-auto custom-scrollbar"></div>
                </div>
                <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3 rounded-b-3xl">
                    <button onclick="closeViewFeedbackModal()" class="px-8 py-3 bg-brand-navy dark:bg-brand-teal text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:opacity-90 transition-all shadow-lg active:scale-95">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Portfolio Modal -->
    <div id="portfolioModal" class="hidden fixed inset-0 z-[9999999] flex items-center justify-center px-4" style="z-index: 9999999 !important;" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" aria-hidden="true" onclick="closePortfolioModal()"></div>
        <div class="relative bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-xs border border-slate-100 dark:border-slate-800 z-10">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-800">
                <h3 class="text-[11px] font-black text-brand-navy dark:text-white uppercase tracking-widest">Portfolio Link</h3>
                <button onclick="closePortfolioModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="p-5 space-y-3">
                <input type="hidden" id="portfolio-candidate-id">
                <div id="portfolio-open-row" class="hidden">
                    <a id="portfolio-open-link" href="#" target="_blank"
                        class="flex items-center gap-2 px-4 py-2.5 bg-brand-teal/10 text-brand-teal rounded-xl text-[11px] font-bold uppercase tracking-wider hover:bg-brand-teal/20 transition-all w-full justify-center">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Open Portfolio
                    </a>
                </div>
                @if(auth()->user()->isAdmin() || auth()->user()->isHR())
                <input type="url" id="portfolio-url-input"
                    class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-2.5 text-xs focus:ring-2 focus:ring-brand-teal/20 focus:border-brand-teal/40 transition-all text-slate-700 dark:text-white placeholder:text-slate-400"
                    placeholder="https://...">
                @endif
            </div>
            <div class="flex justify-end gap-2 px-5 py-3 border-t border-slate-100 dark:border-slate-800">
                <button onclick="closePortfolioModal()" class="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">Cancel</button>
                @if(auth()->user()->isAdmin() || auth()->user()->isHR())
                <button onclick="savePortfolio()" class="px-5 py-2 bg-brand-teal text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:opacity-90 transition-all active:scale-95">Save</button>
                @endif
            </div>
        </div>
    </div>

    <!-- Assessment Modal -->
    <div id="assessmentModal" class="hidden fixed inset-0 z-[9999999] overflow-y-auto" style="z-index: 9999999 !important;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" aria-hidden="true" onclick="closeAssessmentModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-100 dark:border-slate-800 relative z-[99] shadow-[0_0_50px_rgba(0,0,0,0.5)]">
                <div class="px-10 py-8 border-b border-slate-100 dark:border-slate-800 flex justify-between items-start">
                    <div>
                        <h3 class="text-xl font-black text-brand-navy dark:text-white uppercase tracking-tighter" id="modal-title">Send Assessment Task</h3>
                        <p class="text-xs text-slate-400 uppercase tracking-widest mt-1">Recipient: <span id="assessment-candidate-name" class="text-brand-teal font-bold underline decoration-brand-teal/30 underline-offset-4"></span></p>
                    </div>
                    <button onclick="closeAssessmentModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors mt-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <div class="p-8">
                    <div class="mb-6">
                        <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Select Assessment Test</label>
                        <select id="test-selector" onchange="updateEmailPreview()" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-brand-teal/20 transition-all text-slate-700 dark:text-white">
                            <option value="">Choose a test...</option>
                        </select>
                    </div>
                    
                    <div id="email-preview-container" class="hidden">
                        <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Email Preview</label>
                        <div class="bg-slate-50 dark:bg-slate-800 rounded-2xl p-6 border border-slate-100 dark:border-slate-700">
                            <div class="mb-4 pb-4 border-b border-slate-200 dark:border-slate-700">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mr-2">Subject:</span>
                                <span id="preview-subject" class="text-xs font-bold text-brand-navy dark:text-white"></span>
                            </div>
                            <div id="preview-body" class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed whitespace-pre-wrap italic"></div>
                        </div>
                    </div>
                </div>
                <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3 rounded-b-3xl">
                    <button onclick="closeAssessmentModal()" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">Cancel</button>
                    <button id="send-assessment-btn" onclick="sendAssessment()" class="px-8 py-3 bg-brand-navy dark:bg-brand-teal text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:opacity-90 transition-all shadow-lg active:scale-95 disabled:opacity-50" disabled>Send Task</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Schedule Interview Modal -->
    <div id="scheduleModal" class="hidden fixed inset-0 z-[9999999] overflow-y-auto" style="z-index: 9999999 !important;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" aria-hidden="true" onclick="closeScheduleModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-100 dark:border-slate-800 relative z-[99] shadow-[0_0_50px_rgba(0,0,0,0.5)]">
                <form action="{{ route('recruitment.scheduleInterview') }}" method="POST" onsubmit="
                    if(!document.getElementById('scheduled_time_input').value) { alert('Please select a time for the interview'); return false; }
                    if(!document.getElementById('avail-error').classList.contains('hidden')) { alert('Please select a different time. One or more interviewers are busy.'); return false; }
                ">
                    @csrf
                    <input type="hidden" name="candidate_id" id="schedule-candidate-id">
                    
                    <div class="px-10 py-8 border-b border-slate-100 dark:border-slate-800 flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-black text-brand-navy dark:text-white uppercase tracking-tighter" id="modal-title">Schedule Interview</h3>
                            <p class="text-xs text-slate-400 uppercase tracking-widest mt-1">Candidate: <span id="schedule-candidate-name" class="text-brand-teal font-bold underline decoration-brand-teal/30 underline-offset-4"></span></p>
                        </div>
                        <button type="button" onclick="closeScheduleModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors mt-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    
                    <div class="p-8 space-y-6">
                        <!-- Date & Time -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Date</label>
                                <input type="text" id="scheduled_date_input" placeholder="Select HOD first" disabled class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-brand-teal/20 transition-all text-slate-700 dark:text-white cursor-not-allowed opacity-50">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Time</label>
                                <input type="text" id="scheduled_time_input" placeholder="Select date first" disabled class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-brand-teal/20 transition-all text-slate-700 dark:text-white cursor-not-allowed opacity-50">
                            </div>
                            <input type="hidden" name="scheduled_at" id="scheduled_at_hidden" required>
                        </div>

                        <!-- Interviewers (Searchable Dropdown) -->
                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3 block">Select Interviewers</label>
                            
                            <div class="dropdown-container">
                                <div id="interviewer-dropdown-trigger" class="dropdown-trigger" onclick="toggleDropdown()">
                                    <div class="flex items-center gap-3 min-w-0 w-full">
                                        <div class="w-8 h-8 rounded-full bg-brand-teal/10 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-brand-teal" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                        <div class="flex flex-col min-w-0 w-full">
                                            <span id="dropdown-placeholder" class="text-xs font-bold text-slate-400 dark:text-slate-500">Choose interviewers...</span>
                                            <div id="selected-tags-wrapper" class="selected-tags-container hidden">
                                                <!-- Tags injected here -->
                                            </div>
                                        </div>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-300 flex-shrink-0 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                                </div>

                                <div id="interviewer-dropdown-menu" class="dropdown-menu">
                                    <div class="dropdown-search-wrapper">
                                        <div class="relative">
                                            <input type="text" id="dropdown-search" placeholder="Search by name or role..." onkeyup="filterInterviewers(this.value)" 
                                                class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-xl px-4 py-3 text-xs focus:ring-1 focus:ring-brand-teal transition-all text-slate-700 dark:text-white placeholder:text-slate-400">
                                            <svg class="w-3.5 h-3.5 absolute right-3 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                        </div>
                                    </div>
                                    
                                    <div class="dropdown-options custom-scrollbar">
                                        @php
                                            $groupedHods = $hods->groupBy('role');
                                            $order = [\App\Models\User::ROLE_HOD, \App\Models\User::ROLE_MANAGER, \App\Models\User::ROLE_MANAGERS];
                                        @endphp

                                        @foreach($order as $role)
                                            @if(isset($groupedHods[$role]))
                                                <div class="role-group-section mt-2">
                                                    <h5 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1 px-4 py-1 bg-slate-50/50 dark:bg-slate-800/30">{{ $role }}s</h5>
                                                    @foreach($groupedHods[$role] as $hod)
                                                        <div class="dropdown-option group" 
                                                            data-id="{{ $hod->id }}" 
                                                            onclick="toggleInterviewerSelection('{{ $hod->id }}', this); event.stopPropagation();">
                                                            <div class="flex items-center gap-3">
                                                                <div class="w-2 h-2 rounded-full bg-slate-200 dark:bg-slate-700 opt-indicator transition-all"></div>
                                                                <div class="flex flex-col">
                                                                    <span class="text-[11px] font-bold text-slate-700 dark:text-slate-200 opt-name tracking-tight">{{ $hod->name }}</span>
                                                                    <span class="text-[9px] text-slate-400 uppercase tracking-widest opt-role">{{ $hod->role }}</span>
                                                                </div>
                                                            </div>
                                                            <svg class="w-4 h-4 check-mark" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Multiple Select for Form Submission -->
                            <select name="interviewer_ids[]" id="hod_selector" required multiple class="hidden">
                                @foreach($hods as $hod)
                                    <option value="{{ $hod->id }}" 
                                            data-email="{{ $hod->email }}"
                                            data-name="{{ $hod->name }}"
                                            data-role="{{ $hod->role }}">{{ $hod->name }} ({{ $hod->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Duration -->
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Duration</label>
                            <select name="duration" required class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-brand-teal/20 transition-all text-slate-700 dark:text-white">
                                <option value="30">30 Minutes</option>
                                <option value="45">45 Minutes</option>
                                <option value="60">1 Hour</option>
                            </select>
                        </div>

                         <!-- Availability Preview -->
                        <div id="availability-preview" class="hidden bg-slate-50 dark:bg-slate-800 rounded-3xl p-6 border border-slate-100 dark:border-slate-700">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Interviewer Availability (<span id="avail-date-label"></span>)</h4>
                            </div>
                            <div id="avail-loader" class="hidden">
                                <div class="availability-loader-overlay">
                                    <div class="pulse-loader"></div>
                                    <p class="text-[10px] font-black text-brand-teal uppercase tracking-widest animate-pulse">Syncing Schedules...</p>
                                    <div class="w-full max-w-xs space-y-3 mt-4">
                                        <div class="loading-shimmer"></div>
                                        <div class="loading-shimmer" style="width: 80%"></div>
                                    </div>
                                </div>
                            </div>
                            <div id="avail-content" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-60 overflow-y-auto custom-scrollbar p-1">
                                <!-- Individual availability cards injected here -->
                            </div>
                            <div id="avail-error" class="hidden text-[10px] text-red-500 font-bold uppercase tracking-wider mt-3 p-3 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-100 dark:border-red-900/30"></div>
                        </div>

                        <!-- Custom Message -->
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Custom Message (Optional)</label>
                            <textarea name="custom_message" rows="4" placeholder="Add a personalized note to the invitation..." class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-brand-teal/20 transition-all text-slate-700 dark:text-white placeholder:text-slate-400 resize-none custom-scrollbar"></textarea>
                        </div>

                        <!-- Guests -->
                        <div>
                            <label class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Additional Guests</label>
                            <input type="text" name="additional_guests" placeholder="email1@example.com, email2@example.com" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-brand-teal/20 transition-all text-slate-700 dark:text-white placeholder:text-slate-400">
                        </div>
                    </div>

                    <div class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 flex justify-end gap-3 rounded-b-3xl">
                        <button type="button" onclick="closeScheduleModal()" class="px-6 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">Cancel</button>
                        <button type="submit" class="px-8 py-3 bg-brand-navy dark:bg-brand-teal text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:opacity-90 transition-all shadow-lg active:scale-95">Schedule & Send Invite</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Rejection Modal -->
    <div id="rejectionModal" class="fixed inset-0 hidden overflow-y-auto" style="z-index: 999999 !important;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" style="z-index: -1;" aria-hidden="true" onclick="closeRejectionModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100 dark:border-slate-800 relative shadow-[0_0_50px_rgba(0,0,0,0.5)]">
                <input type="hidden" id="rejection-candidate-id">
                <div class="bg-white dark:bg-slate-900 px-8 py-8 pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg leading-6 font-black text-brand-navy dark:text-white uppercase tracking-tighter" id="modal-title">
                                Send Rejection Email
                            </h3>
                            <div class="mt-6">
                                <label for="rejection-textarea" class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2">Message Content</label>
                                <textarea id="rejection-textarea" rows="12" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-red-500/20 transition-all text-slate-700 dark:text-white placeholder:text-slate-400 resize-none custom-scrollbar">Dear [Candidate Name],

Thank you for your interest in joining the team at Loops Integrated and for the time you invested in your application.

After careful consideration, we have decided not to move forward with your application for this position at this time. Our decision was made based on our current requirements and the high volume of applications we received.

We appreciate the opportunity to review your profile and wish you the very best in your future endeavors. Your details will be kept in our database for future opportunities that may be a better match for your skill set.</textarea>
                                <p class="mt-2 text-[10px] text-slate-400 font-bold uppercase tracking-wide">You can edit the message above before sending.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-8 py-6 flex flex-col sm:flex-row justify-center gap-3 rounded-b-3xl">
                    <button type="button" onclick="confirmSendRejection()" class="w-full sm:w-1/2 flex items-center justify-center rounded-xl border border-transparent shadow-lg px-6 py-3 bg-red-600 text-[10px] font-black uppercase tracking-widest text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all active:scale-95 shadow-red-500/30 h-[56px]">
                        Send Email
                    </button>
                    <button type="button" onclick="closeRejectionModal()" class="w-full sm:w-1/2 flex items-center justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-white dark:bg-slate-700 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-slate-700 dark:text-slate-300 dark:hover:text-white focus:outline-none transition-colors h-[56px]">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endpush

    <script>
        function openRejectionModal(candidateId, candidateName) {
            document.getElementById('rejection-candidate-id').value = candidateId;
            let textarea = document.getElementById('rejection-textarea');
            
            let templateContent = `@if($rejectionTemplate){!! str_replace(["\r", "\n"], ['\r', '\n'], addslashes($rejectionTemplate->content)) !!}@else Dear [Candidate Name],

Thank you for your interest in joining the team at Loops Integrated and for the time you invested in your application.

After careful consideration, we have decided not to move forward with your application for this position at this time. Our decision was made based on our current requirements and the high volume of applications we received.

We appreciate the opportunity to review your profile and wish you the very best in your future endeavors. Your details will be kept in our database for future opportunities that may be a better match for your skill set.@endif`;

            let defaultMsg = templateContent.replace('[Candidate Name]', candidateName);
            
            textarea.value = defaultMsg;
            
            document.getElementById('rejectionModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRejectionModal() {
            document.getElementById('rejectionModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        async function confirmSendRejection() {
            const candidateId = document.getElementById('rejection-candidate-id').value;
            const message = document.getElementById('rejection-textarea').value;
            const btn = document.querySelector('#rejectionModal button[onclick="confirmSendRejection()"]');
            
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Sending...';

            try {
                const response = await fetch(`/recruitment/candidate/${candidateId}/send-rejection`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ rejection_message: message })
                });

                const data = await response.json();
                
                if (data.success) {
                    closeRejectionModal();
                    // Optional: Show success toast
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to send rejection email'));
                }
            } catch (error) {
                console.error('Error sending rejection:', error);
                alert('An error occurred. Please try again.');
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        }

        function sendRejection(candidateId, candidateName) {
            openRejectionModal(candidateId, candidateName);
        }

        const CURRENT_USER = {
            id: {{ auth()->user()->id }},
            role: '{{ auth()->user()->role }}',
            is_super_admin: {{ auth()->user()->is_super_admin ? 'true' : 'false' }}
        };

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function escapeHtml(str) {
            if (!str) return '';
            return str
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        async function updateTableBadge(candidateId) {
            try {
                const response = await fetch(`/recruitment/candidate/${candidateId}/feedbacks`);
                const data = await response.json();
                if (data.success) {
                    const feedbacks = data.feedbacks;
                    const trigger = document.querySelector(`.feedback-trigger[data-candidate-id="${candidateId}"]`);
                    if (trigger) {
                        const parentCell = trigger.closest('td');
                        let tooltip = parentCell.querySelector('.feedback-tooltip');

                        if (feedbacks.length > 0) {
                            trigger.className = trigger.className
                                .replace(/bg-slate-100[^\s]*/g, '')
                                .replace(/dark:bg-slate-800[^\s]*/g, '')
                                .replace(/text-slate-400[^\s]*/g, '')
                                .replace(/dark:text-slate-600[^\s]*/g, '');
                            trigger.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400', 'dark:text-slate-600', 'hover:text-brand-teal', 'hover:bg-brand-teal/10');
                            trigger.classList.add('bg-brand-teal/20', 'text-brand-teal', 'ring-1', 'ring-brand-teal/30', 'hover:bg-brand-teal/30');
                            trigger.title = `${feedbacks.length} Feedback(s)`;

                            // Build the tooltip content dynamically
                            let listHtml = '';
                            feedbacks.forEach(f => {
                                listHtml += `
                                    <div class="text-[10px] leading-tight">
                                        <div class="flex justify-between items-center gap-1 font-bold text-slate-300">
                                            <span>${escapeHtml(f.user.name)}</span>
                                            <span class="text-[8px] text-slate-500 font-medium">${formatDate(f.created_at)}</span>
                                        </div>
                                        <p class="text-slate-400 font-medium mt-0.5 line-clamp-2">${escapeHtml(f.feedback)}</p>
                                    </div>
                                `;
                            });

                            const tooltipHtml = `
                                <p class="text-[9px] font-black uppercase tracking-wider text-brand-teal mb-2 border-b border-slate-800 pb-1">Previous Feedbacks (${feedbacks.length})</p>
                                <div class="space-y-2 max-h-40 overflow-y-auto custom-scrollbar">
                                    ${listHtml}
                                </div>
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900/95 dark:border-t-slate-950/95"></div>
                            `;

                            if (!tooltip) {
                                tooltip = document.createElement('div');
                                tooltip.className = 'feedback-tooltip pointer-events-none opacity-0 group-hover/tooltip:opacity-100 transition-opacity duration-200 absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-64 bg-slate-900/95 dark:bg-slate-950/95 backdrop-blur-md rounded-xl p-3 shadow-xl border border-slate-800/80 text-left z-50';
                                parentCell.appendChild(tooltip);
                            }
                            tooltip.innerHTML = tooltipHtml;
                            tooltip.classList.remove('hidden');
                        } else {
                            trigger.classList.remove('bg-brand-teal/20', 'text-brand-teal', 'ring-1', 'ring-brand-teal/30', 'hover:bg-brand-teal/30');
                            trigger.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400', 'dark:text-slate-600', 'hover:text-brand-teal', 'hover:bg-brand-teal/10');
                            trigger.title = 'Add feedback';
                            if (tooltip) {
                                tooltip.classList.add('hidden');
                            }
                        }
                    }
                }
            } catch (e) {
                console.error('Error updating badge:', e);
            }
        }

        async function loadFeedbacks(candidateId) {
            const container = document.getElementById('previous-feedbacks-container');
            container.innerHTML = '<div class="text-xs font-semibold text-slate-400 py-4 text-center italic">Loading feedbacks...</div>';
            
            try {
                const response = await fetch(`/recruitment/candidate/${candidateId}/feedbacks`);
                const data = await response.json();
                
                if (data.success) {
                    const feedbacks = data.feedbacks;
                    if (feedbacks.length === 0) {
                        container.innerHTML = '<div class="text-xs font-semibold text-slate-400 py-6 text-center italic">No feedback given yet. Be the first to leave a comment!</div>';
                        return;
                    }
                    
                    container.innerHTML = '';
                    feedbacks.forEach(feedback => {
                        const isSuperAdmin = CURRENT_USER.role === 'Super Admin' || CURRENT_USER.is_super_admin;
                        const canModify = isSuperAdmin || CURRENT_USER.id === feedback.user_id;
                        
                        let actionsHtml = '';
                        if (canModify) {
                            actionsHtml = `
                                <div class="flex items-center gap-1.5 ml-2">
                                    <button onclick="editFeedbackInline(${feedback.id}, this)" class="text-slate-400 hover:text-brand-teal p-1 transition-colors" title="Edit Feedback">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button onclick="deleteFeedback(${feedback.id}, this)" class="text-slate-400 hover:text-red-500 p-1 transition-colors" title="Delete Feedback">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            `;
                        }
                        
                        const itemHtml = `
                            <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4 border border-slate-100 dark:border-slate-800/80 relative group/item">
                                <div class="flex items-center justify-between mb-1.5">
                                    <div class="flex items-center gap-2">
                                        <span class="feedback-user-name text-xs font-black text-brand-navy dark:text-white">${escapeHtml(feedback.user.name)}</span>
                                        <span class="feedback-user-role text-[8px] font-bold tracking-widest uppercase px-2 py-0.5 rounded-md bg-brand-teal/10 text-brand-teal">${escapeHtml(feedback.user.role)}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="feedback-date text-[9px] text-slate-400 font-medium">${formatDate(feedback.created_at)}</span>
                                        ${actionsHtml}
                                    </div>
                                </div>
                                <p onclick="openViewFeedbackModal(this)"
                                   data-feedback-full="${escapeHtml(feedback.feedback)}"
                                   class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-wrap cursor-pointer hover:text-brand-teal transition-colors"
                                   title="Click to view full feedback">${escapeHtml(feedback.feedback)}</p>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', itemHtml);
                    });
                } else {
                    container.innerHTML = '<div class="text-xs font-semibold text-red-500 py-4 text-center">Failed to load feedbacks.</div>';
                }
            } catch (error) {
                console.error('Load feedbacks error:', error);
                container.innerHTML = '<div class="text-xs font-semibold text-red-500 py-4 text-center">An error occurred loading feedbacks.</div>';
            }
        }

        function editFeedbackInline(feedbackId, buttonEl) {
            const itemEl = buttonEl.closest('.group\\/item');
            const contentEl = itemEl.querySelector('p');
            const originalText = contentEl.textContent.trim();
            
            if (itemEl.classList.contains('is-editing')) return;
            itemEl.classList.add('is-editing');
            
            const textarea = document.createElement('textarea');
            textarea.className = 'w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl px-4 py-3 text-xs text-slate-700 dark:text-white placeholder:text-slate-400 focus:ring-1 focus:ring-brand-teal focus:border-brand-teal resize-none mb-2 mt-2';
            textarea.rows = 4;
            textarea.value = originalText;
            
            const saveBtn = document.createElement('button');
            saveBtn.className = 'px-3 py-1.5 bg-brand-teal text-white rounded-lg text-[9px] font-bold uppercase tracking-wider hover:opacity-90 transition-all mr-2';
            saveBtn.textContent = 'Save';
            
            const cancelBtn = document.createElement('button');
            cancelBtn.className = 'px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-500 rounded-lg text-[9px] font-bold uppercase tracking-wider hover:bg-slate-200 transition-all';
            cancelBtn.textContent = 'Cancel';
            
            saveBtn.onclick = async function() {
                const newText = textarea.value.trim();
                if (!newText) {
                    alert('Feedback cannot be empty.');
                    return;
                }
                
                saveBtn.disabled = true;
                saveBtn.textContent = 'Saving...';
                
                try {
                    const response = await fetch(`/recruitment/feedbacks/${feedbackId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ feedback: newText })
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        contentEl.textContent = newText;
                        contentEl.setAttribute('data-feedback-full', newText);
                        itemEl.classList.remove('is-editing');
                        textarea.remove();
                        saveBtn.remove();
                        cancelBtn.remove();
                        contentEl.classList.remove('hidden');
                        
                        const candidateId = document.getElementById('feedback-candidate-id').value;
                        updateTableBadge(candidateId);
                    } else {
                        alert(data.error || 'Failed to save feedback.');
                        saveBtn.disabled = false;
                        saveBtn.textContent = 'Save';
                    }
                } catch (error) {
                    console.error('Error saving feedback:', error);
                    alert('An error occurred.');
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Save';
                }
            };
            
            cancelBtn.onclick = function() {
                itemEl.classList.remove('is-editing');
                textarea.remove();
                saveBtn.remove();
                cancelBtn.remove();
                contentEl.classList.remove('hidden');
            };
            
            contentEl.classList.add('hidden');
            contentEl.after(textarea, saveBtn, cancelBtn);
            textarea.focus();
        }

        async function deleteFeedback(feedbackId, buttonEl) {
            if (!confirm('Are you sure you want to delete this feedback?')) return;
            
            const itemEl = buttonEl.closest('.group\\/item');
            
            try {
                const response = await fetch(`/recruitment/feedbacks/${feedbackId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    itemEl.remove();
                    const candidateId = document.getElementById('feedback-candidate-id').value;
                    updateTableBadge(candidateId);
                    
                    const container = document.getElementById('previous-feedbacks-container');
                    if (container.children.length === 0) {
                        container.innerHTML = '<div class="text-xs font-semibold text-slate-400 py-6 text-center italic">No feedback given yet. Be the first to leave a comment!</div>';
                    }
                } else {
                    alert(data.error || 'Failed to delete feedback.');
                }
            } catch (error) {
                console.error('Error deleting feedback:', error);
                alert('An error occurred.');
            }
        }

        async function openFeedbackModal(candidateId) {
            document.getElementById('feedback-candidate-id').value = candidateId;
            document.getElementById('feedback-textarea').value = '';
            document.getElementById('feedbackModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            
            await loadFeedbacks(candidateId);
            
            setTimeout(() => {
                document.getElementById('feedback-textarea').focus();
            }, 100);
        }

        function closeFeedbackModal() {
            document.getElementById('feedbackModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openViewFeedbackModal(el) {
            const itemEl = el.closest('.group\\/item');
            if (!itemEl || itemEl.classList.contains('is-editing')) return;
            
            const name = itemEl.querySelector('.feedback-user-name').textContent.trim();
            const role = itemEl.querySelector('.feedback-user-role').textContent.trim();
            const date = itemEl.querySelector('.feedback-date').textContent.trim();
            const feedbackText = el.getAttribute('data-feedback-full');

            document.getElementById('view-feedback-user-name').textContent = name;
            document.getElementById('view-feedback-user-role-date').textContent = `${role} • ${date}`;
            document.getElementById('view-feedback-content').textContent = feedbackText;

            document.getElementById('viewFeedbackModal').classList.remove('hidden');
        }

        function closeViewFeedbackModal() {
            document.getElementById('viewFeedbackModal').classList.add('hidden');
        }

        async function saveFeedback() {
            const candidateId = document.getElementById('feedback-candidate-id').value;
            const feedbackText = document.getElementById('feedback-textarea').value.trim();
            if (!feedbackText) {
                alert('Please enter a feedback comment.');
                return;
            }
            const btn = document.querySelector('#feedbackModal button[onclick="saveFeedback()"]');
            
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Saving...';

            try {
                const response = await fetch(`/recruitment/candidate/${candidateId}/feedbacks`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        feedback: feedbackText
                    })
                });

                const data = await response.json();
                if (data.success) {
                    document.getElementById('feedback-textarea').value = '';
                    await loadFeedbacks(candidateId);
                    updateTableBadge(candidateId);
                } else {
                    alert(data.error || 'Failed to save feedback');
                }
            } catch (error) {
                console.error('Save feedback error:', error);
                alert('An error occurred while saving feedback.');
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        }

        function openPortfolioModal(candidateId, url) {
            document.getElementById('portfolio-candidate-id').value = candidateId;
            const input = document.getElementById('portfolio-url-input');
            if (input) input.value = url || '';
            const openRow = document.getElementById('portfolio-open-row');
            const openLink = document.getElementById('portfolio-open-link');
            if (url) {
                openRow.classList.remove('hidden');
                openLink.href = url;
            } else {
                openRow.classList.add('hidden');
            }
            document.getElementById('portfolioModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            if (input) setTimeout(() => input.focus(), 50);
        }

        function closePortfolioModal() {
            document.getElementById('portfolioModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        async function savePortfolio() {
            const candidateId = document.getElementById('portfolio-candidate-id').value;
            const input = document.getElementById('portfolio-url-input');
            const url = input ? input.value.trim() : '';
            const btn = document.querySelector('#portfolioModal button[onclick="savePortfolio()"]');

            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Saving...';

            try {
                const response = await fetch(`/recruitment/candidate/${candidateId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ field: 'portfolio', value: url })
                });

                const data = await response.json();
                if (data.success) {
                    // Update icon appearance
                    const trigger = document.querySelector(`.portfolio-trigger[data-candidate-id="${candidateId}"]`);
                    if (trigger) {
                        trigger.dataset.portfolio = url;
                        trigger.title = url || 'Add portfolio link';
                        trigger.setAttribute('onclick', `openPortfolioModal(${candidateId}, '${url.replace(/'/g, "\\'")}')`);
                        if (url) {
                            trigger.className = trigger.className
                                .replace(/bg-slate-100[^\s]*/g, '')
                                .replace(/dark:bg-slate-800[^\s]*/g, '')
                                .replace(/text-slate-400[^\s]*/g, '')
                                .replace(/dark:text-slate-600[^\s]*/g, '');
                            trigger.classList.remove('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400', 'dark:text-slate-600', 'hover:text-brand-teal', 'hover:bg-brand-teal/10');
                            trigger.classList.add('bg-brand-teal/20', 'text-brand-teal', 'ring-1', 'ring-brand-teal/30', 'hover:bg-brand-teal/30');
                        } else {
                            trigger.classList.remove('bg-brand-teal/20', 'text-brand-teal', 'ring-1', 'ring-brand-teal/30', 'hover:bg-brand-teal/30');
                            trigger.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-400', 'dark:text-slate-600', 'hover:text-brand-teal', 'hover:bg-brand-teal/10');
                        }
                    }
                    closePortfolioModal();
                } else {
                    alert(data.error || 'Failed to save portfolio link');
                }
            } catch (error) {
                console.error('Save portfolio error:', error);
                alert('An error occurred while saving.');
            } finally {
                btn.disabled = false;
                btn.textContent = originalText;
            }
        }

        function formatSalary(element) {
            let val = element.textContent.replace(/[^0-9]/g, '');
            if (val !== '') {
                // Store cursor position
                const selection = window.getSelection();
                const range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
                const offset = range ? range.startOffset : 0;
                const oldLen = element.textContent.length;

                element.textContent = Number(val).toLocaleString('en-US');

                // Restore cursor position
                if (range && element.childNodes.length > 0) {
                    const newLen = element.textContent.length;
                    const newOffset = Math.max(0, Math.min(offset + (newLen - oldLen), newLen));
                    
                    try {
                        const newRange = document.createRange();
                        newRange.setStart(element.childNodes[0], newOffset);
                        newRange.collapse(true);
                        selection.removeAllRanges();
                        selection.addRange(newRange);
                    } catch (e) {
                        console.error('Cursor restoration error:', e);
                    }
                }
            }
        }




    </script>
</x-app-layout>
