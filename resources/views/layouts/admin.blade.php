{{--
    Layout admin Nabe Coffee (tugas 2.1).

    Variabel yang bisa dikirim dari route/controller. Kalau tidak dikirim,
    dipakai data dummy di bawah ini sampai login & role (tugas Galih #1, #2) masuk:
      $role       'owner' atau 'branch'
      $userName   nama user yang login
      $branchName nama cabang (untuk admin cabang)
--}}
@php
    $role = $role ?? 'owner';
    $userName = $userName ?? ($role === 'owner' ? 'Owner Demo' : 'Admin Cabang Demo');
    $branchName = $branchName ?? ($role === 'owner' ? 'Semua cabang' : 'Nabe Coffee Kusumanegara');
    $initials = $role === 'owner' ? 'OW' : 'AD';

    // Selama masih mode demo, pertahankan ?role= di semua tautan menu
    $demoParams = request()->only('role');

    // Ikon SVG garis (stroke 1.75), isi di dalam <svg>
    $icons = [
        'dashboard' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
        'cabang' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        'menu' => '<path d="M18 8h1a4 4 0 0 1 0 8h-1"/><path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"/><line x1="6" y1="1" x2="6" y2="4"/><line x1="10" y1="1" x2="10" y2="4"/><line x1="14" y1="1" x2="14" y2="4"/>',
        'kuota' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
        'cms' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'analitik' => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
        'reservasi' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
        'pelanggan' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'notifikasi' => '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
        'pembayaran' => '<rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/>',
    ];

    // Menu sidebar. 'route' = nama route. Kalau route belum dibuat oleh tugas
    // lain, menu otomatis tampil nonaktif. Begitu route-nya ada, otomatis aktif.
    $menuOwner = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'admin.dashboard'],
        ['label' => 'Cabang', 'icon' => 'cabang', 'route' => 'admin.branches.index'],
        ['label' => 'Menu', 'icon' => 'menu', 'route' => 'admin.menus.index'],
        ['label' => 'Kuota', 'icon' => 'kuota', 'route' => 'admin.capacity-slots.index'],
        ['label' => 'CMS', 'icon' => 'cms', 'route' => 'admin.cms.index'],
        ['label' => 'Analitik', 'icon' => 'analitik', 'route' => 'admin.analytics'],
    ];
    $menuBranch = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'admin.dashboard'],
        ['label' => 'Reservasi Hari Ini', 'icon' => 'reservasi', 'route' => 'admin.reservations.today'],
    ];
    // Belum ada tugasnya: tampil tapi nonaktif
    $menuSoon = [
        ['label' => 'Pelanggan', 'icon' => 'pelanggan'],
        ['label' => 'Notifikasi', 'icon' => 'notifikasi'],
        ['label' => 'Pembayaran', 'icon' => 'pembayaran'],
    ];
    $menuItems = $role === 'owner' ? $menuOwner : $menuBranch;
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · Admin Nabe Coffee</title>
    <link rel="icon" href="{{ asset('images/brand/favicon-64.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
<div class="flex min-h-screen">

    {{-- Latar gelap saat sidebar terbuka di HP --}}
    <div id="admin-backdrop" class="fixed inset-0 z-30 hidden bg-ink/40 lg:hidden"></div>

    {{-- Sidebar --}}
    <aside id="admin-sidebar"
           class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-line bg-white transition-transform lg:sticky lg:top-0 lg:h-screen lg:translate-x-0">
        <div class="flex items-center justify-between px-5 pb-3 pt-5">
            <a href="{{ url('/') }}" aria-label="Nabe Coffee, ke website">
                <img src="{{ asset('images/brand/logo-nabe-160.png') }}" alt="Nabe Coffee" class="h-14 w-auto">
            </a>
            <button type="button" id="sidebar-close" aria-label="Tutup menu"
                    class="rounded-[5px] p-2 text-ink hover:bg-soft lg:hidden">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <p class="px-5 pb-3 text-[11px] font-bold tracking-wider text-muted">RUANG KERJA ADMIN</p>

        {{-- Identitas user untuk layar kecil (di layar lebar ada di topbar) --}}
        <div class="mx-3 mb-3 rounded-[5px] border border-line bg-cream px-3 py-2.5 sm:hidden">
            <p class="text-sm font-bold leading-tight text-ink">{{ $userName }}</p>
            <p class="text-[13px] leading-tight text-muted">{{ $branchName }}</p>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 pb-4" aria-label="Menu admin">
            @foreach ($menuItems as $item)
                @php $aktif = Route::has($item['route']); @endphp
                @if ($aktif)
                    <a href="{{ route($item['route'], $demoParams) }}"
                       @if (request()->routeIs($item['route'])) aria-current="page" @endif
                       class="flex items-center gap-3 rounded-[5px] px-3 py-2.5 text-sm font-bold {{ request()->routeIs($item['route']) ? 'bg-primary text-white' : 'text-ink hover:bg-soft' }}">
                        <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$item['icon']] !!}</svg>
                        {{ $item['label'] }}
                    </a>
                @else
                    <span aria-disabled="true" title="Halaman ini belum tersedia"
                          class="flex cursor-not-allowed items-center gap-3 rounded-[5px] px-3 py-2.5 text-sm font-bold text-muted/60">
                        <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$item['icon']] !!}</svg>
                        {{ $item['label'] }}
                    </span>
                @endif
            @endforeach

            <div class="my-3 border-t border-line"></div>
            <p class="px-3 pb-1 text-[11px] font-bold tracking-wider text-muted">SEGERA HADIR</p>
            @foreach ($menuSoon as $item)
                <span aria-disabled="true" title="Halaman ini belum tersedia"
                      class="flex cursor-not-allowed items-center gap-3 rounded-[5px] px-3 py-2.5 text-sm font-bold text-muted/60">
                    <svg class="h-[18px] w-[18px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $icons[$item['icon']] !!}</svg>
                    {{ $item['label'] }}
                </span>
            @endforeach
        </nav>

        <div class="border-t border-line p-4">
            <a href="{{ url('/') }}" class="text-sm font-bold text-primary hover:underline">Lihat website</a>
        </div>
    </aside>

    {{-- Area utama --}}
    <div class="flex min-w-0 flex-1 flex-col">
        <header class="sticky top-0 z-20 flex items-center gap-3 border-b border-line bg-cream px-5 py-3.5 lg:px-8">
            <button type="button" id="sidebar-open" aria-expanded="false" aria-controls="admin-sidebar" aria-label="Buka menu"
                    class="rounded-[5px] border border-line bg-white p-2 text-ink hover:bg-soft lg:hidden">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>

            <h1 class="min-w-0 flex-1 truncate text-3xl sm:text-4xl">@yield('page_title', 'Dashboard')</h1>

            <div class="flex items-center gap-3">
                <div class="sr-only text-right sm:not-sr-only sm:block">
                    <p class="text-sm font-bold leading-tight text-ink">{{ $userName }}</p>
                    <p class="text-[13px] leading-tight text-muted">{{ $branchName }}</p>
                </div>
                <span class="grid h-10 w-10 place-items-center rounded-full bg-primary text-xs font-bold text-white" title="{{ $userName }}, {{ $branchName }}" aria-hidden="true">{{ $initials }}</span>
            </div>
        </header>

        <main class="flex-1 px-5 py-6 lg:px-8">
            {{-- Pesan sukses/error dari CRUD (dipertahankan dari layout lama 2.2) --}}
            @if (session('success'))
                <p class="mb-4 rounded border border-green-300 bg-green-100 px-4 py-2 text-green-800">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                <div class="mb-4 rounded border border-red-300 bg-red-100 px-4 py-2 text-red-800">
                    <p class="font-semibold">Terjadi kesalahan validasi:</p>
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

<script>
    (function () {
        var sidebar = document.getElementById('admin-sidebar');
        var backdrop = document.getElementById('admin-backdrop');
        var opener = document.getElementById('sidebar-open');
        var closer = document.getElementById('sidebar-close');
        var desktop = window.matchMedia('(min-width: 1024px)');
        var open = false;

        // Di HP, sidebar yang tertutup dikeluarkan dari urutan fokus keyboard (inert).
        // Di desktop sidebar selalu tampil, jadi tidak boleh inert.
        function render() {
            var drawerOpen = open && !desktop.matches;
            sidebar.classList.toggle('-translate-x-full', !open);
            backdrop.classList.toggle('hidden', !drawerOpen);
            sidebar.inert = !desktop.matches && !open;
            opener.setAttribute('aria-expanded', drawerOpen ? 'true' : 'false');
        }

        function setOpen(value) {
            var wasOpen = open;
            open = value;
            render();
            if (value) {
                closer.focus();
            } else if (wasOpen) {
                opener.focus(); // balikin fokus ke tombol pembuka
            }
        }

        opener.addEventListener('click', function () { setOpen(true); });
        closer.addEventListener('click', function () { setOpen(false); });
        backdrop.addEventListener('click', function () { setOpen(false); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && open) { setOpen(false); }
        });
        desktop.addEventListener('change', function () {
            if (desktop.matches) { open = false; }
            render();
        });

        render();
    })();
</script>
</body>
</html>