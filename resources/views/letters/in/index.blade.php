<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Surat Masuk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            <div class="flex justify-end">
                <a href="{{ route('letters.in.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-500">
                    Tambah Surat Masuk
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 sm:p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-600 dark:text-gray-400">
                            <tr>
                                <th class="py-2">Judul</th>
                                <th class="py-2">Tanggal</th>
                                <th class="py-2 text-right">Scan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($letters as $letter)
                                <tr class="text-gray-700 dark:text-gray-300">
                                    <td class="py-3">{{ $letter->title }}</td>
                                    <td class="py-3">{{ optional($letter->issued_at)->format('d/m/Y') ?? '-' }}</td>
                                    <td class="py-3 text-right">
                                        @if ($letter->scan_path)
                                            <a class="text-sm text-gray-600 hover:underline" href="{{ asset('storage/' . $letter->scan_path) }}" target="_blank" rel="noopener">Lihat Scan</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-gray-500">Belum ada surat masuk.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $letters->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
