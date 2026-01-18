<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="name" value="Nama" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $user->name) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <x-input-label for="unit_code" value="Kode Unit" />
                        <x-text-input id="unit_code" name="unit_code" type="text" class="mt-1 block w-full" value="{{ old('unit_code', $user->unit_code) }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('unit_code')" />
                    </div>

                    <div>
                        <x-input-label for="email" value="Email" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ old('email', $user->email) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="password" value="Password Baru (opsional)" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" value="Konfirmasi Password Baru" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="role" value="Role" />
                        <select id="role" name="role" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->name }}" @selected(old('role', $currentRole) === $role->name)>
                                    {{ ucfirst($role->name) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('role')" />
                    </div>

                    <div>
                        <x-input-label for="avatar" value="Avatar" />
                        @if ($user->avatar_path)
                            <div class="mt-2">
                                <img class="h-16 w-16 rounded-full object-cover" src="{{ asset('storage/' . $user->avatar_path) }}" alt="{{ $user->name }}">
                            </div>
                        @endif
                        <input id="avatar" name="avatar" type="file" class="mt-2 block w-full text-sm text-gray-600" />
                        <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                    </div>

                    <div class="flex items-center gap-2">
                        <input id="is_active" name="is_active" type="checkbox" class="rounded border-gray-300" value="1" @checked(old('is_active', $user->is_active)) />
                        <label for="is_active" class="text-sm text-gray-700 dark:text-gray-300">Aktif</label>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Update</x-primary-button>
                        <a href="{{ route('users.index') }}" class="text-sm text-gray-600 dark:text-gray-400">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
