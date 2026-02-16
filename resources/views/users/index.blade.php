<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center px-2">
            <div>
                <h2 class="font-black text-2xl text-brand-navy leading-tight tracking-tight">
                    {{ __('System Users') }}
                </h2>
                <p class="text-sm text-brand-slate mt-1 font-medium">Manage access to the Loops_HR platform</p>
            </div>
            <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-brand-navy border border-transparent rounded-2xl font-bold text-xs text-white uppercase tracking-widest hover:bg-slate-800 hover:shadow-lg transition-all duration-300">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                {{ __('Add New User') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-slate-900 rounded-[2rem] shadow-premium border border-gray-50 dark:border-slate-800 overflow-hidden transition-colors duration-300">
                <div class="p-8">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-slate-800">
                            <thead>
                                <tr>
                                    <th class="pb-6 text-left text-[10px] font-black text-brand-slate dark:text-slate-400 uppercase tracking-[0.2em]">User Info</th>
                                    <th class="pb-6 text-left text-[10px] font-black text-brand-slate dark:text-slate-400 uppercase tracking-[0.2em]">Role</th>
                                    <th class="pb-6 text-left text-[10px] font-black text-brand-slate dark:text-slate-400 uppercase tracking-[0.2em]">Joined</th>
                                    <th class="pb-6 text-right text-[10px] font-black text-brand-slate dark:text-slate-400 uppercase tracking-[0.2em]">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 dark:divide-slate-800/50">
                                @foreach($users as $user)
                                    <tr class="group hover:bg-gray-50/50 dark:hover:bg-slate-800/50 transition-all duration-200">
                                        <td class="py-6 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-brand-navy/50 flex items-center justify-center text-brand-navy dark:text-brand-accent font-black mr-4 group-hover:bg-brand-navy group-hover:text-white transition-all duration-300">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-black text-brand-navy dark:text-gray-200 leading-tight">{{ $user->name }}</div>
                                                    <div class="text-xs font-medium text-brand-slate mt-0.5 flex items-center">
                                                        {{ $user->email }}
                                                        @if($user->department)
                                                            <span class="mx-2 text-gray-300 dark:text-slate-700">|</span>
                                                            <span class="text-brand-teal dark:text-brand-accent font-black uppercase tracking-wider text-[9px]">{{ $user->department->name }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-6 whitespace-nowrap">
                                            @if($user->is_super_admin || $user->role === \App\Models\User::ROLE_SUPER_ADMIN)
                                                <span class="px-3 py-1.5 inline-flex text-[10px] leading-none font-black rounded-lg uppercase tracking-wider bg-brand-navy dark:bg-brand-teal text-white">
                                                    Super Admin
                                                </span>
                                            @elseif($user->role)
                                                <span class="px-3 py-1.5 inline-flex text-[10px] leading-none font-black rounded-lg uppercase tracking-wider bg-gray-50 dark:bg-slate-800 text-brand-slate dark:text-slate-300 border border-gray-100 dark:border-slate-700">
                                                    {{ $user->role }}
                                                </span>
                                            @else
                                                <span class="px-3 py-1.5 inline-flex text-[10px] leading-none font-black rounded-lg uppercase tracking-wider bg-gray-50 dark:bg-slate-800 text-brand-slate dark:text-gray-400 border border-gray-100 dark:border-slate-700">
                                                    Standard User
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-6 whitespace-nowrap">
                                            <div class="text-xs font-bold text-brand-slate dark:text-slate-400 tracking-wide">
                                                {{ $user->created_at->format('M d, Y') }}
                                            </div>
                                        </td>
                                        <td class="py-6 whitespace-nowrap text-right">
                                            <div class="flex justify-end items-center space-x-3" x-data="{ confirming: false }">
                                                <a href="{{ route('users.edit', $user) }}" 
                                                    class="p-2 text-brand-slate hover:text-brand-navy dark:hover:text-brand-accent hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition-all duration-300" title="Edit User">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                
                                                @if($user->id !== auth()->id())
                                                    <!-- Initial Delete Button -->
                                                    <button type="button" 
                                                        x-show="!confirming"
                                                        x-cloak
                                                        @click="confirming = true"
                                                        class="p-2 text-brand-slate hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl transition-all duration-300" 
                                                        title="Delete User">
                                                        <svg class="w-5 h-5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>

                                                    <!-- Confirmation Action -->
                                                    <div x-show="confirming" 
                                                         x-cloak
                                                         x-transition:enter="transition ease-out duration-200"
                                                         x-transition:enter-start="opacity-0 scale-95"
                                                         x-transition:enter-end="opacity-100 scale-100"
                                                         x-transition:leave="transition ease-in duration-150"
                                                         x-transition:leave-start="opacity-100 scale-100"
                                                         x-transition:leave-end="opacity-0 scale-95"
                                                         class="flex items-center space-x-2">
                                                        <button type="button" 
                                                            @click="triggerGlobalDelete('{{ route('users.destroy', $user) }}', '{{ $user->name }}')"
                                                            class="px-3 py-1.5 bg-red-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-red-700 shadow-sm transition-all duration-200">
                                                            Confirm
                                                        </button>
                                                        <button type="button" 
                                                            @click="confirming = false"
                                                            class="px-3 py-1.5 bg-gray-100 dark:bg-slate-800 text-brand-slate dark:text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-gray-200 dark:hover:bg-slate-700 transition-all duration-200">
                                                            Cancel
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if($users->hasPages())
                <div class="mt-8 px-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Centralized Delete Form -->
    <form id="global-delete-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function triggerGlobalDelete(url, name) {
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Use fetch to delete without page reload
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Reload the page smoothly to show updated list
                window.location.reload();
            })
            .catch(error => {
                console.error('Deletion error:', error);
                alert('An error occurred during deletion. Please try again.');
            });
        }
    </script>
</x-app-layout>
