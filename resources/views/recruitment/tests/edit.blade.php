<x-app-layout>
    @section('title', 'Edit Assessment Test | ' . config('app.name'))
    <x-slot name="header">
        <div class="flex justify-between items-center px-4 gap-6">
            <div>
                <nav class="flex mb-3" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-2 text-[10px] font-bold uppercase tracking-widest">
                        <li>
                            <a href="{{ route('recruitment.index') }}" class="text-slate-400 dark:text-slate-500 hover:text-brand-teal transition-colors">Recruitment</a>
                        </li>
                        <li>
                             <svg class="w-2.5 h-2.5 text-slate-300 dark:text-slate-700 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </li>
                        <li>
                            <a href="{{ route('tests.index') }}" class="text-slate-400 dark:text-slate-500 hover:text-brand-teal transition-colors uppercase">Assessment Tests</a>
                        </li>
                        <li class="flex items-center">
                            <svg class="w-2.5 h-2.5 text-slate-300 dark:text-slate-700 mx-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="text-brand-teal dark:text-brand-accent">Edit Test</span>
                        </li>
                    </ol>
                </nav>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Edit Assessment Test') }}: {{ $test->name }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
             <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl transition-all duration-300 rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden border border-slate-100 dark:border-slate-800 ring-1 ring-slate-900/5 dark:ring-white/10">
                <form action="{{ route('tests.update', $test) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                    @csrf
                    @method('PATCH')
                    
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="group">
                                <label for="name" class="block text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-2 transition-colors group-focus-within:text-brand-teal">Test Name (Internal)</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $test->name) }}" placeholder="e.g. AI Design Task" 
                                    class="w-full bg-slate-50 dark:bg-slate-800/50 border-0 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-brand-teal focus:bg-white dark:focus:bg-slate-800 transition-all duration-200 placeholder:text-slate-300 dark:placeholder:text-slate-600 text-slate-700 dark:text-gray-200 shadow-sm" required autofocus>
                                <x-input-error :messages="$errors->get('name')" class="mt-1" />
                            </div>

                            <div class="group">
                                <label for="subject" class="block text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-2 transition-colors group-focus-within:text-brand-teal">Email Subject</label>
                                <input type="text" name="subject" id="subject" value="{{ old('subject', $test->subject) }}" placeholder="Subject candidates will see" 
                                    class="w-full bg-slate-50 dark:bg-slate-800/50 border-0 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-brand-teal focus:bg-white dark:focus:bg-slate-800 transition-all duration-200 placeholder:text-slate-300 dark:placeholder:text-slate-600 text-slate-700 dark:text-gray-200 shadow-sm" required>
                                <x-input-error :messages="$errors->get('subject')" class="mt-1" />
                            </div>
                        </div>

                        <div class="group">
                            <label class="block text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-2 transition-colors group-focus-within:text-brand-teal">Task Document (Optional)</label>
                            @if($test->attachment_path)
                                <div class="mb-3 flex items-center p-3 bg-brand-teal/10 dark:bg-brand-teal/5 rounded-xl border border-brand-teal/20">
                                    <svg class="w-5 h-5 text-brand-teal mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300 mr-2">Current file attached</span>
                                    <a href="{{ asset('storage/' . $test->attachment_path) }}" target="_blank" class="text-[10px] font-bold uppercase text-brand-teal hover:underline tracking-widest">View File</a>
                                </div>
                            @endif
                            <div class="flex items-center justify-center w-full">
                                <label for="attachment" class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-200 dark:border-slate-800 border-dashed rounded-xl cursor-pointer bg-slate-50 dark:bg-slate-800/30 hover:bg-slate-100 dark:hover:bg-slate-800/50 transition-all duration-200">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-slate-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-xs text-slate-500 font-medium"><span class="font-bold underline">Click to upload</span> or drag and drop</p>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-tighter">PDF, DOC, ZIP (MAX. 10MB)</p>
                                    </div>
                                    <input id="attachment" name="attachment" type="file" class="hidden" />
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('attachment')" class="mt-1" />
                        </div>

                        <div class="group">
                            <label for="content" class="block text-[10px] font-bold uppercase text-slate-400 tracking-wider mb-2 transition-colors group-focus-within:text-brand-teal">Instructions</label>
                            <div class="relative">
                                <textarea name="content" id="content" rows="15" placeholder="Detailed instructions..." 
                                    class="w-full bg-slate-50 dark:bg-slate-800/50 border-0 rounded-xl px-4 py-3 text-sm font-medium focus:ring-2 focus:ring-brand-teal focus:bg-white dark:focus:bg-slate-800 transition-all duration-200 placeholder:text-slate-300 dark:placeholder:text-slate-600 text-slate-700 dark:text-gray-200 shadow-sm resize-y font-mono" required>{{ old('content', $test->content) }}</textarea>
                            </div>
                            <p class="text-[10px] text-slate-500 mt-2 flex items-center justify-between">
                                <span>Markdown ready</span>
                                <span>Use <span class="font-mono text-brand-teal font-bold">[Insert Upload Link Here]</span></span>
                            </p>
                            <x-input-error :messages="$errors->get('content')" class="mt-1" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800 gap-4">
                        <a href="{{ route('tests.index') }}" class="text-xs font-bold uppercase tracking-wider text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors py-2 whitespace-nowrap">Cancel</a>
                        <button type="submit" class="w-full flex items-center justify-center bg-brand-navy dark:bg-brand-teal text-white rounded-xl py-4 text-xs font-black uppercase tracking-widest hover:opacity-90 transition-all shadow-md hover:shadow-lg active:scale-[0.98]">
                            Update Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('attachment').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const container = this.closest('label');
            if (fileName) {
                container.querySelector('p.text-xs').innerHTML = `<span class="font-bold underline text-brand-teal">Selected: ${fileName}</span>`;
                container.style.borderColor = '#0d9488';
                container.style.backgroundColor = 'rgba(13, 148, 136, 0.05)';
            }
        });
    </script>
</x-app-layout>
