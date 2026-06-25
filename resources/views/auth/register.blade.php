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
                                <input id="password" type="password" name="password" required 
                                    class="block w-full rounded-2xl border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 py-3 px-4 focus:ring-4 focus:ring-brand-teal/10 focus:border-brand-teal transition-all duration-300 font-medium text-brand-navy dark:text-gray-100">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-[10px] font-black text-brand-slate dark:text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Confirm Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" required 
                                    class="block w-full rounded-2xl border-gray-100 dark:border-slate-700 bg-gray-50/50 dark:bg-slate-800/50 py-3 px-4 focus:ring-4 focus:ring-brand-teal/10 focus:border-brand-teal transition-all duration-300 font-medium text-brand-navy dark:text-gray-100">
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
