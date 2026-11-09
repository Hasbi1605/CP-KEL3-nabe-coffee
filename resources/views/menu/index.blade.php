@extends('layouts.app')

@section('title', 'Katalog Menu | Nabe Coffee')

@section('content')
    <section class="menu-hero">
        <img src="{{ asset('images/menu/1461023058943-07fcbe16d735.jpg') }}" alt="Ilustrasi menu Nabe Coffee" class="absolute inset-0 size-full object-cover">
        <div class="menu-hero-overlay"></div>
        <div class="menu-hero-content relative z-10">
            <p class="text-[11px] font-bold tracking-wider text-lime">NABE COFFEE · OUR MENU</p>
            <h1 class="mt-5 max-w-4xl text-[clamp(60px,6vw,86px)] text-white">GOOD MOOD, GREAT MENU.</h1>
            <p class="mt-5 max-w-xl text-sm leading-7 text-white">Your daily fuel, your little treat. Pilih yang pas buat harimu.</p>
        </div>
    </section>

    <section class="menu-section">
        <div class="flex flex-col gap-6 border-b border-line pb-8 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-[11px] font-bold tracking-wider text-primary">THE DAILY PICKS</p>
                <h2 class="mt-3 text-[clamp(36px,3.7vw,56px)]">WHAT'S YOUR CRAVING?</h2>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-muted">Pilih cabang untuk melihat pilihan menu. Katalog ini sekarang membaca data menu dari database (tugas 3.4).</p>
            </div>

            <form method="GET" action="{{ route('menu.index') }}" class="flex min-w-0 flex-col gap-2 text-[11px] font-bold tracking-wider text-ink lg:w-72">
                <label for="branch_id">Pilih cabang</label>
                <input type="hidden" name="category" value="{{ $filters['category'] ?? '' }}">
                <input type="hidden" name="max_price" value="{{ $filters['max_price'] ?? '' }}">
                <input type="hidden" name="search" value="{{ $filters['search'] ?? '' }}">
                <select id="branch_id" name="branch_id" onchange="this.form.submit()" class="rounded-[5px] border border-line bg-white px-3 py-3 text-sm font-normal text-ink" aria-label="Pilih cabang">
                    <option value="">Semua cabang</option>
                    @foreach ($branchModels as $branch)
                        <option value="{{ $branch->id }}" @selected(($filters['branch_id'] ?? null) == $branch->id)>{{ $branch->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>

        <form method="GET" action="{{ route('menu.index') }}" class="mt-8 grid gap-4 lg:grid-cols-[1fr_280px_auto]">
            <input type="hidden" name="branch_id" value="{{ $filters['branch_id'] ?? '' }}">
            <input type="hidden" name="category" value="{{ $filters['category'] ?? '' }}">
            <label class="flex flex-col gap-2 text-[11px] font-bold tracking-wider text-ink">
                Cari menu
                <input id="menu-search" name="search" value="{{ $filters['search'] ?? '' }}" type="search" placeholder="Cari menu favoritmu" class="rounded-[5px] border border-line bg-white px-3 py-3 text-sm font-normal text-ink placeholder:text-muted">
            </label>
            <label class="flex flex-col gap-2 text-[11px] font-bold tracking-wider text-ink">
                Rentang harga
                <select id="menu-price-filter" name="max_price" class="rounded-[5px] border border-line bg-white px-3 py-3 text-sm font-normal text-ink">
                    <option value="">Semua harga</option>
                    @foreach ([15000 => 'Sampai Rp15.000', 20000 => 'Sampai Rp20.000', 30000 => 'Sampai Rp30.000'] as $price => $label)
                        <option value="{{ $price }}" @selected(($filters['max_price'] ?? null) == $price)>{{ $label }}</option>
                    @endforeach
                </select>
            </label>
            <div class="flex items-end">
                <button type="submit" class="min-h-11 rounded-[5px] bg-primary px-5 text-[13px] font-bold text-white hover:brightness-95">Terapkan Filter</button>
            </div>
        </form>

        <div class="mt-7 flex gap-2 overflow-x-auto pb-2" role="tablist" aria-label="Kategori menu">
            <a href="{{ route('menu.index', array_filter(['branch_id' => $filters['branch_id'] ?? null, 'max_price' => $filters['max_price'] ?? null, 'search' => $filters['search'] ?? null])) }}" class="menu-category-tab {{ empty($filters['category']) ? 'is-active' : '' }} shrink-0 rounded-full border border-line px-4 py-2 text-[11px] font-bold tracking-wider" aria-pressed="{{ empty($filters['category']) ? 'true' : 'false' }}">Semua</a>
            @foreach ($categories as $value => $label)
                <a href="{{ route('menu.index', array_filter(['category' => $value, 'branch_id' => $filters['branch_id'] ?? null, 'max_price' => $filters['max_price'] ?? null, 'search' => $filters['search'] ?? null])) }}" class="menu-category-tab {{ ($filters['category'] ?? null) === $value ? 'is-active' : '' }} shrink-0 rounded-full border border-line px-4 py-2 text-[11px] font-bold tracking-wider" aria-pressed="{{ ($filters['category'] ?? null) === $value ? 'true' : 'false' }}">{{ $label }}</a>
            @endforeach
        </div>

        <p class="mt-4 text-[13px] leading-6 text-muted">Harga, foto, ketersediaan, label Terlaris, dan penanda promo dibaca dari data cabang terpilih. Ketersediaan menu dapat berbeda di tiap cabang.</p>

        <div id="menu-grid" class="mt-9 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($menus as $menu)
                <x-menu-card :menu="$menu" />
            @endforeach
        </div>

        <div id="menu-empty-state" class="hidden border border-line bg-white p-8 text-center">
            <h3 class="text-xl font-bold">Menu belum tersedia</h3>
            <p class="mx-auto mt-3 max-w-md text-sm leading-7 text-muted">Belum ada menu yang sesuai filter. Coba kategori atau rentang harga lain.</p>
        </div>
    </section>

    <dialog id="menu-detail-modal" class="w-[min(92vw,760px)] border-0 bg-transparent p-0 backdrop:bg-ink/60">
        <div class="relative grid overflow-hidden rounded-[5px] bg-white sm:grid-cols-2">
            <button type="button" class="absolute right-3 top-3 z-10 grid size-10 place-items-center rounded-full bg-white text-xl text-ink hover:bg-soft" data-modal-close aria-label="Tutup detail menu">×</button>
            <img id="menu-modal-image" src="" alt="" class="aspect-4/3 size-full object-cover sm:aspect-auto sm:h-full">
            <div class="p-7 sm:p-9">
                <div id="menu-modal-badges" class="flex flex-wrap gap-2"></div>
                <p id="menu-modal-category" class="mt-5 text-[10px] font-bold tracking-wider text-primary"></p>
                <h2 id="menu-modal-name" class="mt-2 text-[42px]"></h2>
                <p id="menu-modal-description" class="mt-5 text-sm leading-7 text-muted"></p>
                <strong id="menu-modal-price" class="mt-6 block text-lg text-ink"></strong>
                <button type="button" class="mt-7 inline-flex min-h-11 items-center justify-center rounded-[5px] bg-primary px-5 text-[13px] font-bold text-white hover:brightness-95">Tambah ke reservasi</button>
                <p class="mt-3 text-xs leading-5 text-muted">Tombol belum terhubung ke reservasi.</p>
            </div>
        </div>
    </dialog>
@endsection
