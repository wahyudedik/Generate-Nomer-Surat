<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Surat Keluar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('letter'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-md">
                    {{ $errors->first('letter') }}
                </div>
            @endif

            <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Generate Nomor</h3>
                        <p class="text-sm text-gray-500">Pilih format nomor untuk membuat surat keluar baru.</p>
                    </div>
                    @role('admin')
                        <a class="text-sm text-indigo-600 hover:underline" href="{{ route('letter-formats.index') }}">Kelola format</a>
                    @endrole
                </div>

                @if ($hasDraft)
                    <div class="p-3 bg-yellow-50 border border-yellow-200 text-yellow-700 rounded-md text-sm">
                    Surat keluar terakhir belum lengkap.
                    @if ($draftLetter)
                        <div class="mt-1 text-xs text-yellow-700">
                            Draft oleh <span class="font-semibold">{{ $draftLetter->creator?->name ?? 'Unknown' }}</span>
                            @if ($draftLog)
                                ({{ $draftLog->created_at->format('d/m/Y H:i') }})
                            @endif
                            • Nomor: {{ $draftLetter->number ?? '-' }}
                            • <a class="underline" href="{{ route('letters.out.edit', $draftLetter) }}">Lengkapi sekarang</a>
                        </div>
                    @endif
                    </div>
                @endif

                <form method="POST" action="{{ route('letters.out.generate') }}" class="flex flex-col sm:flex-row gap-3 items-end">
                    @csrf
                    <div class="flex-1">
                        <x-input-label for="format_id" value="Format nomor" />
                        <select id="format_id" name="format_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required @disabled($hasDraft)>
                            <option value="" disabled selected>Pilih format</option>
                            @foreach ($formats as $format)
                                <option value="{{ $format->id }}">{{ $format->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-primary-button :disabled="$hasDraft">Generate</x-primary-button>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 sm:p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-600 dark:text-gray-400">
                            <tr>
                                <th class="py-2">Nomor</th>
                                <th class="py-2">Judul</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Tanggal</th>
                                <th class="py-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($letters as $letter)
                                <tr class="text-gray-700 dark:text-gray-300">
                                    <td class="py-3 font-semibold">{{ $letter->number }}</td>
                                    <td class="py-3">{{ $letter->title ?? '-' }}</td>
                                    <td class="py-3">
                                        @if ($letter->status === \App\Models\Letter::STATUS_DRAFT)
                                            <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">Draft</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Selesai</span>
                                        @endif
                                    </td>
                                    <td class="py-3">{{ optional($letter->issued_at)->format('d/m/Y') ?? '-' }}</td>
                                    <td class="py-3 text-right">
                                        @if ($letter->status === \App\Models\Letter::STATUS_DRAFT)
                                            <a class="text-indigo-600 hover:underline" href="{{ route('letters.out.edit', $letter) }}">Lengkapi</a>
                                        @elseif ($letter->scan_path)
                                            <a class="text-sm text-gray-600 hover:underline" href="{{ asset('storage/' . $letter->scan_path) }}" target="_blank" rel="noopener">Lihat Scan</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500">Belum ada surat keluar.</td>
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
