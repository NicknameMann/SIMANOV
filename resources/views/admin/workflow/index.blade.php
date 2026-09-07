<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ⚙️ {{ __('Manajemen Workflow') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Add New Stage --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-4">➕ Tambah Tahap Baru</h3>
                <form method="POST" action="{{ route('admin.workflow.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <x-input-label for="name" :value="__('Nama Tahap')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required placeholder="Contoh: Peer Review" />
                        <x-input-error :messages="$errors->get('name')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="slug" :value="__('Slug')" />
                        <x-text-input id="slug" name="slug" type="text" class="mt-1 block w-full" required placeholder="peer-review" />
                        <x-input-error :messages="$errors->get('slug')" class="mt-1" />
                    </div>
                    <div>
                        <x-input-label for="order" :value="__('Urutan')" />
                        <x-text-input id="order" name="order" type="number" class="mt-1 block w-full" required placeholder="1" />
                    </div>
                    <div>
                        <x-input-label for="color" :value="__('Warna (hex)')" />
                        <x-text-input id="color" name="color" type="color" class="mt-1 block w-full h-10" value="#6366f1" />
                    </div>
                    <div>
                        <x-input-label for="auto_advance_threshold" :value="__('Auto-Advance Threshold (upvotes)')" />
                        <x-text-input id="auto_advance_threshold" name="auto_advance_threshold" type="number" class="mt-1 block w-full" placeholder="Opsional" />
                    </div>
                    <div>
                        <x-input-label for="description" :value="__('Deskripsi')" />
                        <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" placeholder="Opsional" />
                    </div>
                    <div class="md:col-span-2">
                        <x-primary-button>💾 Simpan Tahap</x-primary-button>
                    </div>
                </form>
            </div>

            {{-- Existing Stages --}}
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-900 mb-4">📋 Tahapan Workflow Saat Ini</h3>

                @if($stages->count() > 0)
                    <div class="space-y-3">
                        @foreach($stages as $stage)
                            <div class="border rounded-lg p-4 hover:border-indigo-200 transition" x-data="{ editing: false }">
                                {{-- View Mode --}}
                                <div x-show="!editing" class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-4 h-4 rounded-full" style="background-color: {{ $stage->color ?? '#6366f1' }}"></div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $stage->name }}</h4>
                                            <p class="text-xs text-gray-500">
                                                Slug: {{ $stage->slug }} · Urutan: {{ $stage->order }}
                                                @if($stage->auto_advance_threshold)
                                                    · Auto-advance: {{ $stage->auto_advance_threshold }} upvotes
                                                @endif
                                                · {{ $stage->ideas()->count() }} ide
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button @click="editing = true" class="text-sm text-indigo-600 hover:text-indigo-800">✏️</button>
                                        <form method="POST" action="{{ route('admin.workflow.destroy', $stage) }}"
                                              onsubmit="return confirm('Hapus tahap ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-sm text-red-500 hover:text-red-700">🗑️</button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Edit Mode --}}
                                <form x-show="editing" method="POST" action="{{ route('admin.workflow.update', $stage) }}" class="space-y-3">
                                    @csrf @method('PUT')
                                    <div class="grid grid-cols-2 gap-3">
                                        <input type="text" name="name" value="{{ $stage->name }}" required class="text-sm rounded border-gray-300">
                                        <input type="text" name="slug" value="{{ $stage->slug }}" required class="text-sm rounded border-gray-300">
                                        <input type="number" name="order" value="{{ $stage->order }}" required class="text-sm rounded border-gray-300">
                                        <input type="color" name="color" value="{{ $stage->color ?? '#6366f1' }}" class="text-sm rounded border-gray-300 h-9">
                                        <input type="number" name="auto_advance_threshold" value="{{ $stage->auto_advance_threshold }}" placeholder="Auto-advance" class="text-sm rounded border-gray-300">
                                        <input type="text" name="description" value="{{ $stage->description }}" placeholder="Deskripsi" class="text-sm rounded border-gray-300">
                                    </div>
                                    <div class="flex space-x-2">
                                        <button type="submit" class="text-xs px-3 py-1.5 bg-indigo-600 text-white rounded hover:bg-indigo-700">Simpan</button>
                                        <button type="button" @click="editing = false" class="text-xs px-3 py-1.5 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Batal</button>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-400 text-center py-4">Belum ada tahap workflow.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
