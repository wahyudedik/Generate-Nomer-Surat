<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Format Surat') }}
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
                <a href="{{ route('letter-formats.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-500">
                    Tambah Format
                </a>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 sm:p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-600 dark:text-gray-400">
                            <tr>
                                <th class="py-2">Nama</th>
                                <th class="py-2">Jenis</th>
                                <th class="py-2">Segmen</th>
                                <th class="py-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($formats as $format)
                                <tr class="text-gray-700 dark:text-gray-300">
                                    <td class="py-3 font-semibold">{{ $format->name }}</td>
                                    <td class="py-3">{{ $format->type === 'out' ? 'Keluar' : 'Masuk' }}</td>
                                    <td class="py-3 text-xs">
                                        @foreach ($format->segments as $segment)
                                            <span class="inline-flex items-center px-2 py-1 bg-gray-100 rounded text-gray-700 mb-1">
                                                {{ $segment->kind }}{{ $segment->value ? ':' . $segment->value : '' }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="py-3 text-right space-x-2">
                                        <a class="text-indigo-600 hover:underline" href="{{ route('letter-formats.edit', $format) }}">Edit</a>
                                        <form class="inline" method="POST" action="{{ route('letter-formats.destroy', $format) }}" onsubmit="return confirm('Hapus format ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 text-center text-gray-500">Belum ada format.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
