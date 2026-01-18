<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Lengkapi Surat Keluar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <form method="POST" action="{{ route('letters.out.update', $letter) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label value="Nomor Surat" />
                        <div class="mt-1 text-gray-700 dark:text-gray-300 font-semibold">{{ $letter->number }}</div>
                    </div>

                    <div>
                        <x-input-label for="title" value="Judul Surat" />
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" value="{{ old('title', $letter->title) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('title')" />
                    </div>

                    <div>
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="4">{{ old('description', $letter->description) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('description')" />
                    </div>

                    <div>
                        <x-input-label for="issued_at" value="Tanggal Surat" />
                        <x-text-input id="issued_at" name="issued_at" type="date" class="mt-1 block w-full" value="{{ old('issued_at', optional($letter->issued_at)->format('Y-m-d')) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('issued_at')" />
                    </div>

                    <div>
                        <x-input-label for="scan" value="Upload Scan (PDF)" />
                        <input id="scan" name="scan" type="file" class="mt-1 block w-full text-sm text-gray-600" required />
                        <x-input-error class="mt-2" :messages="$errors->get('scan')" />
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Simpan</x-primary-button>
                        <a href="{{ route('letters.out.index') }}" class="text-sm text-gray-600 dark:text-gray-400">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
