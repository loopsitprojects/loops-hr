<nav x-data="{ open: false }" class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-gray-100 dark:border-slate-800 sticky top-0 z-50 transition-colors duration-300 pt-1">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="dark:text-slate-300 dark:hover:text-white">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('recruitment.index')" :active="request()->routeIs('recruitment.*')" class="dark:text-slate-300 dark:hover:text-white">
                        {{ __('Recruitment') }}
                    </x-nav-link>
                    @if(Auth::user()->isAdmin() || Auth::user()->isHR() || Auth::user()->isHOD() || Auth::user()->isManager() || Auth::user()->isManagers())
                    <x-nav-link :href="route('analytics.index')" :active="request()->routeIs('analytics.*')" class="dark:text-slate-300 dark:hover:text-white">
                        {{ __('Analytics') }}
                    </x-nav-link>
                    @endif
                    @if(Auth::user()->isAdmin())
                        <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')" class="dark:text-slate-300 dark:hover:text-white">
                            {{ __('System Users') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Theme Toggle & Notifications & Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                <button @click="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light')" 
                    class="p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition-all duration-300">
                    <!-- Sun Icon (shown in dark mode) -->
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                    <!-- Moon Icon (shown in light mode) -->
                    <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <!-- Notification Bell -->
                <x-dropdown align="right" width="w-96">
                    <x-slot name="trigger">
                        <button class="relative p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="notification-badge absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-500 rounded-full min-w-[18px]">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="w-96">
                            <!-- Header -->
                            <div class="px-4 py-3 border-b border-gray-100 dark:border-slate-700 bg-gray-50 dark:bg-slate-800 rounded-t-md">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-base font-black text-gray-900 dark:text-white">Notifications</h3>
                                    <div class="flex items-center gap-2">
                                        @if(auth()->user()->unreadNotifications->count() > 0)
                                            <button type="button" onclick="event.stopPropagation(); markAllNotificationsAsRead()" class="text-[10px] font-bold text-brand-teal hover:text-teal-600 uppercase tracking-widest transition-colors cursor-pointer">
                                                Mark all as read
                                            </button>
                                            <span id="header-notification-count" class="px-2 py-1 text-xs font-bold text-white bg-red-500 rounded-full">
                                                {{ auth()->user()->unreadNotifications->count() }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Notifications List -->
                            <div id="notifications-list" class="overflow-y-auto custom-scrollbar" style="max-height: 450px !important;">
                                @forelse(auth()->user()->unreadNotifications as $notification)
                                    <div id="notification-{{ $notification->id }}" class="notification-item px-4 py-3 hover:bg-gray-50 dark:hover:bg-slate-800 transition-all duration-300 border-b border-gray-100 dark:border-slate-700/50">
                                        <div class="flex items-start gap-4">
                                            <div class="flex-shrink-0 mt-1">
                                                <div class="w-10 h-10 rounded-full bg-brand-teal/10 dark:bg-teal-500/20 flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-brand-teal dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 leading-snug">
                                                    {{ $notification->data['message'] ?? 'New Notification' }}
                                                </p>
                                                @if(isset($notification->data['designation']))
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">for <span class="font-medium text-gray-700 dark:text-gray-300">{{ $notification->data['designation'] }}</span></p>
                                                @endif
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </p>
                                                
                                                <!-- Action Buttons -->
                                                <div class="flex items-center gap-4 mt-3">
                                                    @if(isset($notification->data['url']))
                                                        @php
                                                            $notifUrl = $notification->data['url'];
                                                            
                                                            // Fix: Ensure URL is relative to handle localhost/ngrok mismatches
                                                            if (filter_var($notifUrl, FILTER_VALIDATE_URL)) {
                                                                $parsedUrl = parse_url($notifUrl);
                                                                $notifUrl = ($parsedUrl['path'] ?? '') . (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '');
                                                            }

                                                            // Append candidate_id if missing (legacy support)
                                                            if (!str_contains($notifUrl, 'candidate_id') && isset($notification->data['candidate_id'])) {
                                                                $notifUrl .= (str_contains($notifUrl, '?') ? '&' : '?') . 'candidate_id=' . $notification->data['candidate_id'];
                                                            }
                                                        @endphp
                                                        <a href="{{ $notifUrl }}" class="text-xs font-bold text-brand-teal dark:text-teal-400 hover:underline px-3 py-1.5 bg-brand-teal/5 dark:bg-teal-500/10 rounded-lg transition-colors whitespace-nowrap">
                                                            View Candidate
                                                        </a>
                                                    @endif
                                                    <button type="button" onclick="event.stopPropagation(); markNotificationAsRead('{{ $notification->id }}')" class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 px-2 py-1.5 whitespace-nowrap transition-colors cursor-pointer">
                                                        Mark as read
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="px-4 py-8 text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                        </svg>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">No new notifications</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </x-slot>
                </x-dropdown>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-slate-900 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('recruitment.index')" :active="request()->routeIs('recruitment.*')">
                {{ __('Recruitment') }}
            </x-responsive-nav-link>
            @if(Auth::user()->isAdmin() || Auth::user()->isHR() || Auth::user()->isHOD() || Auth::user()->isManager() || Auth::user()->isManagers())
            <x-responsive-nav-link :href="route('analytics.index')" :active="request()->routeIs('analytics.*')">
                {{ __('Analytics') }}
            </x-responsive-nav-link>
            @endif
            @if(Auth::user()->isAdmin())
                <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                    {{ __('System Users') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-slate-800">
            <div class="flex items-center justify-between px-4">
                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
                <button @click="document.documentElement.classList.toggle('dark'); localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light')" 
                    class="p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-slate-800 rounded-xl transition-all duration-300">
                    <svg class="w-5 h-5 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 9H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                    </svg>
                    <svg class="w-5 h-5 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
</style>

<script>
    function markNotificationAsRead(id) {
        // Optimistically remove from UI instantly for a snappy feel
        const item = document.getElementById(`notification-${id}`);
        if (item) {
            item.style.opacity = '0';
            setTimeout(() => {
                item.remove();
                updateNotificationBadges();
                checkEmptyNotifications();
            }, 200);
        }

        // Use relative path to avoid protocol mismatch (http vs https) on hosted servers
        const url = `/notifications/${id}/mark-as-read`;
        
        fetch(url, {
            method: 'POST', // Changed to POST for better shared hosting compatibility
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                console.error('Notification server error:', response.status);
                // On error, we don't necessarily want to reload, but we log it
            }
            return response.json();
        })
        .then(data => {
            if (!data.success) {
                console.warn('Server failed to mark as read:', data);
            }
        })
        .catch(error => {
            console.error('AJAX Error:', error);
        });
    }

    function markAllNotificationsAsRead() {
        if (!confirm('Mark all notifications as read?')) return;

        // Optimistically clear UI
        const list = document.getElementById('notifications-list');
        const items = list.querySelectorAll('.notification-item');
        items.forEach(item => item.remove());
        updateNotificationBadges(0);
        checkEmptyNotifications();

        const url = '/notifications/mark-all-read';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .catch(error => console.error('Error marking all notifications as read:', error));
    }

    function updateNotificationBadges(forcedCount = null) {
        const badges = document.querySelectorAll('.notification-badge');
        const headerCount = document.getElementById('header-notification-count');
        
        let count;
        if (forcedCount !== null) {
            count = forcedCount;
        } else {
            const items = document.querySelectorAll('.notification-item');
            count = items.length;
        }
        
        badges.forEach(badge => {
            if (count > 0) {
                badge.textContent = count;
                badge.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
            }
        });

        if (headerCount) {
            if (count > 0) {
                headerCount.textContent = count;
                headerCount.parentElement.classList.remove('hidden');
            } else {
                headerCount.parentElement.classList.add('hidden');
            }
        }
    }

    function checkEmptyNotifications() {
        const list = document.getElementById('notifications-list');
        if (list && list.querySelectorAll('.notification-item').length === 0) {
            list.innerHTML = `
                <div class="px-4 py-8 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No new notifications</p>
                </div>
            `;
        }
    }

    // Auto-refresh notifications every 30 seconds
    let lastNotificationCount = {{ auth()->user()->unreadNotifications->count() }};
    
    function checkNotifications() {
        fetch('{{ route("notifications.check") }}')
            .then(response => response.json())
            .then(data => {
                // Update badge count
                const badges = document.querySelectorAll('.notification-badge');
                badges.forEach(badge => {
                    if (data.count > 0) {
                        badge.textContent = data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                });
                
                // Show desktop notification if new notifications arrived
                if (data.count > lastNotificationCount && lastNotificationCount >= 0) {
                    if (Notification.permission === 'granted') {
                        new Notification('New HR Notification', {
                            body: data.notifications[0]?.message || 'You have new notifications',
                            icon: '/loops-icon.png'
                        });
                    }
                }
                
                lastNotificationCount = data.count;
            })
            .catch(error => console.error('Notification check failed:', error));
    }
    
    // Check every 30 seconds
    setInterval(checkNotifications, 30000);
    
    // Request desktop notification permission on page load
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }
</script>
