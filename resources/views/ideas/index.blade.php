<x-app-layout>
    <div class="py-6" x-data="redditFeed()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- 1. TOP COMMUNITY BANNER & WHITE-LABEL THEME BAR --}}
            <div class="rounded-2xl p-6 text-white shadow-md relative overflow-hidden transition-all duration-300"
                 :style="'background: linear-gradient(135deg, ' + currentThemeColor + ' 0%, #1e1b4b 100%)'">
                
                {{-- Decorative background glow --}}
                <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-white/20 border border-white/20 backdrop-blur-md">
                                💡 Social-Driven Open Innovation
                            </span>
                            <span class="text-xs text-white/80">Reddit-Style Community Feed</span>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight" x-text="brandTitle || 'Komunitas Inovasi & Kolaborasi Terbuka'"></h1>
                        <p class="text-xs sm:text-sm text-white/80 max-w-2xl mt-1 leading-relaxed">
                            Eksplorasi gagasan inovasi, berikan upvote untuk ide terbaik, diskusikan solusi, dan temukan rekan tim lintas keahlian.
                        </p>
                        
                        {{-- Quick Stats Pills --}}
                        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs font-semibold">
                            <span class="bg-black/20 backdrop-blur-md px-3 py-1 rounded-full border border-white/10 flex items-center gap-1.5">
                                <span>💡</span> <span>{{ $totalIdeasCount }} Total Inovasi</span>
                            </span>
                            <span class="bg-black/20 backdrop-blur-md px-3 py-1 rounded-full border border-white/10 flex items-center gap-1.5">
                                <span>👥</span> <span>{{ $totalUsersCount }} Inovator Terdaftar</span>
                            </span>
                            <span class="bg-black/20 backdrop-blur-md px-3 py-1 rounded-full border border-white/10 flex items-center gap-1.5">
                                <span>🤝</span> <span>{{ count($openPositions) }} Posisi Kolaborasi</span>
                            </span>
                        </div>
                    </div>

                    {{-- Theme & Customization Controller --}}
                    <div class="flex flex-col items-start md:items-end gap-2 shrink-0">
                        <div class="flex items-center gap-1.5 bg-black/30 backdrop-blur-md px-3 py-1.5 rounded-xl border border-white/10">
                            <span class="text-[11px] font-medium text-white/80 mr-1">Tema:</span>
                            <template x-for="t in themePresets" :key="t.name">
                                <button type="button" @click="applyTheme(t)" :title="t.name"
                                        class="w-5 h-5 rounded-full transition-all transform hover:scale-125 border"
                                        :class="activeTheme === t.name ? 'border-white scale-110 ring-2 ring-white/60' : 'border-transparent opacity-80'"
                                        :style="'background-color: ' + t.color">
                                </button>
                            </template>
                        </div>
                        <button type="button" @click="editBrandTitle()" class="text-[11px] text-white/70 hover:text-white underline flex items-center gap-1">
                            <span>✏️ Ubah Nama Komunitas</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- 2. REDDIT 3-COLUMN LAYOUT --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                {{-- ==================== COLUMN 1: LEFT SIDEBAR NAVIGATION (3 COLS) ==================== --}}
                <div class="lg:col-span-3 space-y-4">
                    
                    {{-- Feeds / Sorting Menu --}}
                    <div class="bg-white rounded-2xl p-4 shadow-xs border border-gray-200/80 space-y-1">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-gray-400 px-3 py-1">
                            🧭 Feeds Utama
                        </div>
                        <a href="{{ route('ideas.index', array_merge(request()->except('sort'), ['sort' => 'popular'])) }}"
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ request('sort', 'popular') === 'popular' || request('sort') === 'hot' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <span class="flex items-center gap-2.5">
                                <span class="text-base">🔥</span> <span>Populer (Hot)</span>
                            </span>
                            @if(request('sort', 'popular') === 'popular')
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                            @endif
                        </a>
                        <a href="{{ route('ideas.index', array_merge(request()->except('sort'), ['sort' => 'latest'])) }}"
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ request('sort') === 'latest' || request('sort') === 'new' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <span class="flex items-center gap-2.5">
                                <span class="text-base">🕐</span> <span>Terbaru (New)</span>
                            </span>
                            @if(request('sort') === 'latest' || request('sort') === 'new')
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                            @endif
                        </a>
                        <a href="{{ route('ideas.index', array_merge(request()->except('sort'), ['sort' => 'top'])) }}"
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ request('sort') === 'top' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <span class="flex items-center gap-2.5">
                                <span class="text-base">🏆</span> <span>Skor Tertinggi (Top)</span>
                            </span>
                            @if(request('sort') === 'top')
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                            @endif
                        </a>
                        <a href="{{ route('ideas.index', array_merge(request()->except('sort'), ['sort' => 'most_commented'])) }}"
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ request('sort') === 'most_commented' ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <span class="flex items-center gap-2.5">
                                <span class="text-base">💬</span> <span>Diskusi Ramai</span>
                            </span>
                            @if(request('sort') === 'most_commented')
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-600"></span>
                            @endif
                        </a>
                    </div>

                    {{-- Stage-Gate Engine Filter (Alur Inovasi) --}}
                    <div class="bg-white rounded-2xl p-4 shadow-xs border border-gray-200/80 space-y-1">
                        <div class="flex items-center justify-between px-3 py-1">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">
                                🔄 Alur Stage-Gate
                            </span>
                            @if(auth()->check() && auth()->user()->isAdmin())
                                <a href="{{ route('admin.workflow.index') }}" class="text-[10px] text-indigo-600 hover:underline font-bold">
                                    ⚙️ Kelola
                                </a>
                            @endif
                        </div>

                        <a href="{{ route('ideas.index', request()->except('stage')) }}"
                           class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ !request('stage') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <span>Semua Tahapan</span>
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-gray-100 text-gray-600 font-semibold">{{ $totalIdeasCount }}</span>
                        </a>

                        @foreach($stages as $stg)
                            <a href="{{ route('ideas.index', array_merge(request()->except('stage'), ['stage' => $stg->slug])) }}"
                               class="flex items-center justify-between px-3 py-2 rounded-xl text-xs font-bold transition {{ request('stage') === $stg->slug ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                                <span class="flex items-center gap-2 truncate">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $stg->color ?? '#6366f1' }}"></span>
                                    <span class="truncate">{{ $stg->name }}</span>
                                </span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold {{ request('stage') === $stg->slug ? 'bg-indigo-200 text-indigo-900' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $stg->ideas_count ?? 0 }}
                                </span>
                            </a>
                        @endforeach
                    </div>

                    {{-- Category Topics Filter --}}
                    <div class="bg-white rounded-2xl p-4 shadow-xs border border-gray-200/80 space-y-1">
                        <div class="text-[11px] font-bold uppercase tracking-wider text-gray-400 px-3 py-1">
                            📂 Topik Kategori
                        </div>
                        <a href="{{ route('ideas.index', request()->except('category')) }}"
                           class="flex items-center justify-between px-3 py-1.5 rounded-xl text-xs font-bold transition {{ !request('category') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                            <span>Semua Kategori</span>
                        </a>
                        @php
                            $categories = [
                                'teknologi' => ['label' => 'Teknologi', 'icon' => '💻'],
                                'sosial' => ['label' => 'Sosial & Komunitas', 'icon' => '🤝'],
                                'pendidikan' => ['label' => 'Pendidikan', 'icon' => '📚'],
                                'bisnis' => ['label' => 'Bisnis & Startup', 'icon' => '💼'],
                                'lainnya' => ['label' => 'Lainnya', 'icon' => '📌'],
                            ];
                        @endphp
                        @foreach($categories as $key => $cat)
                            <a href="{{ route('ideas.index', array_merge(request()->except('category'), ['category' => $key])) }}"
                               class="flex items-center justify-between px-3 py-1.5 rounded-xl text-xs font-bold transition {{ request('category') === $key ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-50' }}">
                                <span class="flex items-center gap-2">
                                    <span>{{ $cat['icon'] }}</span>
                                    <span>{{ $cat['label'] }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>

                    {{-- CTA Card: Create Innovation --}}
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl p-4 border border-indigo-100 text-center space-y-2.5">
                        <div class="text-2xl">✨</div>
                        <h4 class="font-bold text-xs text-indigo-950">Punya Solusi Inovatif?</h4>
                        <p class="text-[11px] text-gray-600">Publikasikan ide Anda, dapatkan voting dari komunitas, dan temukan rekan tim kolaborasi.</p>
                        <a href="{{ route('ideas.create') }}"
                           class="block w-full py-2 bg-indigo-600 text-white font-bold text-xs rounded-xl hover:bg-indigo-700 transition shadow-xs">
                            + Publikasikan Ide Baru
                        </a>
                    </div>
                </div>


                {{-- ==================== COLUMN 2: MAIN SOCIAL FEED (6 COLS) ==================== --}}
                <div class="lg:col-span-6 space-y-4">

                    {{-- Reddit-Style "Create Post" Quick Bar --}}
                    @auth
                        <div class="bg-white rounded-2xl p-3.5 shadow-xs border border-gray-200/80 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-600 to-purple-600 text-white flex items-center justify-center font-black text-xs shrink-0 shadow-2xs">
                                {{ Auth::user()->initials }}
                            </div>
                            <a href="{{ route('ideas.create') }}"
                               class="flex-1 bg-gray-100 hover:bg-gray-50 hover:border-gray-300 border border-transparent px-4 py-2 rounded-full text-xs text-gray-500 transition cursor-text flex items-center justify-between">
                                <span>Bagikan ide inovasi baru Anda dengan komunitas...</span>
                                <span class="text-indigo-600 font-bold hidden sm:inline">💡 Buat Ide</span>
                            </a>
                        </div>
                    @endauth

                    {{-- Feed Control Bar: Sort Pills & View Switcher --}}
                    <div class="bg-white rounded-2xl p-3 shadow-xs border border-gray-200/80 flex flex-wrap items-center justify-between gap-3">
                        
                        {{-- Sort Pills --}}
                        <div class="flex items-center gap-1 bg-gray-100/80 p-1 rounded-xl">
                            <a href="{{ route('ideas.index', array_merge(request()->except('sort'), ['sort' => 'popular'])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ request('sort', 'popular') === 'popular' || request('sort') === 'hot' ? 'bg-white text-indigo-700 shadow-2xs' : 'text-gray-600 hover:text-gray-900' }}">
                                <span>🔥</span> <span>Hot</span>
                            </a>
                            <a href="{{ route('ideas.index', array_merge(request()->except('sort'), ['sort' => 'latest'])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ request('sort') === 'latest' || request('sort') === 'new' ? 'bg-white text-indigo-700 shadow-2xs' : 'text-gray-600 hover:text-gray-900' }}">
                                <span>🕐</span> <span>New</span>
                            </a>
                            <a href="{{ route('ideas.index', array_merge(request()->except('sort'), ['sort' => 'top'])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ request('sort') === 'top' ? 'bg-white text-indigo-700 shadow-2xs' : 'text-gray-600 hover:text-gray-900' }}">
                                <span>🏆</span> <span>Top</span>
                            </a>
                            <a href="{{ route('ideas.index', array_merge(request()->except('sort'), ['sort' => 'most_commented'])) }}"
                               class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ request('sort') === 'most_commented' ? 'bg-white text-indigo-700 shadow-2xs' : 'text-gray-600 hover:text-gray-900' }}">
                                <span>💬</span> <span>Diskusi</span>
                            </a>
                        </div>

                        {{-- View Mode Switcher (Reddit Feed vs Grid vs List vs Kanban) --}}
                        <div class="flex items-center gap-1 bg-gray-100/80 p-1 rounded-xl">
                            <button type="button" @click="setViewMode('feed')" title="Reddit Social Feed"
                                    :class="viewMode === 'feed' ? 'bg-white text-indigo-600 shadow-2xs font-bold' : 'text-gray-500 hover:text-gray-900'"
                                    class="p-1.5 rounded-lg text-xs transition flex items-center gap-1">
                                <span>📰</span> <span class="hidden md:inline">Feed</span>
                            </button>
                            <button type="button" @click="setViewMode('grid')" title="Grid Cards"
                                    :class="viewMode === 'grid' ? 'bg-white text-indigo-600 shadow-2xs font-bold' : 'text-gray-500 hover:text-gray-900'"
                                    class="p-1.5 rounded-lg text-xs transition flex items-center gap-1">
                                <span>⊞</span> <span class="hidden md:inline">Grid</span>
                            </button>
                            <button type="button" @click="setViewMode('list')" title="Compact List"
                                    :class="viewMode === 'list' ? 'bg-white text-indigo-600 shadow-2xs font-bold' : 'text-gray-500 hover:text-gray-900'"
                                    class="p-1.5 rounded-lg text-xs transition flex items-center gap-1">
                                <span>☰</span> <span class="hidden md:inline">List</span>
                            </button>
                            <button type="button" @click="setViewMode('kanban')" title="Kanban Workflow Board"
                                    :class="viewMode === 'kanban' ? 'bg-white text-indigo-600 shadow-2xs font-bold' : 'text-gray-500 hover:text-gray-900'"
                                    class="p-1.5 rounded-lg text-xs transition flex items-center gap-1">
                                <span>📊</span> <span class="hidden md:inline">Kanban</span>
                            </button>
                        </div>
                    </div>

                    {{-- Active Search / Filter Notification Chip --}}
                    @if(request()->hasAny(['search', 'category', 'stage']))
                        <div class="bg-indigo-50/80 border border-indigo-200 rounded-xl px-4 py-2.5 flex items-center justify-between text-xs text-indigo-900">
                            <div class="flex items-center gap-2">
                                <span>🔍 Filter aktif:</span>
                                @if(request('search'))
                                    <span class="font-bold bg-white px-2 py-0.5 rounded-md border border-indigo-200">"{{ request('search') }}"</span>
                                @endif
                                @if(request('category'))
                                    <span class="font-bold bg-white px-2 py-0.5 rounded-md border border-indigo-200">Kategori: {{ ucfirst(request('category')) }}</span>
                                @endif
                                @if(request('stage'))
                                    <span class="font-bold bg-white px-2 py-0.5 rounded-md border border-indigo-200">Tahap: {{ ucfirst(request('stage')) }}</span>
                                @endif
                            </div>
                            <a href="{{ route('ideas.index') }}" class="font-bold text-red-600 hover:underline">
                                ✕ Reset Filter
                            </a>
                        </div>
                    @endif


                    {{-- ==================== VIEW 1: REDDIT SOCIAL FEED (DEFAULT) ==================== --}}
                    <div x-show="viewMode === 'feed'" class="space-y-4">
                        @forelse($ideas as $idea)
                            <div class="bg-white rounded-2xl shadow-xs hover:shadow-md border border-gray-200/80 hover:border-indigo-300 transition-all duration-200 overflow-hidden flex">
                                
                                {{-- LEFT VOTING GUTTER (REDDIT STYLE) --}}
                                <div class="bg-slate-50/80 p-3 sm:px-3.5 border-r border-gray-100 flex flex-col items-center justify-start gap-1 shrink-0"
                                     x-data="votePost({{ $idea->id }}, '{{ $idea->current_user_vote ?? '' }}', {{ $idea->upvotes_count }}, {{ $idea->downvotes_count }})">
                                    
                                    {{-- Upvote Arrow Button --}}
                                    <button type="button" @click.stop="castVote('upvote')"
                                            :disabled="isVoting"
                                            :class="userVote === 'upvote' ? 'text-emerald-600 bg-emerald-100/80 font-bold scale-110' : 'text-gray-400 hover:text-emerald-600 hover:bg-gray-200/60'"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                                            title="Upvote ide ini">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>

                                    {{-- Score Counter --}}
                                    <span class="text-xs font-black transition-colors"
                                          :class="score > 0 ? 'text-emerald-600' : (score < 0 ? 'text-red-500' : 'text-gray-700')"
                                          x-text="score">
                                        {{ $idea->score }}
                                    </span>

                                    {{-- Downvote Arrow Button --}}
                                    <button type="button" @click.stop="castVote('downvote')"
                                            :disabled="isVoting"
                                            :class="userVote === 'downvote' ? 'text-red-600 bg-red-100/80 font-bold scale-110' : 'text-gray-400 hover:text-red-600 hover:bg-gray-200/60'"
                                            class="w-8 h-8 rounded-lg flex items-center justify-center transition-all"
                                            title="Downvote ide ini">
                                        <svg class="w-5 h-5 rotate-180" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>

                                {{-- RIGHT / MAIN POST BODY --}}
                                <div class="p-4 sm:p-5 flex-1 min-w-0 flex flex-col justify-between">
                                    <div>
                                        {{-- Post Header Meta --}}
                                        <div class="flex flex-wrap items-center gap-2 text-xs mb-2">
                                            {{-- Author Avatar & Name --}}
                                            <div class="flex items-center gap-1.5 font-bold text-gray-800">
                                                <div class="w-5 h-5 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px] font-black">
                                                    {{ $idea->user->initials ?? '?' }}
                                                </div>
                                                <span>u/{{ $idea->user->name ?? 'Anonim' }}</span>
                                            </div>

                                            <span class="text-gray-300">•</span>

                                            {{-- Workflow Stage Badge with Color --}}
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold"
                                                  style="background-color: {{ $idea->stage->color ?? '#6366f1' }}20; color: {{ $idea->stage->color ?? '#6366f1' }}; border: 1px solid {{ $idea->stage->color ?? '#6366f1' }}40">
                                                <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $idea->stage->color ?? '#6366f1' }}"></span>
                                                {{ $idea->stage->name ?? ucfirst($idea->current_stage) }}
                                            </span>

                                            {{-- Category Pill --}}
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-gray-100 text-gray-600">
                                                @switch($idea->category)
                                                    @case('teknologi') 💻 Teknologi @break
                                                    @case('sosial') 🤝 Sosial @break
                                                    @case('pendidikan') 📚 Pendidikan @break
                                                    @case('bisnis') 💼 Bisnis @break
                                                    @default 📌 Lainnya
                                                @endswitch
                                            </span>

                                            <span class="text-gray-300">•</span>
                                            <span class="text-gray-400 text-[11px]">{{ $idea->created_at->diffForHumans() }}</span>
                                        </div>

                                        {{-- Title --}}
                                        <a href="{{ route('ideas.show', $idea) }}" class="group block">
                                            <h2 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition leading-snug">
                                                {{ $idea->title }}
                                            </h2>
                                        </a>

                                        {{-- Excerpt Description --}}
                                        <p class="mt-2 text-xs sm:text-sm text-gray-600 line-clamp-3 leading-relaxed">
                                            {{ Str::limit(strip_tags($idea->description), 200) }}
                                        </p>

                                        {{-- Tags --}}
                                        @if($idea->tags)
                                            <div class="mt-3 flex flex-wrap gap-1.5">
                                                @foreach(array_slice($idea->tags, 0, 4) as $tag)
                                                    <a href="{{ route('ideas.index', ['search' => $tag]) }}"
                                                       class="text-[10px] font-semibold bg-indigo-50/70 text-indigo-700 hover:bg-indigo-100 px-2 py-0.5 rounded-md border border-indigo-100/80 transition">
                                                        #{{ $tag }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Crowdsourced Skill Recruitment Banner (if hiring team) --}}
                                        @if($idea->teamPositions && $idea->teamPositions->count() > 0)
                                            <div class="mt-3.5 bg-emerald-50/80 border border-emerald-200 rounded-xl p-2.5 flex items-center justify-between text-xs">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-emerald-700 font-bold flex items-center gap-1">
                                                        <span>🤝</span> Tim Butuh Member:
                                                    </span>
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach($idea->teamPositions->take(2) as $pos)
                                                            <span class="bg-white text-emerald-800 border border-emerald-200 px-2 py-0.5 rounded text-[10px] font-semibold">
                                                                {{ $pos->title }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                <a href="{{ route('ideas.show', $idea) }}" class="font-bold text-emerald-700 hover:underline shrink-0 ml-2">
                                                    Lamar →
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Post Footer Action Bar (Reddit Style) --}}
                                    <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3 text-xs">
                                        <div class="flex items-center gap-3 text-gray-500">
                                            {{-- Comments link --}}
                                            <a href="{{ route('ideas.show', $idea) }}"
                                               class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg hover:bg-gray-100 font-bold text-gray-700 hover:text-indigo-600 transition">
                                                <span>💬</span> <span>{{ $idea->comments_count }} Komentar & Diskusi</span>
                                            </a>

                                            {{-- Share button with copy toast --}}
                                            <button type="button" @click="copyIdeaLink('{{ route('ideas.show', $idea) }}')"
                                                    class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg hover:bg-gray-100 font-semibold text-gray-600 transition">
                                                <span>🔗</span> <span>Bagikan</span>
                                            </button>
                                        </div>

                                        {{-- Right Actions: Detail / Edit --}}
                                        <div class="flex items-center gap-2">
                                            @auth
                                                @if(auth()->id() === $idea->user_id || auth()->user()->isAdmin())
                                                    <a href="{{ route('ideas.edit', $idea) }}" class="text-[11px] font-semibold text-gray-500 hover:text-indigo-600">
                                                        ✏️ Edit
                                                    </a>
                                                @endif
                                            @endauth
                                            <a href="{{ route('ideas.show', $idea) }}"
                                               class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold rounded-lg transition text-xs">
                                                <span>Baca Diskusi</span> <span>→</span>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-2xl shadow-xs p-12 text-center border border-gray-200/80">
                                <div class="text-6xl mb-3">💡</div>
                                <h3 class="text-lg font-bold text-gray-800">Tidak ada ide yang ditemukan</h3>
                                <p class="text-gray-500 text-xs mt-1">Coba ubah kata kunci pencarian atau reset filter untuk melihat semua ide.</p>
                                <a href="{{ route('ideas.create') }}" class="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white font-bold text-xs rounded-xl hover:bg-indigo-700 transition shadow-xs">
                                    ✨ Buat Ide Baru Sekarang
                                </a>
                            </div>
                        @endforelse
                    </div>


                    {{-- ==================== VIEW 2: GRID VIEW ==================== --}}
                    <div x-show="viewMode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($ideas as $idea)
                            <div class="bg-white rounded-2xl shadow-xs hover:shadow-md border border-gray-200/80 p-5 flex flex-col justify-between transition">
                                <div>
                                    <div class="flex items-center justify-between gap-2 mb-2.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold"
                                              style="background-color: {{ $idea->stage->color ?? '#6366f1' }}20; color: {{ $idea->stage->color ?? '#6366f1' }}">
                                            {{ $idea->stage->name ?? ucfirst($idea->current_stage) }}
                                        </span>
                                        <span class="text-[10px] text-gray-400">{{ $idea->created_at->diffForHumans() }}</span>
                                    </div>
                                    <a href="{{ route('ideas.show', $idea) }}" class="block group">
                                        <h3 class="font-bold text-sm text-gray-900 group-hover:text-indigo-600 transition line-clamp-2">
                                            {{ $idea->title }}
                                        </h3>
                                    </a>
                                    <p class="text-xs text-gray-600 mt-2 line-clamp-3">
                                        {{ Str::limit(strip_tags($idea->description), 120) }}
                                    </p>
                                </div>
                                <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs font-semibold">
                                    <span class="text-gray-600 text-[11px]">u/{{ $idea->user->name ?? 'Anonim' }}</span>
                                    <div class="flex items-center gap-3">
                                        <span class="text-emerald-600">▲ {{ $idea->upvotes_count }}</span>
                                        <span class="text-blue-600">💬 {{ $idea->comments_count }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>


                    {{-- ==================== VIEW 3: COMPACT LIST VIEW ==================== --}}
                    <div x-show="viewMode === 'list'" class="space-y-2">
                        @foreach($ideas as $idea)
                            <div class="bg-white rounded-xl shadow-xs hover:border-indigo-300 border border-gray-200/80 p-3.5 flex items-center justify-between gap-4 transition">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <div class="text-center font-bold text-xs text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg shrink-0">
                                        ▲ {{ $idea->upvotes_count }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded"
                                                  style="background-color: {{ $idea->stage->color ?? '#6366f1' }}20; color: {{ $idea->stage->color ?? '#6366f1' }}">
                                                {{ $idea->stage->name ?? ucfirst($idea->current_stage) }}
                                            </span>
                                            <a href="{{ route('ideas.show', $idea) }}" class="font-bold text-xs text-gray-900 hover:text-indigo-600 truncate block">
                                                {{ $idea->title }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 text-xs font-semibold text-gray-500 shrink-0">
                                    <span>💬 {{ $idea->comments_count }}</span>
                                    <a href="{{ route('ideas.show', $idea) }}" class="text-indigo-600 hover:underline">Buka →</a>
                                </div>
                            </div>
                        @endforeach
                    </div>


                    {{-- ==================== VIEW 4: KANBAN WORKFLOW VIEW ==================== --}}
                    <div x-show="viewMode === 'kanban'" class="overflow-x-auto pb-4">
                        <div class="flex gap-4 min-w-[900px]">
                            @foreach($stages as $stg)
                                @php
                                    $stageIdeas = $ideas->filter(fn($i) => $i->current_stage === $stg->slug);
                                @endphp
                                <div class="flex-1 bg-gray-100/80 rounded-2xl p-3.5 border border-gray-200 flex flex-col min-h-[420px]">
                                    <div class="flex items-center justify-between pb-2.5 border-b border-gray-200 mb-3">
                                        <div class="flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $stg->color ?? '#6366f1' }}"></span>
                                            <h4 class="font-bold text-xs text-gray-800 uppercase tracking-wider">{{ $stg->name }}</h4>
                                        </div>
                                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-white text-gray-700 shadow-2xs">
                                            {{ $stageIdeas->count() }}
                                        </span>
                                    </div>

                                    <div class="space-y-2.5 flex-1 overflow-y-auto max-h-[500px]">
                                        @forelse($stageIdeas as $sIdea)
                                            <div class="bg-white rounded-xl p-3 shadow-2xs hover:shadow border border-gray-200 hover:border-indigo-300 transition">
                                                <a href="{{ route('ideas.show', $sIdea) }}" class="font-bold text-xs text-gray-900 hover:text-indigo-600 block line-clamp-2">
                                                    {{ $sIdea->title }}
                                                </a>
                                                <div class="mt-2.5 pt-2 border-t border-gray-100 flex items-center justify-between text-[11px]">
                                                    <span class="text-gray-500 font-medium truncate max-w-[80px]">u/{{ $sIdea->user->name ?? 'Anonim' }}</span>
                                                    <span class="font-bold text-emerald-600">▲ {{ $sIdea->upvotes_count }}</span>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="py-8 text-center text-xs text-gray-400 border border-dashed border-gray-300 rounded-xl">
                                                Kosong
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Pagination --}}
                    @if($ideas->hasPages())
                        <div class="pt-4">
                            {{ $ideas->links() }}
                        </div>
                    @endif

                </div>


                {{-- ==================== COLUMN 3: RIGHT SIDEBAR WIDGETS (3 COLS) ==================== --}}
                <div class="lg:col-span-3 space-y-4">
                    
                    {{-- Widget 1: Tentang Platform (About Community) --}}
                    <div class="bg-white rounded-2xl p-5 shadow-xs border border-gray-200/80 space-y-3">
                        <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                            <span class="text-lg">📌</span>
                            <h3 class="font-bold text-sm text-gray-900">Tentang Platform Inovasi</h3>
                        </div>
                        <p class="text-xs text-gray-600 leading-relaxed">
                            Pusat keterbukaan ide inovatif kampus dan organisasi. Suarakan ide, bangun validasi dengan voting terbuka, dan rekrut tim impian.
                        </p>
                        
                        <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100 text-center">
                            <div class="bg-indigo-50/60 p-2.5 rounded-xl">
                                <div class="font-black text-base text-indigo-700">{{ $totalIdeasCount }}</div>
                                <div class="text-[10px] text-gray-500 font-semibold uppercase">Inovasi</div>
                            </div>
                            <div class="bg-purple-50/60 p-2.5 rounded-xl">
                                <div class="font-black text-base text-purple-700">{{ $totalUsersCount }}</div>
                                <div class="text-[10px] text-gray-500 font-semibold uppercase">Inovator</div>
                            </div>
                        </div>

                        <a href="{{ route('ideas.create') }}"
                           class="block w-full py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-center font-bold text-xs rounded-xl hover:opacity-95 transition shadow-xs">
                            + Ajukan Ide Anda
                        </a>
                    </div>

                    {{-- Widget 2: Ide Trending & Terpopuler --}}
                    <div class="bg-white rounded-2xl p-5 shadow-xs border border-gray-200/80 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <h3 class="font-bold text-sm text-gray-900 flex items-center gap-1.5">
                                <span>🔥</span> <span>Trending Inovasi</span>
                            </h3>
                            <span class="text-[10px] font-bold text-indigo-600">Top Upvoted</span>
                        </div>

                        <div class="space-y-2.5">
                            @foreach($trendingIdeas as $idx => $tIdea)
                                <a href="{{ route('ideas.show', $tIdea) }}" class="flex items-start gap-2.5 group p-1.5 rounded-xl hover:bg-gray-50 transition">
                                    <span class="w-5 h-5 rounded-md flex items-center justify-center text-xs font-black shrink-0 {{ $idx === 0 ? 'bg-amber-100 text-amber-800' : ($idx === 1 ? 'bg-slate-200 text-slate-700' : 'bg-gray-100 text-gray-500') }}">
                                        {{ $idx + 1 }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-xs font-bold text-gray-800 group-hover:text-indigo-600 truncate leading-snug">
                                            {{ $tIdea->title }}
                                        </h4>
                                        <div class="flex items-center gap-2 mt-0.5 text-[10px] text-gray-400">
                                            <span class="text-emerald-600 font-bold">+{{ $tIdea->upvotes_count }} upvotes</span>
                                            <span>•</span>
                                            <span>{{ $tIdea->stage->name ?? $tIdea->current_stage }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Widget 3: Crowdsourced Skill-Matching (Lowongan Kolaborasi) --}}
                    <div class="bg-white rounded-2xl p-5 shadow-xs border border-gray-200/80 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <h3 class="font-bold text-sm text-gray-900 flex items-center gap-1.5">
                                <span>🤝</span> <span>Lowongan Kolaborasi</span>
                            </h3>
                            <span class="text-[10px] font-bold text-emerald-600">Open Recruits</span>
                        </div>

                        @if(count($openPositions) > 0)
                            <div class="space-y-2.5">
                                @foreach($openPositions as $pos)
                                    <div class="p-3 rounded-xl border border-gray-200/80 hover:border-indigo-300 bg-slate-50/50 hover:bg-white transition space-y-1.5">
                                        <div class="flex items-center justify-between gap-1">
                                            <span class="font-bold text-xs text-indigo-700">{{ $pos->title }}</span>
                                            <span class="text-[9px] font-bold text-emerald-600 bg-emerald-100 px-1.5 py-0.5 rounded">Open</span>
                                        </div>
                                        <p class="text-[11px] text-gray-600 line-clamp-1">
                                            {{ $pos->idea->title ?? 'Inovasi' }}
                                        </p>
                                        @if($pos->skills_required)
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(array_slice($pos->skills_required, 0, 2) as $sk)
                                                    <span class="text-[9px] bg-white border border-gray-200 px-1.5 py-0.5 rounded text-gray-600 font-medium">
                                                        {{ $sk }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="pt-1 flex items-center justify-between text-[10px]">
                                            <span class="text-gray-400">u/{{ $pos->idea->user->name ?? 'Tim' }}</span>
                                            <a href="{{ route('ideas.show', $pos->idea_id) }}" class="font-bold text-indigo-600 hover:underline">
                                                Lamar →
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-xs text-gray-400 text-center py-2">Belum ada lowongan tim terbuka saat ini.</p>
                        @endif
                    </div>

                    {{-- Widget 4: Top Inovator Leaderboard & Gamification --}}
                    <div class="bg-white rounded-2xl p-5 shadow-xs border border-gray-200/80 space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                            <h3 class="font-bold text-sm text-gray-900 flex items-center gap-1.5">
                                <span>🏆</span> <span>Leaderboard Inovator</span>
                            </h3>
                            <span class="text-[10px] font-bold text-amber-600">Reputasi</span>
                        </div>

                        <div class="space-y-2">
                            @foreach($topContributors as $idx => $contributor)
                                <div class="flex items-center justify-between p-1.5 rounded-xl hover:bg-gray-50 transition text-xs">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px] font-bold shrink-0">
                                            {{ $contributor->initials }}
                                        </div>
                                        <div class="truncate">
                                            <div class="font-bold text-gray-800 truncate">{{ $contributor->name }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $contributor->badges_count }} lencana</div>
                                        </div>
                                    </div>
                                    <span class="font-bold text-amber-700 bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-full text-[10px] shrink-0">
                                        ⭐ {{ $contributor->reputation_points ?? 0 }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function redditFeed() {
            return {
                brandTitle: localStorage.getItem('inovasi_brand_title') || 'Portal Inovasi & Kolaborasi',
                activeTheme: localStorage.getItem('inovasi_active_theme') || 'Indigo',
                currentThemeColor: localStorage.getItem('inovasi_theme_color') || '#4f46e5',
                viewMode: localStorage.getItem('inovasi_view_mode') || 'feed',

                themePresets: [
                    { name: 'Indigo', color: '#4f46e5' },
                    { name: 'Emerald', color: '#059669' },
                    { name: 'Violet', color: '#7c3aed' },
                    { name: 'Rose', color: '#e11d48' },
                    { name: 'Amber', color: '#d97706' },
                    { name: 'Cyan', color: '#0891b2' }
                ],

                setViewMode(mode) {
                    this.viewMode = mode;
                    localStorage.setItem('inovasi_view_mode', mode);
                },

                applyTheme(theme) {
                    this.activeTheme = theme.name;
                    this.currentThemeColor = theme.color;
                    localStorage.setItem('inovasi_active_theme', theme.name);
                    localStorage.setItem('inovasi_theme_color', theme.color);
                    document.documentElement.style.setProperty('--brand-primary', theme.color);
                },

                editBrandTitle() {
                    const newTitle = prompt('Masukkan nama brand/portal inovasi:', this.brandTitle);
                    if (newTitle && newTitle.trim()) {
                        this.brandTitle = newTitle.trim();
                        localStorage.setItem('inovasi_brand_title', this.brandTitle);
                    }
                },

                copyIdeaLink(url) {
                    navigator.clipboard.writeText(url).then(() => {
                        alert('🔗 Link ide berhasil disalin ke clipboard!');
                    });
                }
            }
        }

        function votePost(ideaId, initialUserVote, initialUpvotes, initialDownvotes) {
            return {
                ideaId: ideaId,
                userVote: initialUserVote || null,
                upvotes: initialUpvotes,
                downvotes: initialDownvotes,
                score: initialUpvotes - initialDownvotes,
                isVoting: false,

                async castVote(type) {
                    @guest
                        window.location.href = "{{ route('login') }}";
                        return;
                    @endguest

                    if (this.isVoting) return;
                    this.isVoting = true;

                    try {
                        const res = await fetch(`/ideas/${this.ideaId}/vote`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ type: type })
                        });

                        if (res.ok) {
                            const data = await res.json();
                            this.upvotes = data.upvotes_count;
                            this.downvotes = data.downvotes_count;
                            this.score = data.score;
                            this.userVote = data.user_vote_type;
                        }
                    } catch (err) {
                        console.error('Voting error:', err);
                    } finally {
                        this.isVoting = false;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
