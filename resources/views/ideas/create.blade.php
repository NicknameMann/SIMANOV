<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ✨ {{ __('Buat Ide Inovasi Baru') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <form method="POST" action="{{ route('ideas.store') }}">
                    @csrf

                    {{-- Title --}}
                    <div class="mb-5">
                        <x-input-label for="title" :value="__('Judul Ide')" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
                                      :value="old('title')" required autofocus
                                      placeholder="Contoh: Aplikasi Penghubung Mahasiswa & Industri" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    {{-- Category --}}
                    <div class="mb-5">
                        <x-input-label for="category" :value="__('Kategori')" />
                        <select id="category" name="category" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach(['teknologi' => '💻 Teknologi', 'sosial' => '🤝 Sosial', 'pendidikan' => '📚 Pendidikan', 'bisnis' => '💼 Bisnis', 'lainnya' => '📌 Lainnya'] as $val => $label)
                                <option value="{{ $val }}" {{ old('category') == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category')" class="mt-2" />
                    </div>

                    {{-- Description --}}
                    <div class="mb-5">
                        <x-input-label for="description" :value="__('Deskripsi Lengkap')" />
                        <textarea id="description" name="description" rows="8" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Jelaskan ide inovasi kamu secara detail: latar belakang masalah, solusi yang ditawarkan, dan manfaat yang diharapkan...">{{ old('description') }}</textarea>
                        <p class="mt-1 text-xs text-gray-400">Minimal 20 karakter</p>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    {{-- Tags --}}
                    <div class="mb-5" x-data="{ tags: [], newTag: '' }">
                        <x-input-label for="tags" :value="__('Tags (opsional)')" />
                        <div class="mt-1 flex flex-wrap gap-2 items-center">
                            <template x-for="(tag, index) in tags" :key="index">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <span x-text="'#' + tag"></span>
                                    <input type="hidden" :name="'tags[' + index + ']'" :value="tag">
                                    <button type="button" @click="tags.splice(index, 1)" class="ml-1 text-indigo-600 hover:text-indigo-800">×</button>
                                </span>
                            </template>
                            <input type="text" x-model="newTag"
                                   @keydown.enter.prevent="if(newTag.trim() && tags.length < 5) { tags.push(newTag.trim().toLowerCase()); newTag = ''; }"
                                   @keydown.comma.prevent="if(newTag.trim() && tags.length < 5) { tags.push(newTag.trim().toLowerCase()); newTag = ''; }"
                                   class="border-0 border-b border-gray-200 focus:ring-0 focus:border-indigo-500 text-sm px-1 py-0.5 w-32"
                                   placeholder="Ketik & Enter..." />
                        </div>
                        <p class="mt-1 text-xs text-gray-400">Maksimal 5 tag. Tekan Enter atau koma untuk menambah.</p>
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t">
                        <a href="{{ route('ideas.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Batal</a>
                        <x-primary-button>
                            🚀 {{ __('Publikasikan Ide') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
