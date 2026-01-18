<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="p-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="text-sm text-gray-500">Surat Keluar</div>
                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['totalOut'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">30 hari: {{ $stats['out30'] }}</div>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="text-sm text-gray-500">Draft Keluar</div>
                    <div class="text-2xl font-semibold text-yellow-600">{{ $stats['draftOut'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Butuh upload scan</div>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="text-sm text-gray-500">Selesai Keluar</div>
                    <div class="text-2xl font-semibold text-green-600">{{ $stats['completedOut'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">Total selesai</div>
                </div>
                <div class="p-4 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="text-sm text-gray-500">Surat Masuk</div>
                    <div class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $stats['totalIn'] }}</div>
                    <div class="text-xs text-gray-400 mt-1">30 hari: {{ $stats['in30'] }}</div>
                </div>
            </div>

            @if ($blockingDraft)
                <div class="p-4 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-md text-sm">
                    Nomor surat keluar sedang terkunci oleh draft:
                    <span class="font-semibold">{{ $blockingDraft->creator?->name ?? 'Unknown' }}</span>
                    ({{ $blockingDraft->number ?? '-' }}).
                    <a class="underline" href="{{ route('letters.out.edit', $blockingDraft) }}">Lengkapi sekarang</a>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ $isAdmin ? 'Draft Terbaru (Global)' : 'Draft Saya' }}
                        </h3>
                        <p class="text-sm text-gray-500">Ringkasan draft yang belum selesai.</p>
                    </div>
                    <div class="px-4 sm:px-6 pb-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-gray-600 dark:text-gray-400">
                                <tr>
                                    <th class="py-2">Nomor</th>
                                    <th class="py-2">Tanggal</th>
                                    <th class="py-2 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($drafts as $draft)
                                    <tr class="text-gray-700 dark:text-gray-300">
                                        <td class="py-3 font-semibold">{{ $draft->number ?? '-' }}</td>
                                        <td class="py-3">{{ $draft->created_at->format('d/m/Y') }}</td>
                                        <td class="py-3 text-right">
                                            <a class="text-indigo-600 hover:underline" href="{{ route('letters.out.edit', $draft) }}">Lengkapi</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-gray-500">Tidak ada draft.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="p-4 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            Aktivitas Terbaru
                        </h3>
                        <p class="text-sm text-gray-500">Generate dan penyelesaian surat.</p>
                    </div>
                    <div class="px-4 sm:px-6 pb-4 overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="text-left text-gray-600 dark:text-gray-400">
                                <tr>
                                    <th class="py-2">Waktu</th>
                                    <th class="py-2">Nomor</th>
                                    <th class="py-2">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($recentLogs as $log)
                                    <tr class="text-gray-700 dark:text-gray-300">
                                        <td class="py-3">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="py-3">{{ $log->letter?->number ?? '-' }}</td>
                                        <td class="py-3">{{ $log->action }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 text-center text-gray-500">Belum ada aktivitas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 sm:px-6 pb-6">
                        <a class="text-sm text-indigo-600 hover:underline" href="{{ route('letters.logs.index') }}">
                            Lihat semua log
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
