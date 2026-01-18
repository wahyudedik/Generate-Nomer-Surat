<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Log Aktivitas Surat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Draft Tertahan</h3>
                <p class="text-sm text-gray-500">Daftar surat keluar draft yang belum selesai.</p>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-600 dark:text-gray-400">
                            <tr>
                                <th class="py-2">Nomor</th>
                                <th class="py-2">Pembuat</th>
                                <th class="py-2">Terakhir Update</th>
                                <th class="py-2">Status</th>
                                <th class="py-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($openDrafts as $letter)
                                @php
                                    $lastLog = $letter->activityLogs->first();
                                @endphp
                                <tr class="text-gray-700 dark:text-gray-300">
                                    <td class="py-3 font-semibold">{{ $letter->number ?? '-' }}</td>
                                    <td class="py-3">{{ $letter->creator?->name ?? '-' }}</td>
                                    <td class="py-3">
                                        {{ $lastLog?->created_at?->diffForHumans() ?? $letter->created_at->diffForHumans() }}
                                    </td>
                                    <td class="py-3">
                                        <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">Draft</span>
                                    </td>
                                    <td class="py-3 text-right">
                                        <a class="text-indigo-600 hover:underline" href="{{ route('letters.out.edit', $letter) }}">Lengkapi</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500">Tidak ada draft tertahan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aktivitas Terbaru</h3>
                    <p class="text-sm text-gray-500">Riwayat generate dan penyelesaian surat.</p>
                </div>
                <div class="px-4 sm:px-6 pb-4">
                    <form method="GET" class="grid grid-cols-1 sm:grid-cols-7 gap-3 items-end">
                        <div>
                            <x-input-label for="user_id" value="User" />
                            <select id="user_id" name="user_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="action" value="Aksi" />
                            <select id="action" name="action" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua</option>
                                @foreach ($actions as $action)
                                    <option value="{{ $action }}" @selected(request('action') === $action)>
                                        {{ $action }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="letter_type" value="Jenis" />
                            <select id="letter_type" name="letter_type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua</option>
                                @foreach ($types as $value => $label)
                                    <option value="{{ $value }}" @selected(request('letter_type') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="letter_status" value="Status" />
                            <select id="letter_status" name="letter_status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua</option>
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected(request('letter_status') === $value)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="start_date" value="Mulai" />
                            <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" value="{{ request('start_date') }}" />
                        </div>
                        <div>
                            <x-input-label for="end_date" value="Sampai" />
                            <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" value="{{ request('end_date') }}" />
                        </div>
                        <div class="flex gap-2">
                            <x-primary-button>Filter</x-primary-button>
                            <a href="{{ route('letters.logs.index') }}" class="text-sm text-gray-600 dark:text-gray-400">Reset</a>
                        </div>
                    </form>
                    <div class="mt-3">
                        <a class="text-sm text-indigo-600 hover:underline"
                           href="{{ route('letters.logs.index', array_merge(request()->except('page'), ['export' => 'csv'])) }}">
                            Export CSV
                        </a>
                    </div>
                </div>
                <div class="px-4 sm:px-6 pb-4 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-600 dark:text-gray-400">
                            <tr>
                                <th class="py-2">Waktu</th>
                                <th class="py-2">User</th>
                                <th class="py-2">Jenis</th>
                                <th class="py-2">Status</th>
                                <th class="py-2">Nomor</th>
                                <th class="py-2">Aksi</th>
                                <th class="py-2">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($recentLogs as $log)
                                <tr class="text-gray-700 dark:text-gray-300">
                                    <td class="py-3">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="py-3">{{ $log->user?->name ?? '-' }}</td>
                                    <td class="py-3">
                                        {{ $log->letter?->type === 'out' ? 'Keluar' : 'Masuk' }}
                                    </td>
                                    <td class="py-3">
                                        {{ $log->letter?->status === \App\Models\Letter::STATUS_DRAFT ? 'Draft' : 'Selesai' }}
                                    </td>
                                    <td class="py-3">{{ $log->letter?->number ?? '-' }}</td>
                                    <td class="py-3">{{ $log->action }}</td>
                                    <td class="py-3">{{ $log->note ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center text-gray-500">Belum ada log.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 sm:px-6 pb-6">
                    {{ $recentLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
