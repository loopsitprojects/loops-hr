<x-app-layout>
    @section('title', 'New Application | ' . config('app.name'))
    <x-slot name="header">
        <div class="flex items-center px-2">
            <a href="{{ url()->previous() }}" class="mr-4 p-2 bg-white dark:bg-slate-800 rounded-xl shadow-soft border border-gray-100 dark:border-slate-700 text-brand-navy dark:text-gray-100 hover:bg-brand-navy dark:hover:bg-brand-teal hover:text-white transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('New Application') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-premium border border-gray-50 dark:border-slate-800 overflow-hidden transition-colors duration-300">
                <div class="p-12">
                    <form method="POST" action="{{ route('recruitment.store') }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf



                        <!-- Hidden Context Fields -->
                        <input type="hidden" name="department_id" value="{{ request('department_id') }}">
                        <input type="hidden" name="designation_id" value="{{ request('designation_id') }}">


                        <!-- CV Upload -->
                        <div class="relative group">
                            <!-- Loading Overlay -->
                            <div id="loading-overlay" class="absolute inset-0 bg-white/95 dark:bg-slate-900/95 z-20 hidden flex-col items-center justify-center rounded-3xl">
                                <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-2xl shadow-soft border border-brand-teal/20 flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-brand-teal animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-brand-navy dark:text-gray-200 uppercase tracking-widest">Processing CV...</p>
                                <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider">Extracting Information</p>
                            </div>

                            <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-300 uppercase tracking-widest mb-2 px-1">Resume / CV (PDF)</label>
                            <div class="relative flex items-center justify-center w-full min-h-[200px] border-2 border-dashed border-gray-200 dark:border-slate-700 rounded-[2rem] bg-gray-50/50 dark:bg-slate-800/50 group-hover:border-brand-teal/50 group-hover:bg-blue-50/30 dark:group-hover:bg-brand-teal/5 transition-all duration-300 cursor-pointer overflow-hidden p-8">
                                <input id="cv" class="absolute inset-0 opacity-0 cursor-pointer z-10" type="file" name="cv" accept=".pdf" required />
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-white dark:bg-slate-800 rounded-2xl shadow-soft border border-gray-100 dark:border-slate-700 flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300 text-brand-teal">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-brand-navy dark:text-gray-200 uppercase tracking-widest mb-2">Upload Candidate CV</p>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider">PDF up to 10MB • Auto-extracts Name, Email & Phone</p>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('cv')" class="mt-2" />
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cvInput = document.getElementById('cv');
            const form = cvInput.closest('form');
            const loadingOverlay = document.getElementById('loading-overlay');
            
            cvInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    // Show loading overlay
                    loadingOverlay.classList.remove('hidden');
                    loadingOverlay.classList.add('flex');
                    
                    // Auto-submit form immediately
                    form.submit();
                }
            });
        });
    </script>
</x-app-layout>
