<nav x-data="{ open: false, searchOpen: false }" class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-xs">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 gap-4">
            
            <!-- Left: Logo & Core Links -->
            <div class="flex items-center gap-6 shrink-0">
                <a href="{{ route('home') }}" class="flex items-center space-x-2.5 group">
                    <span class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-lg text-white shadow-sm group-hover:scale-105 transition-transform">
                        💡
                    </span>
                    <div>
                        <div class="font-black text-lg tracking-tight bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                            Inovasi<span class="text-slate-900">Hub</span>
                        </div>
                        <div class="text-[9px] uppercase font-bold tracking-widest text-indigo-500 -mt-1">
                            Reddit-Style Open Innovation
                        </div>
                    </div>
                </a>

                <!-- Desktop Nav Links -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('ideas.index') }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('ideas.index') || request()->routeIs('home') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                        <span>📰</span> {{ __('Social Feed') }}
                    </a>
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                            <span>📊</span> {{ __('Custom Dashboard') }}
                        </a>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.workflow.index') }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ request()->routeIs('admin.workflow.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                                <span>⚙️</span> {{ __('Stage-Gate Engine') }}
                            </a>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Center: Reddit-Style Global Search Bar -->
            <div class="flex-1 max-w-xl hidden sm:block">
                <form method="GET" action="{{ route('ideas.index') }}" class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari ide, topik inovasi, skill, atau #tag..."
                           class="w-full pl-9 pr-4 py-2 bg-gray-100 hover:bg-gray-50 focus:bg-white border-transparent focus:border-indigo-500 rounded-full text-xs text-gray-800 placeholder-gray-400 transition shadow-inner focus:ring-2 focus:ring-indigo-100" />
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if(request('stage'))
                        <input type="hidden" name="stage" value="{{ request('stage') }}">
                    @endif
                </form>
            </div>

            <!-- Right: Actions & User Menu -->
            <div class="flex items-center gap-3">
                @auth
                    <!-- Quick Create Post Button (Reddit Style) -->
                    <a href="{{ route('ideas.create') }}"
                       class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-xs font-bold rounded-full shadow-xs hover:from-indigo-700 hover:to-purple-700 hover:shadow transition transform hover:-translate-y-0.5">
                        <span class="text-sm">+</span>
                        <span class="hidden sm:inline">Buat Ide</span>
                    </a>

                    <!-- Reputation Pill -->
                    <a href="{{ route('profile.edit') }}"
                       class="hidden md:inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-50 text-amber-800 border border-amber-200 hover:bg-amber-100 transition shadow-2xs"
                       title="Poin Reputasi Anda dari vote dan kontribusi">
                        <span>⭐</span>
                        <span>{{ Auth::user()->reputation_points ?? 0 }}</span>
                        <span class="text-[10px] text-amber-600 font-semibold">pts</span>
                    </a>

                    <!-- Settings Dropdown -->
                    <div class="relative">
                        <x-dropdown align="right" width="56">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center gap-2 p-1.5 sm:px-3 sm:py-1.5 border border-gray-200 rounded-full hover:bg-gray-50 focus:outline-none transition">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-500 text-white flex items-center justify-center text-xs font-black shadow-2xs">
                                        {{ Auth::user()->initials }}
                                    </div>
                                    <div class="hidden lg:block text-left">
                                        <div class="text-xs font-bold text-gray-800 leading-tight">{{ Auth::user()->name }}</div>
                                        <div class="text-[10px] text-indigo-600 font-semibold">{{ ucfirst(Auth::user()->role ?? 'Member') }}</div>
                                    </div>
                                    <svg class="w-3.5 h-3.5 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="px-4 py-2.5 border-b border-gray-100 bg-gray-50 rounded-t-lg">
                                    <p class="text-xs text-gray-500">Login sebagai</p>
                                    <p class="text-xs font-bold text-gray-900 truncate">{{ Auth::user()->email }}</p>
                                    <div class="mt-1.5 flex items-center gap-1.5 text-[11px] font-bold text-amber-700 bg-amber-100/70 px-2 py-0.5 rounded">
                                        <span>⭐ Reputasi:</span> <span>{{ Auth::user()->reputation_points ?? 0 }} poin</span>
                                    </div>
                                </div>

                                <x-dropdown-link :href="route('ideas.index')">
                                    📰 {{ __('Social Feed Inovasi') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('dashboard')">
                                    📊 {{ __('Custom Dashboard') }}
                                </x-dropdown-link>

                                <x-dropdown-link :href="route('profile.edit')">
                                    👤 {{ __('Profil & Portofolio') }}
                                </x-dropdown-link>

                                @if(auth()->user()->isAdmin())
                                    <div class="border-t border-gray-100"></div>
                                    <x-dropdown-link :href="route('admin.workflow.index')" class="text-indigo-600 font-semibold">
                                        ⚙️ {{ __('Stage-Gate Engine (Admin)') }}
                                    </x-dropdown-link>
                                @endif

                                <div class="border-t border-gray-100"></div>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">
                                        🚪 {{ __('Keluar (Log Out)') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('login') }}" class="text-xs font-bold text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-lg transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="text-xs font-bold px-4 py-2 bg-indigo-600 text-white rounded-full hover:bg-indigo-700 transition shadow-xs">
                            Daftar Sekarang
                        </a>
                    </div>
                @endauth

                <!-- Hamburger Mobile -->
                <div class="flex items-center md:hidden">
                    <button @click="open = ! open" class="p-2 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Search Bar -->
    <div class="px-4 pb-3 sm:hidden border-t border-gray-100 pt-2">
        <form method="GET" action="{{ route('ideas.index') }}">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari ide inovasi atau tag..."
                   class="w-full px-3.5 py-1.5 bg-gray-100 border-transparent rounded-full text-xs text-gray-800" />
        </form>
    </div>

    <!-- Mobile Navigation Drawer -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden border-t border-gray-200 bg-white">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('ideas.index')" :active="request()->routeIs('ideas.index') || request()->routeIs('home')">
                📰 {{ __('Social Feed Inovasi') }}
            </x-responsive-nav-link>
            @auth
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    📊 {{ __('Custom Dashboard') }}
                </x-responsive-nav-link>
                @if(auth()->user()->isAdmin())
                    <x-responsive-nav-link :href="route('admin.workflow.index')" :active="request()->routeIs('admin.workflow.*')">
                        ⚙️ {{ __('Stage-Gate Engine') }}
                    </x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('ideas.create')">
                    ✨ {{ __('Buat Ide Baru') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        @auth
            <div class="pt-4 pb-3 border-t border-gray-200 px-4 bg-gray-50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                        {{ Auth::user()->initials }}
                    </div>
                    <div>
                        <div class="font-bold text-sm text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="text-xs text-gray-500">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <div class="mt-2 text-xs font-semibold text-amber-700">
                    ⭐ Reputasi: {{ Auth::user()->reputation_points ?? 0 }} pts
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        👤 {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-600">
                            🚪 {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-3 border-t border-gray-200 px-4 space-y-2">
                <a href="{{ route('login') }}" class="block w-full text-center py-2 text-xs font-bold text-gray-700 bg-gray-100 rounded-lg">
                    Login
                </a>
                <a href="{{ route('register') }}" class="block w-full text-center py-2 text-xs font-bold text-white bg-indigo-600 rounded-lg">
                    Daftar
                </a>
            </div>
        @endauth
    </div>
</nav>
