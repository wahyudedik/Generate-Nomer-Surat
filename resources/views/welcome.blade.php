<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-gray-50 text-gray-900 min-h-screen">
        <header class="border-b border-gray-200 bg-white">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <div class="text-lg font-semibold">{{ config('app.name', 'Nomer Surat') }}</div>
                @if (Route::has('login'))
                    <nav class="flex items-center gap-3 text-sm">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-md border border-gray-300 hover:border-gray-400">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 rounded-md border border-transparent hover:border-gray-300">
                                Login
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-500">
                                    Daftar
                                </a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        <main>
            <section class="bg-white">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 grid gap-8 lg:grid-cols-2">
                    <div class="space-y-5">
                        <div class="inline-flex items-center gap-2 text-xs font-semibold tracking-wide bg-indigo-50 text-indigo-700 px-3 py-1 rounded-full">
                            Sistem Penomoran Surat
                        </div>
                        <h1 class="text-3xl sm:text-4xl font-bold leading-tight">
                            Generate nomor surat tanpa duplikasi, sesuai format instansi Anda.
                        </h1>
                        <p class="text-gray-600">
                            Buat format nomor surat kustom per unit, generate otomatis, dan simpan arsip scan surat masuk/keluar dalam satu sistem.
                        </p>
                        <div class="flex items-center gap-3">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-500">
                                    Buka Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-md bg-indigo-600 text-white hover:bg-indigo-500">
                                    Mulai Sekarang
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-md border border-gray-300 hover:border-gray-400">
                                        Buat Akun
                                    </a>
                                @endif
                            @endauth
                        </div>
                    </div>
                    <div class="bg-indigo-50 rounded-2xl p-6 shadow-sm">
                        <div class="text-sm text-gray-600 mb-3">Contoh format:</div>
                        <div class="bg-white rounded-xl p-4 border border-indigo-100 space-y-3">
                            <div class="text-xs text-gray-500">Nomor surat keluar</div>
                            <div class="text-lg font-semibold text-gray-800">001/SK/UNIT/IX/2026</div>
                            <div class="text-xs text-gray-500">Reset bulanan, counter per unit.</div>
                        </div>
                        <div class="mt-6 grid grid-cols-2 gap-4 text-sm text-gray-600">
                            <div class="bg-white rounded-lg p-3 border border-gray-100">Format fleksibel</div>
                            <div class="bg-white rounded-lg p-3 border border-gray-100">Anti duplikasi</div>
                            <div class="bg-white rounded-lg p-3 border border-gray-100">Arsip scan PDF</div>
                            <div class="bg-white rounded-lg p-3 border border-gray-100">Kontrol per unit</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                <div class="grid gap-6 md:grid-cols-3">
                    <div class="p-5 bg-white rounded-xl border border-gray-200">
                        <h3 class="font-semibold mb-2">1. Buat format</h3>
                        <p class="text-sm text-gray-600">Susun segmen nomor surat sesuai kebutuhan instansi.</p>
                    </div>
                    <div class="p-5 bg-white rounded-xl border border-gray-200">
                        <h3 class="font-semibold mb-2">2. Generate nomor</h3>
                        <p class="text-sm text-gray-600">Nomor otomatis dan berurutan, sesuai scope unit.</p>
                    </div>
                    <div class="p-5 bg-white rounded-xl border border-gray-200">
                        <h3 class="font-semibold mb-2">3. Arsipkan</h3>
                        <p class="text-sm text-gray-600">Isi metadata dan unggah scan PDF untuk arsip.</p>
                    </div>
                </div>
            </section>

            <section class="bg-indigo-600">
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-white flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold">Siap rapikan administrasi surat Anda?</h2>
                        <p class="text-indigo-100 text-sm">Mulai sekarang dan atur format nomor surat sesuai kebutuhan.</p>
                    </div>
                    @guest
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-md bg-white text-indigo-700 font-medium">
                            Daftar Gratis
                        </a>
                    @endguest
                </div>
            </section>
        </main>
    </body>
</html>
