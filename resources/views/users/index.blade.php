<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-md">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('user'))
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-md">
                    {{ $errors->first('user') }}
                </div>
            @endif

            <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg space-y-4">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div class="sm:col-span-2">
                        <x-input-label for="search" value="Cari nama/email" />
                        <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" value="{{ $search }}" />
                    </div>
                    <div>
                        <x-input-label for="status" value="Status" />
                        <select id="status" name="status" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua</option>
                            <option value="active" @selected($status === 'active')>Aktif</option>
                            <option value="inactive" @selected($status === 'inactive')>Nonaktif</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua</option>
                            @foreach ($roles as $roleOption)
                                <option value="{{ $roleOption->name }}" @selected($role === $roleOption->name)>
                                    {{ ucfirst($roleOption->name) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-4 flex items-center gap-2">
                        <x-primary-button>Filter</x-primary-button>
                        <a href="{{ route('users.index') }}" class="text-sm text-gray-600 dark:text-gray-400">Reset</a>
                        <div class="ms-auto">
                            <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-500">
                                Tambah User
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <div class="p-4 sm:p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-gray-600 dark:text-gray-400">
                            <tr>
                                <th class="py-2">User</th>
                                <th class="py-2">Email</th>
                                <th class="py-2">Role</th>
                                <th class="py-2">Status</th>
                                <th class="py-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($users as $user)
                                <tr class="text-gray-700 dark:text-gray-300">
                                    <td class="py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-gray-200 overflow-hidden">
                                                @if ($user->avatar_path)
                                                    <img class="h-10 w-10 object-cover" src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}">
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-semibold">{{ $user->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $user->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">{{ $user->email }}</td>
                                    <td class="py-3">
                                        {{ $user->roles->pluck('name')->map(fn ($name) => ucfirst($name))->join(', ') ?: '-' }}
                                    </td>
                                    <td class="py-3">
                                        @if ($user->is_active)
                                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Aktif</span>
                                        @else
                                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right space-x-2">
                                        <a class="text-indigo-600 hover:underline" href="{{ route('users.edit', $user) }}">Edit</a>
                                        <form class="inline" method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-center text-gray-500">Data user belum ada.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
