<x-app-layout>
    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Idea Header --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                {{-- Stage Badge --}}
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold mb-3"
                                      style="background-color: {{ $idea->stage->color ?? '#6366f1' }}20; color: {{ $idea->stage->color ?? '#6366f1' }}">
                                    {{ $idea->stage->name ?? ucfirst($idea->current_stage) }}
                                </span>

                                {{-- Category Badge --}}
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 mb-3">
                                    @switch($idea->category)
                                        @case('teknologi') 💻 Teknologi @break
                                        @case('sosial') 🤝 Sosial @break
                                        @case('pendidikan') 📚 Pendidikan @break
                                        @case('bisnis') 💼 Bisnis @break
                                        @default 📌 Lainnya
                                    @endswitch
                                </span>

                                <h1 class="text-2xl font-bold text-gray-900 mt-2">{{ $idea->title }}</h1>

                                {{-- Author Info --}}
                                <div class="mt-3 flex items-center space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-bold text-indigo-600">
                                        {{ $idea->user->initials }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ $idea->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $idea->created_at->translatedFormat('d F Y, H:i') }} · {{ $idea->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Edit/Delete --}}
                            @auth
                                @if(auth()->id() === $idea->user_id || auth()->user()->isAdmin())
                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
                                        </button>
                                        <div x-show="open" @click.away="open = false"
                                             class="absolute right-0 mt-2 w-36 bg-white rounded-lg shadow-lg py-1 z-10 border">
                                            <a href="{{ route('ideas.edit', $idea) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">✏️ Edit</a>
                                            <form method="POST" action="{{ route('ideas.destroy', $idea) }}"
                                                  onsubmit="return confirm('Yakin ingin menghapus ide ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">🗑️ Hapus</button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            @endauth
                        </div>

                        {{-- Vote Section --}}
                        <div class="mt-6 flex items-center space-x-4 border-t border-b border-gray-100 py-4"
                             x-data="voteSystem({{ $idea->id }}, '{{ $userVoteType }}', {{ $idea->upvotes_count }}, {{ $idea->downvotes_count }})">
                            @auth
                                <button @click="vote('upvote')"
                                        :class="userVoteType === 'upvote' ? 'bg-green-100 text-green-700 border-green-300' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-green-50'"
                                        class="flex items-center space-x-2 px-4 py-2 rounded-lg border transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    <span x-text="upvotes" class="font-semibold"></span>
                                </button>
                                <button @click="vote('downvote')"
                                        :class="userVoteType === 'downvote' ? 'bg-red-100 text-red-700 border-red-300' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-red-50'"
                                        class="flex items-center space-x-2 px-4 py-2 rounded-lg border transition">
                                    <svg class="w-5 h-5 rotate-180" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    <span x-text="downvotes" class="font-semibold"></span>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-gray-50 text-gray-600 border border-gray-200 hover:bg-green-50 transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                                    <span class="font-semibold">{{ $idea->upvotes_count }}</span>
                                </a>
                                <span class="text-sm text-gray-400">Login untuk vote</span>
                            @endauth

                            <div class="flex-1"></div>

                            {{-- Social Share Buttons --}}
                            <div class="flex items-center space-x-2" x-data="{ copied: false }">
                                <button type="button" @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="inline-flex items-center space-x-1 px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-xs font-semibold text-gray-700 transition">
                                    <span x-text="copied ? '✅ Disalin!' : '🔗 Bagikan'"></span>
                                </button>
                                <a href="https://api.whatsapp.com/send?text={{ urlencode($idea->title . ' - ' . url()->current()) }}" target="_blank"
                                   class="px-2.5 py-1.5 rounded-lg bg-green-50 text-green-600 hover:bg-green-100 text-xs font-semibold" title="Bagikan ke WhatsApp">
                                    📱 WA
                                </a>
                            </div>

                            <span class="text-sm text-gray-500">💬 {{ $idea->comments_count }} komentar</span>
                        </div>

                        {{-- Description --}}
                        <div class="mt-6 prose max-w-none text-gray-700">
                            {!! nl2br(e($idea->description)) !!}
                        </div>

                        {{-- Tags --}}
                        @if($idea->tags)
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach($idea->tags as $tag)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700">
                                        #{{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Workflow Progress --}}
                        @if(isset($stages) && $stages->count() > 0)
                            <div class="mt-6 border-t pt-4">
                                <h3 class="text-sm font-semibold text-gray-600 mb-3">📊 Progress Workflow</h3>
                                <div class="flex items-center space-x-1">
                                    @foreach($stages as $stage)
                                        @php
                                            $isActive = $idea->current_stage === $stage->slug;
                                            $isPast = $stage->order < ($idea->stage->order ?? 0);
                                        @endphp
                                        <div class="flex-1 text-center">
                                            <div class="h-2 rounded-full {{ $isActive ? '' : ($isPast ? 'opacity-100' : 'bg-gray-200') }}"
                                                 style="{{ $isActive || $isPast ? 'background-color: ' . ($stage->color ?? '#6366f1') : '' }}">
                                            </div>
                                            <span class="text-xs {{ $isActive ? 'font-bold text-gray-800' : 'text-gray-400' }} mt-1 block">
                                                {{ $stage->name }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Admin: Advance Stage --}}
                        @auth
                            @if(auth()->user()->isAdmin())
                                <div class="mt-4 pt-3 border-t">
                                    <form method="POST" action="{{ route('admin.workflow.advance', $idea) }}" class="flex items-center space-x-2">
                                        @csrf
                                        <span class="text-sm text-gray-500">Admin:</span>
                                        <button type="submit" class="text-sm px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                            ⏭️ Pindahkan ke Tahap Berikutnya
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>

                    {{-- Comments Section --}}
                    <div class="bg-white rounded-xl shadow-sm p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">💬 Komentar ({{ $idea->comments_count }})</h2>

                        {{-- Comment Form --}}
                        @auth
                            <form method="POST" action="{{ route('ideas.comments.store', $idea) }}" class="mb-6">
                                @csrf
                                <textarea name="body" rows="3" required
                                          class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                          placeholder="Tulis komentar konstruktif...">{{ old('body') }}</textarea>
                                <x-input-error :messages="$errors->get('body')" class="mt-1" />
                                <div class="mt-2 flex justify-end">
                                    <x-primary-button>💬 Kirim Komentar</x-primary-button>
                                </div>
                            </form>
                        @else
                            <p class="mb-4 text-sm text-gray-500">
                                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800">Login</a> untuk memberikan komentar.
                            </p>
                        @endauth

                        {{-- Comment List --}}
                        <div class="space-y-4">
                            @forelse($idea->comments as $comment)
                                <div class="border-l-2 border-gray-200 pl-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">
                                                {{ $comment->user->initials ?? '?' }}
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</span>
                                                <span class="text-xs text-gray-400 ml-1">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                        @auth
                                            @if(auth()->id() === $comment->user_id || auth()->user()->isAdmin())
                                                <form method="POST" action="{{ route('comments.destroy', $comment) }}"
                                                      onsubmit="return confirm('Hapus komentar ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-xs text-red-400 hover:text-red-600">🗑️</button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                    <p class="mt-2 text-sm text-gray-700">{{ $comment->body }}</p>

                                    {{-- Reply Form (toggle) --}}
                                    @auth
                                        <div x-data="{ showReply: false }">
                                            <button @click="showReply = !showReply" class="text-xs text-indigo-600 hover:text-indigo-800 mt-1">
                                                ↩️ Balas
                                            </button>
                                            <form x-show="showReply" method="POST" action="{{ route('ideas.comments.store', $idea) }}" class="mt-2">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <textarea name="body" rows="2" required
                                                          class="w-full text-sm rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                                          placeholder="Tulis balasan..."></textarea>
                                                <button type="submit" class="mt-1 text-xs px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">Kirim</button>
                                            </form>
                                        </div>
                                    @endauth

                                    {{-- Replies --}}
                                    @if($comment->replies->count() > 0)
                                        <div class="mt-3 space-y-3 ml-4">
                                            @foreach($comment->replies as $reply)
                                                <div class="border-l-2 border-indigo-100 pl-3">
                                                    <div class="flex items-center space-x-2">
                                                        <div class="w-6 h-6 rounded-full bg-indigo-50 flex items-center justify-center text-xs font-bold text-indigo-500">
                                                            {{ $reply->user->initials ?? '?' }}
                                                        </div>
                                                        <span class="text-xs font-medium text-gray-900">{{ $reply->user->name }}</span>
                                                        <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <p class="mt-1 text-sm text-gray-600 ml-8">{{ $reply->body }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-gray-400 text-center py-4">Belum ada komentar. Jadilah yang pertama!</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Team Positions (Skill Matching) --}}
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <h3 class="font-semibold text-gray-900 mb-3">👥 Lowongan Tim</h3>

                        @if($idea->teamPositions->count() > 0)
                            <div class="space-y-3">
                                @foreach($idea->teamPositions as $position)
                                    <div class="border rounded-lg p-3 {{ $position->is_open ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }}">
                                        <div class="flex items-center justify-between">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $position->title }}</h4>
                                            <span class="text-xs {{ $position->is_open ? 'text-green-600' : 'text-gray-400' }}">
                                                {{ $position->is_open ? '🟢 Buka' : '🔴 Tutup' }}
                                            </span>
                                        </div>
                                        @if($position->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ $position->description }}</p>
                                        @endif
                                        @if($position->skills_required)
                                            <div class="mt-1 flex flex-wrap gap-1">
                                                @foreach($position->skills_required as $skill)
                                                    <span class="text-xs bg-white px-1.5 py-0.5 rounded border">{{ $skill }}</span>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Apply Button --}}
                                        @auth
                                            @if($position->is_open && auth()->id() !== $idea->user_id)
                                                @php $hasApplied = $position->applications->where('user_id', auth()->id())->count() > 0; @endphp
                                                @if($hasApplied)
                                                    <p class="mt-2 text-xs text-indigo-600">✅ Sudah melamar</p>
                                                @else
                                                    <form method="POST" action="{{ route('positions.apply', $position) }}" class="mt-2"
                                                          x-data="{ showForm: false }">
                                                        @csrf
                                                        <button type="button" @click="showForm = !showForm"
                                                                class="text-xs px-2 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                                                            🙋 Daftar
                                                        </button>
                                                        <div x-show="showForm" class="mt-2">
                                                            <textarea name="message" rows="2"
                                                                      class="w-full text-xs rounded border-gray-300"
                                                                      placeholder="Pesan singkat (opsional)..."></textarea>
                                                            <button type="submit" class="mt-1 text-xs px-2 py-1 bg-green-600 text-white rounded">Kirim Lamaran</button>
                                                        </div>
                                                    </form>
                                                @endif
                                            @endif
                                        @endauth

                                        {{-- Show applicants (only for idea owner) --}}
                                        @auth
                                            @if(auth()->id() === $idea->user_id && $position->applications->count() > 0)
                                                <div class="mt-2 space-y-1">
                                                    <p class="text-xs font-medium text-gray-600">Pelamar:</p>
                                                    @foreach($position->applications as $app)
                                                        <div class="flex items-center justify-between text-xs bg-white rounded p-1.5 border">
                                                            <span>{{ $app->user->name }}</span>
                                                            @if($app->status === 'pending')
                                                                <div class="flex space-x-1">
                                                                    <form method="POST" action="{{ route('applications.update', $app) }}">
                                                                        @csrf @method('PATCH')
                                                                        <input type="hidden" name="status" value="accepted">
                                                                        <button type="submit" class="px-1.5 py-0.5 bg-green-500 text-white rounded">✓</button>
                                                                    </form>
                                                                    <form method="POST" action="{{ route('applications.update', $app) }}">
                                                                        @csrf @method('PATCH')
                                                                        <input type="hidden" name="status" value="rejected">
                                                                        <button type="submit" class="px-1.5 py-0.5 bg-red-500 text-white rounded">✗</button>
                                                                    </form>
                                                                </div>
                                                            @else
                                                                <span class="{{ $app->status === 'accepted' ? 'text-green-600' : 'text-red-500' }}">
                                                                    {{ $app->status === 'accepted' ? '✅ Diterima' : '❌ Ditolak' }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endauth
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-400">Belum ada lowongan tim.</p>
                        @endif

                        {{-- Add Position (idea owner only) --}}
                        @auth
                            @if(auth()->id() === $idea->user_id)
                                <div class="mt-3 pt-3 border-t" x-data="{ showForm: false }">
                                    <button @click="showForm = !showForm"
                                            class="text-sm text-indigo-600 hover:text-indigo-800">
                                        ➕ Tambah Posisi Tim
                                    </button>
                                    <form x-show="showForm" method="POST" action="{{ route('ideas.positions.store', $idea) }}" class="mt-2 space-y-2">
                                        @csrf
                                        <input type="text" name="title" required
                                               class="w-full text-sm rounded-lg border-gray-300"
                                               placeholder="Contoh: UI/UX Designer">
                                        <textarea name="description" rows="2"
                                                  class="w-full text-sm rounded-lg border-gray-300"
                                                  placeholder="Deskripsi posisi..."></textarea>
                                        <button type="submit" class="text-xs px-3 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                            Simpan
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @endauth
                    </div>

                    {{-- Idea Stats --}}
                    <div class="bg-white rounded-xl shadow-sm p-5">
                        <h3 class="font-semibold text-gray-900 mb-3">📈 Statistik</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Score</span>
                                <span class="font-semibold {{ $idea->score >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $idea->score >= 0 ? '+' : '' }}{{ $idea->score }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Upvotes</span>
                                <span class="text-green-600">{{ $idea->upvotes_count }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Downvotes</span>
                                <span class="text-red-500">{{ $idea->downvotes_count }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Komentar</span>
                                <span>{{ $idea->comments_count }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Dibuat</span>
                                <span>{{ $idea->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Back Link --}}
                    <a href="{{ route('ideas.index') }}"
                       class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                        ← Kembali ke Papan Inovasi
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function voteSystem(ideaId, initialVoteType, initialUpvotes, initialDownvotes) {
            return {
                upvotes: initialUpvotes,
                downvotes: initialDownvotes,
                userVoteType: initialVoteType || null,
                loading: false,

                async vote(type) {
                    if (this.loading) return;
                    this.loading = true;

                    try {
                        const response = await fetch(`/ideas/${ideaId}/vote`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({ type })
                        });

                        const data = await response.json();
                        this.upvotes = data.upvotes_count;
                        this.downvotes = data.downvotes_count;
                        this.userVoteType = data.user_vote_type;
                    } catch (error) {
                        console.error('Vote error:', error);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
