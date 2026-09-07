<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <span>📊</span> <span>{{ __('Customizable Dashboard') }}</span>
                </h2>
                <p class="text-xs text-gray-500 mt-1">Susun dan atur tata letak widget dashboard sesuai preferensi kebutuhan Anda</p>
            </div>

            <div class="flex items-center gap-3">
                <button type="button" @click="$dispatch('open-widget-customizer')" 
                        class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-semibold rounded-lg hover:bg-indigo-100 transition shadow-sm">
                    <span>⚙️</span> Atur Widget
                </button>
                <a href="{{ route('ideas.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition shadow-sm">
                    <span>✨</span> Buat Ide Baru
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="dashboardCustomizer()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- White-Labeling & Theme Customizer Bar --}}
            <div class="bg-gradient-to-r from-indigo-900 via-indigo-800 to-purple-900 rounded-2xl p-6 text-white shadow-md relative overflow-hidden">
                <div class="relative z-10 flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/30 border border-indigo-400/30 text-indigo-200">
                                🎨 White-Labeling & Theme
                            </span>
                            <span class="text-xs text-indigo-200">Pusat Inovasi & Kolaborasi Terbuka</span>
                        </div>
                        <h3 class="text-xl font-bold mt-1.5" x-text="brandTitle || 'Portal Inovasi Kampus & Komunitas'"></h3>
                        <p class="text-xs text-indigo-200 max-w-xl mt-1">Platform Open Innovation terpadu dengan kustomisasi tampilan, alur kerja fleksibel, dan kolaborasi talenta lintas bidang.</p>
                    </div>

                    {{-- Theme Quick Switcher --}}
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-md px-3.5 py-2 rounded-xl border border-white/10">
                        <span class="text-xs font-medium text-white/90">Tema:</span>
                        <template x-for="t in themePresets" :key="t.name">
                            <button @click="applyTheme(t)" :title="t.name"
                                    class="w-6 h-6 rounded-full border-2 transition-transform transform hover:scale-110"
                                    :class="activeTheme === t.name ? 'border-white scale-110 ring-2 ring-white/50' : 'border-transparent'"
                                    :style="'background-color: ' + t.color">
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Customizable Widget Container with Drag and Drop Reordering --}}
            <div class="space-y-6">
                {{-- Dynamic Widgets --}}
                <template x-for="(wId, index) in widgetOrder" :key="wId">
                    <div x-show="isWidgetVisible(wId)"
                         draggable="true"
                         @dragstart="onDragStart($event, index)"
                         @dragover.prevent="onDragOver($event, index)"
                         @drop="onDrop($event, index)"
                         @dragend="onDragEnd()"
                         :class="{ 'opacity-50 ring-2 ring-indigo-400': draggingIndex === index, 'ring-2 ring-indigo-500 ring-offset-2': dropTargetIndex === index }"
                         class="transition duration-150 cursor-move group relative">

                        {{-- Drag Handle Bar --}}
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-3 right-4 z-20 flex items-center gap-1 bg-gray-900 text-white text-[10px] px-2.5 py-1 rounded-full shadow">
                            <span>⋮⋮ Drag untuk pindah urutan</span>
                                        {{-- WIDGET: STATISTIK PROYEK --}}
                        <template x-if="wId === 'stats'">
                            <div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Total Inovasi</p>
                                            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $totalIdeas }}</p>
                                            <p class="text-[11px] text-green-600 font-medium mt-1">↑ Aktif di sistem</p>
                                        </div>
                                        <div class="w-12 h-12 rounded-xl bg-indigo-50 flex items-center justify-center text-2xl">💡</div>
                                    </div>
                                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Inovator Terdaftar</p>
                                            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $totalUsers }}</p>
                                            <p class="text-[11px] text-indigo-600 font-medium mt-1">👥 Komunitas kampus</p>
                                        </div>
                                        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-2xl">🎓</div>
                                    </div>
                                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Ide Diajukan</p>
                                            <p class="text-3xl font-extrabold text-gray-900 mt-1">{{ $userIdeas->count() }}</p>
                                            <p class="text-[11px] text-blue-600 font-medium mt-1">Oleh profil Anda</p>
                                        </div>
                                        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-2xl">📝</div>
                                    </div>
                                    <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-wider text-gray-500">Poin Reputasi</p>
                                            <p class="text-3xl font-extrabold text-indigo-600 mt-1">{{ $userReputation }}</p>
                                            <p class="text-[11px] text-yellow-600 font-medium mt-1">⭐ Skor kontribusi</p>
                                        </div>
                                        <div class="w-12 h-12 rounded-xl bg-yellow-50 flex items-center justify-center text-2xl">🏆</div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- WIDGET: TIM BUTUH MEMBER (CROWDSOURCED SKILL-MATCHING) --}}
                        <template x-if="wId === 'team_recruitment'">
                            <div>
                                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-4 pb-3 border-b border-gray-100">
                                        <div>
                                            <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                                                 <span>🤝</span> <span>Tim Butuh Member (Crowdsourced Skill-Matching)</span>
                                            </h3>
                                            <p class="text-xs text-gray-500 mt-0.5">Pembuat ide membuka lowongan talenta untuk berkolaborasi mewujudkan karya</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ count($openPositions) }} Posisi Terbuka
                                        </span>
                                    </div>

                                    @if(count($openPositions) > 0)
                                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                            @foreach($openPositions as $pos)
                                                <div class="p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:shadow-sm transition bg-gradient-to-br from-white to-gray-50 flex flex-col justify-between">
                                                    <div>
                                                        <div class="flex items-start justify-between gap-2">
                                                            <span class="inline-block px-2 py-0.5 rounded text-[11px] font-semibold bg-indigo-100 text-indigo-800">
                                                                {{ $pos->title }}
                                                            </span>
                                                            <span class="text-[11px] text-emerald-600 font-medium">● Open</span>
                                                        </div>

                                                        <h4 class="font-bold text-sm text-gray-900 mt-2 line-clamp-1">
                                                            <a href="{{ route('ideas.show', $pos->idea_id) }}" class="hover:text-indigo-600">
                                                                {{ $pos->idea->title ?? 'Inovasi' }}
                                                            </a>
                                                        </h4>
                                                        <p class="text-xs text-gray-600 mt-1 line-clamp-2">{{ $pos->description }}</p>

                                                        @if($pos->skills_required)
                                                            <div class="mt-2.5 flex flex-wrap gap-1">
                                                                @foreach(array_slice($pos->skills_required, 0, 3) as $skill)
                                                                    <span class="text-[10px] bg-white px-2 py-0.5 rounded-md border border-gray-200 text-gray-600">
                                                                        {{ $skill }}
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="mt-4 pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                                                        <span class="text-gray-500 text-[11px]">Oleh: {{ $pos->idea->user->name ?? 'Tim' }}</span>
                                                        <a href="{{ route('ideas.show', $pos->idea_id) }}" class="font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                                                            Lamar <span>→</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="py-6 text-center text-gray-400 text-xs">
                                            Belum ada lowongan tim yang aktif saat ini.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </template>

                        {{-- WIDGET: DISTRIBUSI TAHAP WORKFLOW --}}
                        <template x-if="wId === 'workflow_distribution'">
                            <div>
                                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                    <div class="flex items-center justify-between mb-4">
                                        <div>
                                            <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                                                <span>🔄</span> <span>Distribusi Alur Inovasi (Flexible Stage-Gate Engine)</span>
                                            </h3>
                                            <p class="text-xs text-gray-500 mt-0.5">Pantau jumlah inovasi di tiap tahapan dari Ideation hingga Launch</p>
                                        </div>
                                        @if(auth()->user()->isAdmin())
                                            <a href="{{ route('admin.workflow.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                                ⚙️ Kelola Alur & Threshold →
                                            </a>
                                        @endif
                                    </div>

                                    <div class="space-y-3.5">
                                        @foreach($stageDistribution as $stage)
                                            @php
                                                $percentage = $totalIdeas > 0 ? round(($stage['count'] / $totalIdeas) * 100) : 0;
                                            @endphp
                                            <div>
                                                <div class="flex justify-between text-xs font-medium mb-1.5">
                                                    <span class="text-gray-800 flex items-center gap-2">
                                                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $stage['color'] ?? '#6366f1' }}"></span>
                                                        {{ $stage['name'] }}
                                                    </span>
                                                    <span class="text-gray-500 font-semibold">{{ $stage['count'] }} ide ({{ $percentage }}%)</span>
                                                </div>
                                                <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                                    <div class="h-3 rounded-full transition-all duration-500"
                                                         style="width: {{ max(3, $percentage) }}%; background-color: {{ $stage['color'] ?? '#6366f1' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- WIDGET: TWO COLUMN (IDE SAYA & IDE TERPOPULER/LENCANA) --}}
                        <template x-if="wId === 'ideas_and_social'">
                            <div>
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                    {{-- Your Ideas --}}
                                    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                                                <span>📝</span> <span>Ide Inovasi Anda</span>
                                            </h3>
                                            <a href="{{ route('ideas.create') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">+ Ide Baru</a>
                                        </div>

                                        @if($userIdeas->count() > 0)
                                            <div class="space-y-3">
                                                @foreach($userIdeas->take(5) as $idea)
                                                    <div class="p-3.5 rounded-xl border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/40 transition flex items-center justify-between gap-4">
                                                        <div class="flex-1 min-w-0">
                                                            <a href="{{ route('ideas.show', $idea) }}" class="font-semibold text-sm text-gray-900 hover:text-indigo-600 block truncate">
                                                                {{ $idea->title }}
                                                            </a>
                                                            <div class="flex items-center gap-2 mt-1">
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold"
                                                                      style="background-color: {{ $idea->stage->color ?? '#6366f1' }}20; color: {{ $idea->stage->color ?? '#6366f1' }}">
                                                                    {{ $idea->stage->name ?? ucfirst($idea->current_stage) }}
                                                                </span>
                                                                <span class="text-[11px] text-gray-400">{{ $idea->created_at->diffForHumans() }}</span>
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center gap-3 text-xs text-gray-500 font-medium shrink-0">
                                                            <span class="flex items-center gap-1 text-green-600">👍 {{ $idea->upvotes_count }}</span>
                                                            <span class="flex items-center gap-1 text-blue-600">💬 {{ $idea->comments_count }}</span>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="py-8 text-center bg-gray-50 rounded-xl border border-dashed border-gray-200">
                                                <p class="text-gray-500 text-xs">Anda belum mempublikasikan ide inovasi.</p>
                                                <a href="{{ route('ideas.create') }}" class="mt-2 inline-block px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-semibold hover:bg-indigo-700">
                                                    Ajukan Ide Sekarang
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Sidebar: Badges & Trending --}}
                                    <div class="space-y-6">
                                        {{-- Badges Widget --}}
                                        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                                            <h3 class="font-bold text-gray-900 text-sm mb-3 flex items-center gap-2">
                                                <span>🏆</span> <span>Lencana & Reputasi Anda</span>
                                            </h3>
                                            @if($userBadges && $userBadges->count() > 0)
                                                <div class="space-y-2">
                                                    @foreach($userBadges as $badge)
                                                        <div class="flex items-center gap-2.5 p-2.5 bg-amber-50 rounded-lg border border-amber-100">
                                                            <span class="text-xl">{{ $badge->icon ?? '🏅' }}</span>
                                                            <div>
                                                                <p class="text-xs font-bold text-gray-900">{{ $badge->name }}</p>
                                                                <p class="text-[11px] text-gray-600">{{ $badge->description }}</p>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="p-3 bg-gray-50 rounded-lg text-center text-xs text-gray-500">
                                                    Belum ada lencana. Berikan vote & komentar konstruktif untuk meraih lencana!
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Top Voted Ideas --}}
                                        <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                                            <h3 class="font-bold text-gray-900 text-sm mb-3 flex items-center gap-2">
                                                <span>🔥</span> <span>Ide Terpopuler</span>
                                            </h3>
                                            <div class="space-y-2">
                                                @foreach($topVotedIdeas as $idea)
                                                    <a href="{{ route('ideas.show', $idea) }}" class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition text-xs">
                                                        <span class="text-gray-800 font-medium truncate flex-1">{{ $idea->title }}</span>
                                                        <span class="font-bold text-emerald-600 ml-2 shrink-0">+{{ $idea->upvotes_count }} 👍</span>
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                    </div>
                </template>
            </div>

            {{-- Widget Customizer Modal --}}
            <div x-data="{ open: false }"
                 @open-widget-customizer.window="open = true"
                 x-show="open"
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                <div @click.away="open = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-5">
                    <div class="flex items-center justify-between border-b pb-3">
                        <h3 class="font-bold text-gray-900 text-base">🛠️ Kustomisasi Tata Letak Widget</h3>
                        <button @click="open = false" class="text-gray-400 hover:text-gray-600 text-lg font-bold">✕</button>
                    </div>

                    <p class="text-xs text-gray-600">Centang widget yang ingin ditampilkan di dashboard dan atur urutan posisi dengan drag-and-drop langsung di dashboard.</p>

                    <div class="space-y-3">
                        <template x-for="item in availableWidgets" :key="item.id">
                            <label class="flex items-center justify-between p-3 rounded-xl border border-gray-200 hover:bg-gray-50 cursor-pointer transition">
                                <div class="flex items-center gap-3">
                                    <span class="text-lg" x-text="item.icon"></span>
                                    <div>
                                        <p class="text-xs font-bold text-gray-900" x-text="item.title"></p>
                                        <p class="text-[11px] text-gray-500" x-text="item.desc"></p>
                                    </div>
                                </div>
                                <input type="checkbox"
                                       :checked="isWidgetVisible(item.id)"
                                       @change="toggleWidget(item.id)"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 w-4 h-4">
                            </label>
                        </template>
                    </div>

                    <div class="pt-3 border-t flex items-center justify-between">
                        <button @click="resetWidgets()" class="text-xs text-red-600 hover:underline">
                            🔄 Reset ke Bawaan
                        </button>
                        <button @click="open = false" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-semibold hover:bg-indigo-700">
                            Selesai
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function dashboardCustomizer() {
            return {
                brandTitle: localStorage.getItem('inovasi_brand_title') || 'Portal Inovasi Kampus & Komunitas',
                activeTheme: localStorage.getItem('inovasi_active_theme') || 'Indigo',
                draggingIndex: null,
                dropTargetIndex: null,

                availableWidgets: [
                    { id: 'stats', icon: '📊', title: 'Statistik Proyek', desc: 'Metrik total inovasi, pengguna, ide diajukan, reputasi' },
                    { id: 'team_recruitment', icon: '🤝', title: 'Tim Butuh Member', desc: 'Crowdsourced skill-matching untuk open recruitment anggota' },
                    { id: 'workflow_distribution', icon: '🔄', title: 'Distribusi Alur Inovasi', desc: 'Grafik progres inovasi di setiap stage-gate' },
                    { id: 'ideas_and_social', icon: '📝', title: 'Ide Saya & Komunitas Populer', desc: 'Daftar ide buatan pengguna beserta lencana & trending' }
                ],

                widgetOrder: JSON.parse(localStorage.getItem('inovasi_widget_order') || '["stats", "team_recruitment", "workflow_distribution", "ideas_and_social"]'),
                hiddenWidgets: JSON.parse(localStorage.getItem('inovasi_hidden_widgets') || '[]'),

                themePresets: [
                    { name: 'Indigo', color: '#4f46e5' },
                    { name: 'Emerald', color: '#059669' },
                    { name: 'Violet', color: '#7c3aed' },
                    { name: 'Rose', color: '#e11d48' },
                    { name: 'Amber', color: '#d97706' },
                    { name: 'Cyan', color: '#0891b2' }
                ],

                isWidgetVisible(id) {
                    return !this.hiddenWidgets.includes(id);
                },

                toggleWidget(id) {
                    if (this.hiddenWidgets.includes(id)) {
                        this.hiddenWidgets = this.hiddenWidgets.filter(w => w !== id);
                    } else {
                        this.hiddenWidgets.push(id);
                    }
                    localStorage.setItem('inovasi_hidden_widgets', JSON.stringify(this.hiddenWidgets));
                },

                resetWidgets() {
                    this.widgetOrder = ["stats", "team_recruitment", "workflow_distribution", "ideas_and_social"];
                    this.hiddenWidgets = [];
                    localStorage.removeItem('inovasi_widget_order');
                    localStorage.removeItem('inovasi_hidden_widgets');
                },

                onDragStart(e, index) {
                    this.draggingIndex = index;
                    e.dataTransfer.effectAllowed = 'move';
                },

                onDragOver(e, index) {
                    this.dropTargetIndex = index;
                },

                onDrop(e, targetIndex) {
                    if (this.draggingIndex === null || this.draggingIndex === targetIndex) return;
                    const item = this.widgetOrder.splice(this.draggingIndex, 1)[0];
                    this.widgetOrder.splice(targetIndex, 0, item);
                    localStorage.setItem('inovasi_widget_order', JSON.stringify(this.widgetOrder));
                    this.onDragEnd();
                },

                onDragEnd() {
                    this.draggingIndex = null;
                    this.dropTargetIndex = null;
                },

                applyTheme(theme) {
                    this.activeTheme = theme.name;
                    localStorage.setItem('inovasi_active_theme', theme.name);
                    localStorage.setItem('inovasi_theme_color', theme.color);
                    document.documentElement.style.setProperty('--brand-primary', theme.color);
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
