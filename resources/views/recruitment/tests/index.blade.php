<x-app-layout>
    @section('title', 'Assessment Tests | ' . config('app.name'))
    <x-slot name="header">
        <div class="flex justify-between items-center px-4 gap-6">
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
                            <span class="text-brand-teal dark:text-brand-accent">Assessment Tests</span>
                        </li>
                    </ol>
                </nav>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Assessment Tests') }}
                </h2>
                <p class="text-xs text-slate-500 mt-1 uppercase tracking-widest">Manage Candidate Task Templates</p>
            </div>
            <div class="flex items-center gap-4 justify-end">
                <a href="{{ route('tests.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-navy dark:bg-brand-teal text-white rounded-md text-xs font-semibold uppercase tracking-widest hover:opacity-90 transition-all duration-300 shadow-sm">
                    {{ __('Create New Test') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 transition-colors duration-300 rounded-3xl shadow-sm overflow-hidden border border-slate-100 dark:border-slate-800">
                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full border-separate border-spacing-y-6">
                        <thead>
                            <tr class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 border-b border-slate-100 dark:border-slate-800">
                                <th class="pb-4 pl-4 text-left">Test Name</th>
                                <th class="pb-4 text-left">Email Subject</th>
                                <th class="pb-4 text-center">Last Updated</th>
                                <th class="pb-4 text-right pr-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tests as $test)
                                <tr class="group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors">
                                    <td class="py-6 pl-4 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                                        <div class="text-sm font-bold text-brand-navy dark:text-white">{{ $test->name }}</div>
                                    </td>
                                    <td class="py-6 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900">
                                        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $test->subject }}</div>
                                    </td>
                                    <td class="py-6 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-center">
                                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $test->updated_at->format('M d, Y') }}</div>
                                    </td>
                                    <td class="py-6 align-middle border-b border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 text-right pr-4">
                                        <div class="flex justify-end gap-3">
                                            <a href="{{ route('tests.edit', $test) }}" class="text-slate-400 hover:text-brand-teal transition-colors" title="Edit Test">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('tests.destroy', $test) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this test?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors" title="Delete Test">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-12 text-center text-xs font-bold text-slate-400 uppercase tracking-widest italic">
                                        No assessment tests found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($tests->hasPages())
                    <div class="px-6 py-4 bg-slate-50/50 dark:bg-slate-800/30 border-t border-gray-50 dark:border-slate-800">
                        {{ $tests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
