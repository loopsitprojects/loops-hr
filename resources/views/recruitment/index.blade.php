<x-app-layout>
    @section('title', 'Recruitment Dashboard | ' . config('app.name'))
    <x-slot name="header">
        <div class="flex justify-between items-center px-4 gap-6">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Recruitment') }}
                </h2>
            </div>
            <div class="flex flex-wrap items-center gap-4 justify-end">
                @if(Auth::user()->isAdmin() || Auth::user()->isHR())
                    <a href="{{ route('tests.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-300 shadow-sm">
                        {{ __('Assessment Tests') }}
                    </a>
                    <button onclick="openDefaultRejectionModal()" class="inline-flex items-center px-4 py-2 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-700 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-300 shadow-sm">
                        {{ __('Rejection Email') }}
                    </button>
                    <x-primary-button x-data="" x-on:click="$dispatch('open-modal', 'add-department-modal')">
                        {{ __('Add Department') }}
                    </x-primary-button>
                @endif

            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ confirmingDeletion: null, renamingDepartment: null, renameValue: '' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Folder Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mt-12">
                @foreach($departments as $index => $department)
                    <div class="relative group h-full">
                        <a href="{{ route('recruitment.department', $department) }}" 
                           class="block p-8 bg-white dark:bg-slate-900 bg-gradient-to-br from-white via-white to-brand-teal/[0.03] dark:from-slate-900 dark:to-slate-900 rounded-[2.5rem] shadow-premium hover:shadow-2xl border border-slate-100 dark:border-slate-800 hover:border-brand-teal/20 dark:hover:border-brand-teal/50 hover:-translate-y-1.5 transition-all duration-500 group/card relative overflow-hidden h-full flex flex-col">
                            
                            <div class="flex items-center justify-between mb-6">
                                <div class="w-14 h-14 bg-brand-teal/5 dark:bg-slate-800 text-brand-teal dark:text-brand-accent rounded-2xl flex items-center justify-center dark:group-hover/card:bg-white/20 dark:group-hover/card:text-white transition-all duration-300 shadow-sm border border-brand-teal/10 dark:border-slate-700/50">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="text-right">
                                    <span class="block text-2xl font-bold text-brand-navy dark:text-white dark:group-hover/card:text-white leading-none mb-1">{{ $department->candidates_count }}</span>
                                    <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 dark:group-hover/card:text-white/70 uppercase tracking-widest">Candidates</span>
                                </div>
                            </div>

                            <div class="flex-1"></div>

                            <h4 class="text-lg font-bold text-brand-navy dark:text-gray-100 dark:group-hover/card:text-white mb-2">{{ $department->name }}</h4>
                            
                            <div class="mt-8 flex items-center justify-between">
                                <div class="flex items-center text-[10px] font-bold text-brand-teal dark:text-brand-accent dark:group-hover/card:text-white uppercase tracking-widest group-hover/card:translate-x-2 transition-transform">
                                    Open Folder
                                    <svg class="w-3.5 h-3.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                </div>
                                @if(Auth::user()->isAdmin() || Auth::user()->isHR())
                                    <div class="flex items-center gap-1">
                                        <button @click.stop.prevent="renamingDepartment = {{ $department->id }}; renameValue = '{{ addslashes($department->name) }}'" 
                                                class="p-2 text-slate-300 hover:text-brand-teal transition-colors relative z-20"
                                                title="Rename Department">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button @click.stop.prevent="confirmingDeletion = {{ $department->id }}" 
                                                class="p-2 text-slate-300 hover:text-red-500 transition-colors relative z-20"
                                                title="Delete Department">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <!-- Rename Overlay -->
                        <div x-show="renamingDepartment === {{ $department->id }}" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             class="absolute inset-0 bg-white/30 dark:bg-slate-900/30 backdrop-blur-xl z-30 rounded-[2.5rem] flex flex-col items-center justify-center p-8 text-center border border-white/40 dark:border-slate-800/40 shadow-2xl">
                            <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] shadow-xl border border-slate-100 dark:border-slate-800 w-full max-w-[280px]">
                                <p class="text-sm font-bold text-slate-900 dark:text-white mb-4">Rename Department</p>
                                <form action="{{ route('recruitment.updateDepartment', $department) }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PATCH')
                                    <input type="text" name="name" x-model="renameValue"
                                           class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-2.5 px-3 text-sm text-slate-900 dark:text-white focus:ring-brand-teal/20 focus:border-brand-teal" required />
                                    <div class="flex flex-col gap-2 pt-1">
                                        <button type="submit" class="w-full py-3 bg-brand-teal text-white rounded-xl text-[11px] font-bold hover:bg-brand-teal/90 shadow-md transition-all active:scale-95">Save</button>
                                        <button type="button" @click.prevent="renamingDepartment = null" class="w-full py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-[11px] font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Deletion Overlay -->
                        <div x-show="confirmingDeletion === {{ $department->id }}" 
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 backdrop-blur-0"
                             x-transition:enter-end="opacity-100 backdrop-blur-xl"
                             class="absolute inset-0 bg-white/30 dark:bg-slate-900/30 z-30 rounded-[2.5rem] flex flex-col items-center justify-center p-8 text-center border border-white/40 dark:border-slate-800/40 shadow-2xl">
                            <div class="bg-white dark:bg-slate-900 p-8 rounded-[2rem] shadow-xl border border-slate-100 dark:border-slate-800 max-w-[280px]">
                                <p class="text-sm font-bold text-slate-900 dark:text-white mb-2">Delete {{ $department->name }}?</p>
                                <p class="text-xs text-slate-500 mb-6">This action will permanently remove this organizational folder.</p>
                                <div class="flex flex-col gap-2">
                                    <form action="{{ route('recruitment.destroyDepartment', $department) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full py-3 bg-red-500 text-white rounded-xl text-[11px] font-bold hover:bg-red-600 shadow-md transition-all active:scale-95">Yes, Delete</button>
                                    </form>
                                    <button x-on:click="confirmingDeletion = null" 
                                            class="w-full py-3 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 rounded-xl text-[11px] font-bold hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @push('modals')
    <!-- Simplified Add Department Modal -->
    <x-modal name="add-department-modal" :show="$errors->has('name')" focusable>
        <div class="p-8">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">New Department</h2>
            <p class="text-xs text-slate-500 mb-8">Create a new organizational folder</p>

            <form method="POST" action="{{ route('recruitment.storeDepartment') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="name_create" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-2">Department Name</label>
                    <input id="name_create" name="name" type="text" placeholder="e.g. Strategic Growth" 
                           class="block w-full rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 py-3 focus:ring-brand-teal/20 focus:border-brand-teal transition-all text-slate-900 dark:text-white placeholder-slate-300" required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                </div>

                <div class="flex items-center justify-end gap-3 mt-8">
                    <button type="button" x-on:click="$dispatch('close')" 
                            class="px-5 py-2.5 text-[11px] font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800/50 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-xl transition-all uppercase tracking-widest">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-brand-navy dark:bg-brand-teal text-white font-bold text-[11px] rounded-xl shadow-sm hover:shadow-md hover:bg-opacity-90 active:scale-95 transition-all uppercase tracking-widest">
                        Create Folder
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Global Rejection Email Modal -->
    <div id="defaultRejectionModal" class="fixed inset-0 hidden overflow-y-auto" style="z-index: 999999 !important;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" style="z-index: -1;" aria-hidden="true" onclick="closeDefaultRejectionModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-[2.5rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-slate-100 dark:border-slate-800 relative shadow-[0_0_50px_rgba(0,0,0,0.5)]">
                <div class="bg-white dark:bg-slate-900 px-10 py-10 pb-6">
                    <div class="sm:flex sm:items-start">
                        <div class="w-full text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg leading-6 font-black text-brand-navy dark:text-white uppercase tracking-tighter" id="modal-title">
                                Global Rejection Template
                            </h3>
                            <p class="mt-2 text-[10px] text-slate-400 font-bold uppercase tracking-widest leading-loose">This message will be the default for all candidate rejections.</p>
                            
                            <div class="mt-8">
                                <label for="default-rejection-textarea" class="block text-[10px] font-black uppercase text-slate-400 tracking-widest mb-3">Default Message Content</label>
                                <textarea id="default-rejection-textarea" rows="15" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl px-6 py-5 text-sm focus:ring-2 focus:ring-brand-teal/20 transition-all text-slate-700 dark:text-white placeholder:text-slate-400 resize-none custom-scrollbar shadow-inner">@if($rejectionTemplate){{ $rejectionTemplate->content }}@else Dear [Candidate Name],

Thank you for your interest in joining the team at Loops Integrated and for the time you invested in your application.

After careful consideration, we have decided not to move forward with your application for this position at this time. Our decision was made based on our current requirements and the high volume of applications we received.

We appreciate the opportunity to review your profile and wish you the very best in your future endeavors. Your details will be kept in our database for future opportunities that may be a better match for your skill set.@endif</textarea>
                                <div class="mt-4 flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full bg-brand-teal"></div>
                                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wide">Use <span class="text-brand-teal">[Candidate Name]</span> as a placeholder.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 dark:bg-slate-800/50 px-10 py-8 flex flex-col sm:flex-row justify-center gap-4 rounded-b-[2.5rem]">
                    <button type="button" onclick="saveDefaultRejection()" class="w-full sm:w-1/2 flex items-center justify-center rounded-2xl border border-transparent shadow-lg px-8 py-4 bg-brand-navy dark:bg-brand-teal text-[10px] font-black uppercase tracking-widest text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-teal transition-all active:scale-95 shadow-brand-teal/20 h-[56px]">
                        Save Template
                    </button>
                    <button type="button" onclick="closeDefaultRejectionModal()" class="w-full sm:w-1/2 flex items-center justify-center rounded-2xl border border-transparent px-8 py-4 bg-white dark:bg-slate-700 text-[10px] font-black uppercase tracking-widest text-slate-500 hover:text-slate-700 dark:text-slate-300 dark:hover:text-white focus:outline-none transition-colors h-[56px]">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endpush

    <script>
        function openDefaultRejectionModal() {
            // In a real app, we would fetch the latest template from DB here
            document.getElementById('defaultRejectionModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeDefaultRejectionModal() {
            document.getElementById('defaultRejectionModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        async function saveDefaultRejection() {
            const message = document.getElementById('default-rejection-textarea').value;
            const btn = document.querySelector('#defaultRejectionModal button[onclick="saveDefaultRejection()"]');
            
            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Saving...';

            try {
                // We'll create this route in the next step
                const response = await fetch('/recruitment/update-default-rejection', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                });

                const data = await response.json();
                
                if (data.success) {
                    alert('Default rejection template updated successfully!');
                    closeDefaultRejectionModal();
                } else {
                    alert('Error: ' + (data.message || 'Failed to update template'));
                    btn.disabled = false;
                    btn.textContent = originalText;
                }
            } catch (error) {
                console.error('Error saving template:', error);
                // For now, since we haven't created the route yet, let's just simulate success for the UI part
                alert('An error occurred. Make sure the backend route is implemented.');
                btn.disabled = false;
                btn.textContent = originalText;
            }
        }
    </script>
</x-app-layout>
