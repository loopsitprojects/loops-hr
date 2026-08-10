<x-app-layout>
    @section('title', 'Create System User | ' . config('app.name'))
    <x-slot name="header">
        <div class="flex items-center px-2">
            <a href="{{ route('users.index') }}" class="mr-4 p-2 bg-white rounded-xl shadow-soft border border-gray-100 text-brand-navy hover:bg-brand-navy hover:text-white transition-all duration-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h2 class="font-black text-2xl text-brand-navy leading-tight tracking-tight">
                    {{ __('Create System User') }}
                </h2>
                <p class="text-sm text-brand-slate font-medium">Grant platform access to a new team member</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-slate-900 rounded-[2.5rem] shadow-premium border border-gray-50 dark:border-slate-800 overflow-hidden transition-colors duration-300">
                <div class="p-12">
                    <form method="POST" action="{{ route('register') }}" class="space-y-8">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-[10px] font-black text-brand-slate dark:text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Full Name</label>
                                <input id="name" type="text" name="name" :value="old('name')" required autofocus 
                                    class="block w-full rounded-2xl border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 py-3 px-4 focus:ring-4 focus:ring-brand-teal/10 focus:border-brand-teal transition-all duration-300 font-medium text-brand-navy dark:text-gray-100">
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-[10px] font-black text-brand-slate dark:text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Email Address</label>
                                <input id="email" type="email" name="email" :value="old('email')" required 
                                    class="block w-full rounded-2xl border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 py-3 px-4 focus:ring-4 focus:ring-brand-teal/10 focus:border-brand-teal transition-all duration-300 font-medium text-brand-navy dark:text-gray-100">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Role Selection -->
                            <div>
                                <label for="role" class="block text-[10px] font-black text-brand-slate dark:text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">System Role</label>
                                <select id="role" name="role" required 
                                    class="block w-full rounded-2xl border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 py-3 px-4 focus:ring-4 focus:ring-brand-teal/10 focus:border-brand-teal transition-all duration-300 font-medium text-brand-navy dark:text-gray-100">
                                    <option value="">Select a Role</option>
                                    <option value="{{ \App\Models\User::ROLE_SUPER_ADMIN }}">Super Admin</option>
                                    <option value="{{ \App\Models\User::ROLE_HR_ADMIN }}">HR Admin</option>
                                    <option value="{{ \App\Models\User::ROLE_MANAGER }}">Operations Manager</option>
                                    <option value="{{ \App\Models\User::ROLE_MANAGERS }}">Managers</option>
                                    <option value="{{ \App\Models\User::ROLE_HOD }}">Head of Department</option>
                                </select>
                                <x-input-error :messages="$errors->get('role')" class="mt-2" />
                            </div>

                            <!-- Department Selection (Optional) -->
                            <div>
                                <label for="department_id" class="block text-[10px] font-black text-brand-slate dark:text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Department (Optional)</label>
                                <select id="department_id" name="department_id" 
                                    class="block w-full rounded-2xl border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 py-3 px-4 focus:ring-4 focus:ring-brand-teal/10 focus:border-brand-teal transition-all duration-300 font-medium text-brand-navy dark:text-gray-100">
                                    <option value="">No Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-[10px] font-black text-brand-slate dark:text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Password</label>
                                <div class="relative" x-data="{ show: false }">
                                    <input id="password" x-bind:type="show ? 'text' : 'password'" name="password" required 
                                        class="block w-full rounded-2xl border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 py-3 pl-4 pr-12 focus:ring-4 focus:ring-brand-teal/10 focus:border-brand-teal transition-all duration-300 font-medium text-brand-navy dark:text-gray-100">
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none" aria-label="Toggle password visibility">
                                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-[10px] font-black text-brand-slate dark:text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Confirm Password</label>
                                <div class="relative" x-data="{ show: false }">
                                    <input id="password_confirmation" x-bind:type="show ? 'text' : 'password'" name="password_confirmation" required 
                                        class="block w-full rounded-2xl border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 py-3 pl-4 pr-12 focus:ring-4 focus:ring-brand-teal/10 focus:border-brand-teal transition-all duration-300 font-medium text-brand-navy dark:text-gray-100">
                                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none" aria-label="Toggle password visibility">
                                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <svg x-show="show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-50 dark:border-slate-800 flex items-center justify-end">
                            <button type="submit" class="w-full md:w-auto px-10 py-4 bg-brand-navy dark:bg-brand-teal text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] hover:bg-slate-800 dark:hover:bg-teal-600 hover:shadow-soft active:scale-95 transition-all duration-300">
                                Create User Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
