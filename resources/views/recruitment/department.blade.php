<x-app-layout>
    @section('title', $department->name . ' | Recruitment')
    <x-slot name="header">
        <div class="flex justify-between items-center px-4 gap-6">
            <div class="relative z-10">
                <nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-2 text-[10px] font-bold uppercase tracking-widest">
                        <li>
                            <a href="{{ route('recruitment.index') }}" class="text-slate-400 dark:text-slate-500 hover:text-brand-teal transition-colors">Recruitment</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-2.5 h-2.5 text-slate-300 dark:text-slate-700 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="text-brand-teal">{{ $department->name }}</span>
                        </li>
                    </ol>
                </nav>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ $department->name }}
                </h2>
            </div>
            
            <div class="flex items-center gap-3 justify-end">
                @if(Auth::user()->isAdmin() || Auth::user()->isHR())
                <button x-data="" x-on:click="$dispatch('open-modal', 'edit-department-modal')" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Rename Dept
                </button>
                @endif
                <x-primary-button x-data="" x-on:click="$dispatch('open-modal', 'add-designation-modal')">
                    {{ __('Add Role') }}
                </x-primary-button>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ confirmingRoleDeletion: null, renamingRole: null, renameRoleValue: '' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            

            <!-- Role Grid -->
            <!-- Role Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mt-12">
                @forelse($designations as $index => $designation)
                    <div class="relative group h-full flex flex-col" id="designation-card-{{ $designation->id }}">
                        <div class="p-8 bg-white dark:bg-slate-900 bg-gradient-to-br from-white via-white to-brand-teal/[0.03] dark:from-slate-900 dark:to-slate-900 rounded-[2.5rem] shadow-premium hover:shadow-2xl border border-slate-100 dark:border-slate-800 hover:border-brand-teal/20 dark:hover:border-brand-teal/50 hover:-translate-y-1.5 transition-all duration-500 group/card relative overflow-hidden flex flex-col h-[280px] {{ !$designation->is_active ? 'opacity-60 grayscale-[0.5]' : '' }}">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-2">
                                        <h4 class="text-lg font-bold text-brand-navy dark:text-gray-100 dark:group-hover/card:text-white leading-tight">{{ $designation->name }}</h4>
                                    </div>
                                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 dark:group-hover/card:text-white/70 uppercase tracking-widest">
                                        <span class="{{ $designation->is_active ? 'text-emerald-500' : 'text-slate-400' }}">
                                            {{ $designation->is_active ? 'Active' : 'Paused' }} Hiring
                                        </span>
                                    </p>
                                </div>
                                <div class="text-right">
                                    <a href="{{ route('recruitment.create', ['department_id' => $department->id, 'designation_id' => $designation->id]) }}" 
                                       class="block hover:scale-110 transition-transform"
                                       title="Add Candidate">
                                        <span class="block text-2xl font-bold text-brand-navy dark:text-white dark:group-hover/card:text-white leading-none mb-1">{{ $designation->candidates_count }}</span>
                                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 dark:group-hover/card:text-white/70 uppercase tracking-widest">Profiles</span>
                                    </a>
                                </div>
                            </div>

                            <div class="flex-grow"></div>

                            <!-- Candidate list removed to restore compact tile look -->

                            <div class="mt-8 flex items-center justify-between">
                                <a href="{{ route('recruitment.designation', [$department, $designation]) }}" 
                                   class="text-[10px] font-bold text-brand-teal dark:text-brand-accent dark:group-hover/card:text-white uppercase tracking-widest group-hover:translate-x-2 transition-transform inline-flex items-center">
                                    View Pipeline
                                    <svg class="w-3.5 h-3.5 ml-2 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </a>

                                @if(Auth::user()->isAdmin() || Auth::user()->isHR())
                                <div class="flex items-center gap-1">
                                    <button @click.stop.prevent="renamingRole = {{ $designation->id }}; renameRoleValue = '{{ addslashes($designation->name) }}'" 
                                            class="p-2 text-slate-300 hover:text-brand-teal transition-all"
                                            title="Rename Role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </button>
                                    <button onclick="toggleStatus({{ $designation->id }})" 
                                            class="p-2 {{ $designation->is_active ? 'text-brand-teal' : 'text-slate-400' }} hover:text-brand-navy dark:hover:text-white transition-all"
                                            title="{{ $designation->is_active ? 'Pause Hiring' : 'Resume Hiring' }}">
                                        @if($designation->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @endif
                                    </button>
                                    <form action="{{ route('recruitment.destroyDesignation', $designation) }}" method="POST" onsubmit="return confirm('Remove this role?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-300 dark:text-slate-600 dark:group-hover/card:text-white/40 dark:hover:group-hover/card:text-red-200 rounded-xl transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Rename Role Overlay -->
                        @if(Auth::user()->isAdmin() || Auth::user()->isHR())
                        <div x-show="renamingRole === {{ $designation->id }}" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             class="absolute inset-0 bg-white/30 dark:bg-slate-900/30 backdrop-blur-xl z-30 rounded-[2.5rem] flex flex-col items-center justify-center p-6 text-center border border-white/40 dark:border-slate-800/40 shadow-2xl">
                            <div class="bg-white dark:bg-slate-900 p-6 rounded-[2rem] shadow-xl border border-slate-100 dark:border-slate-800 w-full">
                                <p class="text-sm font-bold text-slate-900 dark:text-white mb-4">Rename Job Role</p>
                                <form action="{{ route('recruitment.updateDesignation', $designation) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="name" x-model="renameRoleValue"
                                           class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-2.5 px-3 text-sm text-slate-900 dark:text-white focus:ring-brand-teal/20 focus:border-brand-teal" required />
                                    <div class="flex flex-col gap-2 pt-1">
                                        <button type="submit" class="w-full py-3 bg-brand-teal text-white rounded-xl text-[11px] font-bold hover:bg-brand-teal/90 shadow-md transition-all active:scale-95">Save</button>
                                        <button type="button" @click.prevent="renamingRole = null" class="w-full py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-[11px] font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center flex flex-col items-center justify-center bg-white/40 dark:bg-slate-900/40 backdrop-blur-xl rounded-[3rem] border border-dashed border-slate-200 dark:border-slate-800">
                        <div class="w-16 h-16 bg-slate-50 dark:bg-slate-800 rounded-3xl flex items-center justify-center text-slate-300 dark:text-slate-600 mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-bold text-slate-400 uppercase tracking-widest italic">No roles defined for this department</p>
                        <button x-on:click="$dispatch('open-modal', 'add-designation-modal')" class="mt-6 text-xs text-brand-teal font-bold hover:underline">Create First Role →</button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Simplified Add Designation Modal -->
    <x-modal name="add-designation-modal" :show="$errors->has('name')" focusable>
        <div class="p-8">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">New Role</h2>
            <p class="text-xs text-slate-500 mb-8">Add a designation to {{ $department->name }}</p>

            <form method="POST" action="{{ route('recruitment.storeDesignation', $department) }}" class="space-y-6">
                @csrf
                <div>
                    <label for="name_role" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Role Title</label>
                    <input id="name_role" name="name" type="text" placeholder="e.g. Senior Creative Lead" 
                           class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-3 focus:ring-brand-teal/20 focus:border-brand-teal transition-all text-slate-900 dark:text-white" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div class="flex items-center justify-end gap-3 mt-8">
                    <button type="button" x-on:click="$dispatch('close')" 
                            class="px-5 py-2.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/50 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-all uppercase tracking-widest">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-brand-navy dark:bg-brand-teal text-white font-bold text-[11px] rounded-xl shadow-sm hover:shadow-md hover:bg-opacity-90 active:scale-95 transition-all uppercase tracking-widest">
                        Create Role
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Simplified Rename Folder Modal -->
    <x-modal name="edit-department-modal" focusable>
        <div class="p-8">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Rename Folder</h2>
            <p class="text-xs text-slate-500 mb-8">Currently: {{ $department->name }}</p>

            <form method="POST" action="{{ route('recruitment.updateDepartment', $department) }}" class="space-y-6">
                @csrf
                @method('PATCH')
                <div>
                    <label for="name_edit" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">New Name</label>
                    <input id="name_edit" name="name" type="text" value="{{ $department->name }}" 
                           class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-3 focus:ring-brand-teal/20 focus:border-brand-teal transition-all text-slate-900 dark:text-white" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div class="flex items-center justify-end gap-3 mt-8">
                    <button type="button" x-on:click="$dispatch('close')" 
                            class="px-5 py-2.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/50 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-all uppercase tracking-widest">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-brand-navy dark:bg-brand-teal text-white font-bold text-[11px] rounded-xl shadow-sm hover:shadow-md hover:bg-opacity-90 active:scale-95 transition-all uppercase tracking-widest">
                        Update Folder
                    </button>
                </div>
            </form>
        </div>
    </x-modal>


    @if(Auth::user()->isAdmin() || Auth::user()->isHR() || Auth::user()->isHOD())
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(45, 212, 191, 0.2);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(45, 212, 191, 0.4);
        }
        .editable-text:empty:before {
            content: attr(data-placeholder);
            color: #94a3b8;
            opacity: 0.6;
        }
        .editable-text:focus:empty:before {
            content: attr(data-placeholder);
            opacity: 0.4;
        }
    </style>
    
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Handle contenteditable fields
        function updateField(element) {
            const candidateId = element.dataset.candidateId;
            const fieldName = element.dataset.field;
            const value = element.textContent.trim();

            // Visual feedback
            const originalBg = element.style.backgroundColor;
            element.style.backgroundColor = 'rgba(59, 130, 246, 0.1)';

            fetch(`/recruitment/candidate/${candidateId}`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    field: fieldName,
                    value: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    element.style.backgroundColor = 'rgba(34, 197, 94, 0.15)';
                    setTimeout(() => {
                        element.style.backgroundColor = '';
                    }, 800);
                } else {
                    element.style.backgroundColor = 'rgba(239, 68, 68, 0.15)';
                    alert('Error: ' + (data.error || 'Failed to update'));
                    setTimeout(() => {
                        element.style.backgroundColor = '';
                    }, 1500);
                }
            })
            .catch(error => {
                element.style.backgroundColor = 'rgba(239, 68, 68, 0.15)';
                console.error('Error:', error);
                alert('Failed to update. Please try again.');
                setTimeout(() => {
                    element.style.backgroundColor = '';
                }, 1500);
            });
        }

        // Handle select dropdowns
        document.querySelectorAll('select.editable-field').forEach(select => {
            select.addEventListener('change', function() {
                const candidateId = this.dataset.candidateId;
                const fieldName = this.dataset.field;
                const value = this.value;

                // Visual feedback
                this.style.opacity = '0.5';

                fetch(`/recruitment/candidate/${candidateId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        field: fieldName,
                        value: value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    this.style.opacity = '1';
                    if (!data.success) {
                        alert('Error: ' + (data.error || 'Failed to update'));
                    }
                })
                .catch(error => {
                    this.style.opacity = '1';
                    console.error('Error:', error);
                    alert('Failed to update. Please try again.');
                });
            });
        });

        function toggleStatus(designationId) {
            fetch(`/recruitment/designation/${designationId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Failed to update status'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update. Please try again.');
            });
        }
    </script>
    @endif
</x-app-layout>
