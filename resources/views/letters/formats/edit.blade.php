<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Format Surat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg space-y-6">
                <form method="POST" action="{{ route('letter-formats.update', $format) }}" class="space-y-6" id="format-form">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="type" value="Jenis Surat" />
                            <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="out" @selected(old('type', $format->type) === 'out')>Keluar</option>
                                <option value="in" @selected(old('type', $format->type) === 'in')>Masuk</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('type')" />
                        </div>
                        <div>
                            <x-input-label for="period_mode" value="Reset Counter" />
                            <select id="period_mode" name="period_mode" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="year" @selected(old('period_mode', $format->period_mode) === 'year')>Tahunan</option>
                                <option value="month" @selected(old('period_mode', $format->period_mode) === 'month')>Bulanan</option>
                                <option value="all" @selected(old('period_mode', $format->period_mode) === 'all')>Tidak Reset</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('period_mode')" />
                        </div>
                        <div>
                            <x-input-label for="counter_scope" value="Scope Counter" />
                            <select id="counter_scope" name="counter_scope" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="global" @selected(old('counter_scope', $format->counter_scope) === 'global')>Global</option>
                                <option value="unit" @selected(old('counter_scope', $format->counter_scope) === 'unit')>Per Unit</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Global: nomor berurutan untuk semua unit. Per Unit: nomor terpisah per kode unit.</p>
                            <x-input-error class="mt-2" :messages="$errors->get('counter_scope')" />
                        </div>
                        <div>
                            <x-input-label for="name" value="Nama Format" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $format->name) }}" required />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="description" value="Deskripsi" />
                        <textarea id="description" name="description" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" rows="3">{{ old('description', $format->description) }}</textarea>
                    </div>

                    <div>
                        <div class="flex items-center justify-between">
                            <x-input-label value="Segmen Format" />
                            <button type="button" id="add-segment" class="text-sm text-indigo-600 hover:underline">Tambah Segmen</button>
                        </div>
                        <div class="mt-3 rounded-md border border-indigo-100 bg-indigo-50 p-3 text-sm text-indigo-900">
                            <div class="font-semibold mb-1">Tips menyusun segmen</div>
                            <ul class="space-y-1 text-xs text-indigo-900">
                                <li><span class="font-semibold">sequence</span>: nomor urut (padding = jumlah digit, contoh 3 → 001)</li>
                                <li><span class="font-semibold">text</span>: teks statis seperti <code class="px-1 bg-white rounded">/SK/</code> atau <code class="px-1 bg-white rounded">/</code></li>
                                <li><span class="font-semibold">unit_code</span>: kode unit dari profil user (wajib jika counter per unit)</li>
                                <li><span class="font-semibold">day</span>: tanggal (01-31)</li>
                                <li><span class="font-semibold">month_roman</span>: bulan Romawi (I-XII)</li>
                                <li><span class="font-semibold">month_number</span>: bulan angka (01-12)</li>
                                <li><span class="font-semibold">year</span>: tahun angka (2026)</li>
                                <li><span class="font-semibold">year_roman</span>: tahun Romawi (MMXXVI)</li>
                            </ul>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">Preview: <span id="format-preview" class="font-semibold">-</span></p>
                        <div class="mt-2 overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="text-left text-gray-600">
                                    <tr>
                                        <th class="py-2">Kind</th>
                                        <th class="py-2">Value</th>
                                        <th class="py-2">Padding</th>
                                    </tr>
                                </thead>
                                <tbody id="segments-body" class="divide-y divide-gray-200">
                                    @php
                                        $segments = $format->segments->values();
                                        $rows = max(3, $segments->count());
                                    @endphp
                                    @for ($i = 0; $i < $rows; $i++)
                                        @php
                                            $segment = $segments[$i] ?? null;
                                        @endphp
                                        <tr>
                                            <td class="py-2">
                                                <select name="segments[{{ $i }}][kind]" class="w-full rounded-md border-gray-300 segment-kind">
                                                    <option value="">-- pilih --</option>
                                                    <option value="sequence" @selected(old("segments.$i.kind", $segment?->kind) === 'sequence')>sequence</option>
                                                    <option value="text" @selected(old("segments.$i.kind", $segment?->kind) === 'text')>text</option>
                                                    <option value="unit_code" @selected(old("segments.$i.kind", $segment?->kind) === 'unit_code')>unit_code</option>
                                                    <option value="day" @selected(old("segments.$i.kind", $segment?->kind) === 'day')>day</option>
                                                    <option value="month_roman" @selected(old("segments.$i.kind", $segment?->kind) === 'month_roman')>month_roman</option>
                                                    <option value="month_number" @selected(old("segments.$i.kind", $segment?->kind) === 'month_number')>month_number</option>
                                                    <option value="year" @selected(old("segments.$i.kind", $segment?->kind) === 'year')>year</option>
                                                    <option value="year_roman" @selected(old("segments.$i.kind", $segment?->kind) === 'year_roman')>year_roman</option>
                                                </select>
                                            </td>
                                            <td class="py-2">
                                                <input name="segments[{{ $i }}][value]" type="text" class="w-full rounded-md border-gray-300 segment-value" value="{{ old("segments.$i.value", $segment?->value) }}" />
                                            </td>
                                            <td class="py-2">
                                                <input name="segments[{{ $i }}][padding]" type="number" min="0" max="10" class="w-full rounded-md border-gray-300 segment-padding" value="{{ old("segments.$i.padding", $segment?->padding) }}" />
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-primary-button>Simpan</x-primary-button>
                        <a href="{{ route('letter-formats.index') }}" class="text-sm text-gray-600">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const body = document.getElementById('segments-body');
            const addButton = document.getElementById('add-segment');
            const preview = document.getElementById('format-preview');

            const monthRoman = (month) => {
                const map = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
                return map[month - 1] ?? '';
            };

            const toRoman = (num) => {
                const map = [
                    [1000, 'M'], [900, 'CM'], [500, 'D'], [400, 'CD'],
                    [100, 'C'], [90, 'XC'], [50, 'L'], [40, 'XL'],
                    [10, 'X'], [9, 'IX'], [5, 'V'], [4, 'IV'], [1, 'I'],
                ];
                let result = '';
                let value = num;
                map.forEach(([n, r]) => {
                    while (value >= n) {
                        result += r;
                        value -= n;
                    }
                });
                return result;
            };

            const updatePreview = () => {
                const now = new Date();
                const day = String(now.getDate()).padStart(2, '0');
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const year = String(now.getFullYear());

                const parts = Array.from(body.querySelectorAll('tr')).map((row) => {
                    const kind = row.querySelector('.segment-kind')?.value;
                    const value = row.querySelector('.segment-value')?.value || '';
                    const padding = parseInt(row.querySelector('.segment-padding')?.value || '0', 10);

                    switch (kind) {
                        case 'sequence':
                            return String(1).padStart(padding, '0');
                        case 'text':
                            return value;
                        case 'unit_code':
                            return 'UNIT';
                        case 'day':
                            return day;
                        case 'month_roman':
                            return monthRoman(parseInt(month, 10));
                        case 'month_number':
                            return month;
                        case 'year':
                            return year;
                        case 'year_roman':
                            return toRoman(parseInt(year, 10));
                        default:
                            return '';
                    }
                }).filter(Boolean);

                preview.textContent = parts.length ? parts.join('') : '-';
            };

            addButton.addEventListener('click', () => {
                const index = body.children.length;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="py-2">
                        <select name="segments[${index}][kind]" class="w-full rounded-md border-gray-300 segment-kind">
                            <option value="">-- pilih --</option>
                            <option value="sequence">sequence</option>
                            <option value="text">text</option>
                            <option value="unit_code">unit_code</option>
                            <option value="day">day</option>
                            <option value="month_roman">month_roman</option>
                            <option value="month_number">month_number</option>
                            <option value="year">year</option>
                            <option value="year_roman">year_roman</option>
                        </select>
                    </td>
                    <td class="py-2">
                        <input name="segments[${index}][value]" type="text" class="w-full rounded-md border-gray-300 segment-value" />
                    </td>
                    <td class="py-2">
                        <input name="segments[${index}][padding]" type="number" min="0" max="10" class="w-full rounded-md border-gray-300 segment-padding" />
                    </td>
                `;
                body.appendChild(row);
                updatePreview();
            });

            body.addEventListener('input', updatePreview);
            body.addEventListener('change', updatePreview);
            updatePreview();
        })();
    </script>
</x-app-layout>
