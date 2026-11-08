@extends('layouts.app')

@section('title', 'Nabe Coffee — Good Coffee. Good Company. (Yogyakarta)')

@section('content')
    <!-- 1. Hero Section -->
    <section class="relative bg-primary-dark text-white min-h-[580px] lg:min-h-[660px] flex items-center overflow-hidden">
        <!-- Asset Photo Background -->
        <div class="absolute inset-0 asset-photo">
            <img
                src="{{ asset('images/placeholders/hero-cafe.jpg') }}"
                alt="Suasana Kafe Nabe Coffee"
                class="w-full h-full object-cover object-center brightness-75 scale-105 transition-transform duration-1000 ease-out"
            />
            <small>FOTO SUASANA · NABE COFFEE</small>
        </div>

        <!-- Gradient Shade Overlay -->
        <div class="absolute inset-0 bg-gradient-to-r from-primary-dark/95 via-primary-dark/75 to-primary-dark/25"></div>

        <!-- Hero Content Container -->
        <div class="relative max-w-[1600px] mx-auto px-6 lg:px-[7.1%] py-20 lg:py-28 w-full">
            <div class="max-w-[820px]">
                <span class="eyebrow text-lime mb-4 tracking-[2px]">
                    YOUR EVERYDAY HAPPY PLACE &middot; YOGYAKARTA
                </span>

                <h1 class="text-white text-5xl sm:text-7xl lg:text-[96px] font-display font-bold uppercase leading-[0.93] tracking-tight mb-6">
                    LIGHT UP YOUR DAY<br />
                    <span class="font-sans font-medium text-3xl sm:text-5xl lg:text-[49px] tracking-tight block mt-3 text-white/95">
                        with Nabe.
                    </span>
                </h1>

                <p class="text-white/85 text-[15px] sm:text-[16px] leading-relaxed max-w-[560px] mb-8 font-sans">
                    Dari kopi pertama di pagi hari sampai obrolan terakhir di malam hari. Selalu ada tempat nyaman buat kamu di setiap sudut Nabe Coffee.
                </p>

                <!-- Hero Actions -->
                <div class="flex flex-wrap items-center gap-4">
                    <x-button variant="lime" href="#locations" class="text-[13px] py-4 px-8 uppercase tracking-wider">
                        Reservasi Sekarang <span>↗</span>
                    </x-button>
                    <x-button variant="glass" href="#about" class="text-[13px] py-4 px-8 uppercase tracking-wider">
                        Cerita Nabe <span>↓</span>
                    </x-button>
                </div>
            </div>

            <!-- Hero Circular Sticker Badge (Desktop only) -->
            <div class="hidden lg:flex hero-sticker pointer-events-none select-none">
                <span class="text-[10px] font-bold tracking-wider uppercase">COFFEE &amp;</span>
                <strong class="font-display text-4xl leading-none my-1 tracking-tight">
                    GOOD<br />TIMES!
                </strong>
                <span class="text-[9px] font-semibold tracking-wide uppercase">EVERY DAY, EVERY CUP</span>
            </div>

            <!-- Hero Bottom Bar -->
            <div class="hidden sm:flex absolute bottom-6 left-6 lg:left-[7.1%] right-6 lg:right-[7.1%] justify-between items-center text-[11px] tracking-wider text-white/70 font-mono">
                <div class="flex items-center gap-3">
                    <span class="font-bold text-white">01</span>
                    <span class="w-12 h-px bg-white/40"></span>
                    <span>04 CABANG AKTIF</span>
                </div>
                <div>
                    <span>SCROLL TO EXPLORE &darr;</span>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Info Strip -->
    <section class="border-b border-line bg-cream py-6 px-6 lg:px-[7.1%]">
        <div class="max-w-[1600px] mx-auto flex flex-col md:flex-row items-start md:items-center justify-between gap-4 md:gap-8 text-[12px] text-muted">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-primary flex-shrink-0"></span>
                <span><strong class="text-ink font-bold">&empty; 4 Cabang di Jogja:</strong> Kusumanegara, Demangan, Soragan, Batikan</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-lime flex-shrink-0"></span>
                <span><strong class="text-ink font-bold">&bull; Jam Operasional:</strong> 07.00 &ndash; 00.00 WIB* (Batikan 09.00 &ndash; 22.00)</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-gold flex-shrink-0"></span>
                <span><strong class="text-ink font-bold">&starf; Kisaran Harga:</strong> Rp25.000 &ndash; Rp50.000 / orang</span>
            </div>
        </div>
    </section>

    <!-- 3. Cerita Brand & Visi Misi Section -->
    <section id="about" class="py-20 lg:py-28 px-6 lg:px-[7.1%] max-w-[1600px] mx-auto">
        <!-- Brand Story Split -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center mb-20">
            <!-- Left: Visual Story Photo -->
            <div class="asset-photo rounded-[5px] overflow-hidden aspect-[4/3] shadow-md">
                <img
                    src="{{ asset('images/placeholders/about-story.jpg') }}"
                    alt="Cerita Ruang Nabe Coffee"
                    class="w-full h-full object-cover"
                />
                <small>FOTO SUASANA NABE &middot; JOGJA</small>
            </div>

            <!-- Right: Brand Narrative -->
            <div>
                <span class="eyebrow text-primary mb-3">MORE THAN YOUR DAILY CUP</span>
                <h2 class="text-ink text-4xl sm:text-5xl lg:text-[56px] font-display font-bold uppercase leading-[0.95] mb-6">
                    BUKAN CUMA KOPI.<br />
                    INI TEMPAT KITA.
                </h2>
                <div class="space-y-4 text-muted text-[14px] leading-relaxed">
                    <p>
                        Nabe Coffee hadir sebagai ruang komunal di Yogyakarta yang mempertemukan secangkir kopi berkualitas dengan kenyamanan berinteraksi. Entah untuk bekerja fokus di sudut tenang, berdiskusi dengan rekan kerja, atau sekadar rehat sejenak dari hiruk-pikuk kota.
                    </p>
                    <p>
                        Dengan empat cabang yang tersebar di titik-titik strategis Jogja, kami berkomitmen menghadirkan atmosfer hangat dan standar pelayanan yang konsisten di setiap kunjungan Anda.
                    </p>
                    <div class="pt-2">
                        <span class="inline-block px-3 py-1.5 rounded-[4px] bg-soft border border-line text-primary font-bold text-[11px] tracking-wider uppercase">
                            Grup Afiliasi: Majja Decospace &middot; Jogja Padel &middot; MEandPartners
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Visi & Misi Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
            <!-- Visi Card -->
            <div class="bg-white border border-line rounded-[5px] p-8 lg:p-10 flex flex-col justify-between hover:border-primary/40 transition-colors shadow-sm">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="eyebrow text-primary">VISI KAMI</span>
                        <x-badge variant="sage">Tujuan Utama</x-badge>
                    </div>
                    <h3 class="font-display font-bold text-3xl sm:text-4xl text-ink uppercase tracking-tight mb-3">
                        A place to belong.
                    </h3>
                    <p class="text-muted text-[13px] sm:text-[14px] leading-relaxed">
                        Menjadi ruang ketiga yang inklusif dan menginspirasi di Yogyakarta, mempertemukan cita rasa kopi terbaik dengan kehangatan komunitas kreatif lokal.
                    </p>
                </div>
            </div>

            <!-- Misi Card -->
            <div class="bg-white border border-line rounded-[5px] p-8 lg:p-10 flex flex-col justify-between hover:border-primary/40 transition-colors shadow-sm">
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="eyebrow text-primary">MISI KAMI</span>
                        <x-badge variant="gold">Komitmen Harian</x-badge>
                    </div>
                    <h3 class="font-display font-bold text-3xl sm:text-4xl text-ink uppercase tracking-tight mb-3">
                        Make good days happen.
                    </h3>
                    <p class="text-muted text-[13px] sm:text-[14px] leading-relaxed">
                        Menemani setiap harimu melalui konsistensi menu kopi pilihan, suasana ruang yang mendukung produktivitas, serta kemudahan sistem reservasi multi-cabang yang tertib kuota.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. Galeri Suasana Kafe Section -->
    <section id="gallery" class="bg-soft/40 border-y border-line py-20 lg:py-28 px-6 lg:px-[7.1%]">
        <div class="max-w-[1600px] mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12">
                <div>
                    <span class="eyebrow text-primary mb-2">OUR LITTLE CORNERS</span>
                    <h2 class="text-ink text-4xl sm:text-5xl lg:text-[52px] font-display font-bold uppercase leading-[0.95]">
                        THE EVERYDAY, BUT BETTER.
                    </h2>
                </div>
                <p class="text-muted text-[13px] max-w-[420px] leading-relaxed">
                    Setiap sudut Nabe dirancang dengan detail: pencahayaan nyaman, stopkontak memadai, dan ambience yang pas untuk segala aktivitasmu.
                </p>
            </div>

            <!-- Gallery Grid (3 Kolom Estetik) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Gallery Item 1 -->
                <div class="asset-photo rounded-[5px] overflow-hidden aspect-[4/3] group shadow-sm">
                    <img
                        src="{{ asset('images/placeholders/hero-cafe.jpg') }}"
                        alt="Area Indoor Work & Meet"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                    <small>AREA INDOOR &middot; WORK &amp; MEET</small>
                </div>

                <!-- Gallery Item 2 -->
                <div class="asset-photo rounded-[5px] overflow-hidden aspect-[4/3] group shadow-sm">
                    <img
                        src="{{ asset('images/placeholders/galeri-kopi.jpg') }}"
                        alt="Barista & Sajian Kopi Spesialti"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                    <small>BARISTA &middot; DAILY ESPRESSO</small>
                </div>

                <!-- Gallery Item 3 -->
                <div class="asset-photo rounded-[5px] overflow-hidden aspect-[4/3] group shadow-sm">
                    <img
                        src="{{ asset('images/placeholders/galeri-suasana.jpg') }}"
                        alt="Sudut Santai & Komunitas"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                    />
                    <small>SUASANA SANTAI &middot; JEDAMU</small>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. Section Promo (Data Dummy CMS - Siap Integrasi Tugas 3.4) -->
    <section id="promo" class="py-20 lg:py-28 px-6 lg:px-[7.1%] max-w-[1600px] mx-auto">
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12">
            <div>
                <span class="eyebrow text-primary mb-2">PROMO &amp; SPESIAL BULAN INI</span>
                <h2 class="text-ink text-4xl sm:text-5xl lg:text-[52px] font-display font-bold uppercase leading-[0.95]">
                    PENAWARAN HARI BAIKMU.
                </h2>
            </div>
            <span class="inline-flex items-center gap-2 text-muted text-[12px] font-bold">
                <span class="w-2 h-2 rounded-full bg-gold"></span>
                Berlaku di Semua Cabang Nabe Coffee
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Promo Card 1 -->
            <div class="bg-white border border-line rounded-[5px] overflow-hidden flex flex-col sm:flex-row hover:border-primary/50 transition-colors shadow-sm">
                <div class="sm:w-2/5 aspect-[4/3] sm:aspect-auto asset-photo">
                    <img
                        src="{{ asset('images/placeholders/promo-spesial.jpg') }}"
                        alt="Morning Fuel Combo"
                        class="w-full h-full object-cover"
                    />
                    <small>PROMO PAGI</small>
                </div>
                <div class="p-6 sm:p-8 sm:w-3/5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <x-badge variant="gold">Diskon 20%</x-badge>
                            <span class="text-muted text-[11px] font-mono">07.00 &ndash; 10.00 WIB</span>
                        </div>
                        <h3 class="font-display font-bold text-2xl text-ink uppercase tracking-tight mb-2">
                            Morning Fuel Combo
                        </h3>
                        <p class="text-muted text-[12px] leading-relaxed mb-4">
                            Mulai harimu dengan fokus. Pesan kopi favoritmu bersama pilihan croissant pastry hangat dengan potongan harga 20%.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-line/60 flex items-center justify-between text-[11px] text-muted">
                        <span>Seluruh Cabang</span>
                        <a href="#locations" class="text-primary font-bold hover:underline">Reservasi Slot &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Promo Card 2 -->
            <div class="bg-white border border-line rounded-[5px] overflow-hidden flex flex-col sm:flex-row hover:border-primary/50 transition-colors shadow-sm">
                <div class="sm:w-2/5 aspect-[4/3] sm:aspect-auto asset-photo">
                    <img
                        src="{{ asset('images/placeholders/about-story.jpg') }}"
                        alt="Group Table Gathering"
                        class="w-full h-full object-cover"
                    />
                    <small>PROMO ROMBONGAN</small>
                </div>
                <div class="p-6 sm:p-8 sm:w-3/5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <x-badge variant="sage">Free Pitcher</x-badge>
                            <span class="text-muted text-[11px] font-mono">Min. 8 Orang</span>
                        </div>
                        <h3 class="font-display font-bold text-2xl text-ink uppercase tracking-tight mb-2">
                            Group Table Gathering
                        </h3>
                        <p class="text-muted text-[12px] leading-relaxed mb-4">
                            Pertemuan komunitas, rapat tim, atau kumpul keluarga jadi lebih hangat. Dapatkan free 1 pitcher signature iced tea untuk reservasi rombongan.
                        </p>
                    </div>
                    <div class="pt-4 border-t border-line/60 flex items-center justify-between text-[11px] text-muted">
                        <span>Prioritas Rombongan</span>
                        <a href="#locations" class="text-primary font-bold hover:underline">Reservasi Sekarang &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. Section Informasi 4 Cabang (Siap Terintegrasi Tugas 3.2) -->
    <section id="locations" class="border-t border-line bg-cream py-20 lg:py-28 px-6 lg:px-[7.1%]">
        <div class="max-w-[1600px] mx-auto">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12">
                <div>
                    <span class="eyebrow text-primary mb-2">OUR LOCATIONS</span>
                    <h2 class="text-ink text-4xl sm:text-5xl lg:text-[52px] font-display font-bold uppercase leading-[0.95]">
                        FIND YOUR NABE.
                    </h2>
                </div>
                <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-[4px] bg-white border border-line text-[11px] font-bold text-ink uppercase tracking-wider">
                    4 Sudut Jogja &middot; Satu Cita Rasa
                </span>
            </div>

            <!-- Branch Grid (4 Cabang) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Cabang 1: Kusumanegara -->
                <article class="bg-white border border-line rounded-[5px] overflow-hidden flex flex-col justify-between hover:border-primary/50 transition-colors shadow-sm">
                    <div>
                        <div class="aspect-[4/3] asset-photo">
                            <img
                                src="{{ asset('images/placeholders/cabang-kusumanegara.jpg') }}"
                                alt="Nabe Coffee Kusumanegara"
                                class="w-full h-full object-cover"
                            />
                            <small>CABANG KUSUMANEGARA</small>
                        </div>
                        <div class="p-5">
                            <h3 class="font-display font-bold text-2xl text-ink uppercase tracking-tight mb-2">
                                Kusumanegara
                            </h3>
                            <p class="text-muted text-[12px] leading-relaxed mb-3">
                                Jl. Kusumanegara No.11, Semaki, Umbulharjo, Kota Yogyakarta 55166
                            </p>
                            <span class="inline-block text-[11px] font-mono text-primary font-bold bg-soft px-2 py-1 rounded-[3px]">
                                07.00 &ndash; 00.00 WIB
                            </span>
                        </div>
                    </div>
                    <div class="p-5 pt-0 flex items-center justify-between gap-2 border-t border-line/40 mt-4">
                        <a
                            href="https://maps.google.com/?q=Nabe+Coffee+Kusumanegara+Yogyakarta"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-[11px] font-bold text-primary hover:underline"
                        >
                            Peta Lokasi ↗
                        </a>
                        <a
                            href="https://wa.me/6281234567890"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-[11px] font-bold text-muted hover:text-ink"
                        >
                            WhatsApp ↗
                        </a>
                    </div>
                </article>

                <!-- Cabang 2: Demangan -->
                <article class="bg-white border border-line rounded-[5px] overflow-hidden flex flex-col justify-between hover:border-primary/50 transition-colors shadow-sm">
                    <div>
                        <div class="aspect-[4/3] asset-photo">
                            <img
                                src="{{ asset('images/placeholders/cabang-demangan.jpg') }}"
                                alt="Nabe Coffee Demangan"
                                class="w-full h-full object-cover"
                            />
                            <small>CABANG DEMANGAN</small>
                        </div>
                        <div class="p-5">
                            <h3 class="font-display font-bold text-2xl text-ink uppercase tracking-tight mb-2">
                                Demangan
                            </h3>
                            <p class="text-muted text-[12px] leading-relaxed mb-3">
                                Jl. Demangan Baru, Mrican, Caturtunggal, Depok, Sleman 55281
                            </p>
                            <span class="inline-block text-[11px] font-mono text-primary font-bold bg-soft px-2 py-1 rounded-[3px]">
                                07.00 &ndash; 00.00 WIB
                            </span>
                        </div>
                    </div>
                    <div class="p-5 pt-0 flex items-center justify-between gap-2 border-t border-line/40 mt-4">
                        <a
                            href="https://maps.google.com/?q=Nabe+Coffee+Demangan+Yogyakarta"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-[11px] font-bold text-primary hover:underline"
                        >
                            Peta Lokasi ↗
                        </a>
                        <a
                            href="https://wa.me/6281234567890"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-[11px] font-bold text-muted hover:text-ink"
                        >
                            WhatsApp ↗
                        </a>
                    </div>
                </article>

                <!-- Cabang 3: Soragan -->
                <article class="bg-white border border-line rounded-[5px] overflow-hidden flex flex-col justify-between hover:border-primary/50 transition-colors shadow-sm">
                    <div>
                        <div class="aspect-[4/3] asset-photo">
                            <img
                                src="{{ asset('images/placeholders/cabang-soragan.jpg') }}"
                                alt="Nabe Coffee Soragan"
                                class="w-full h-full object-cover"
                            />
                            <small>CABANG SORAGAN</small>
                        </div>
                        <div class="p-5">
                            <h3 class="font-display font-bold text-2xl text-ink uppercase tracking-tight mb-2">
                                Soragan
                            </h3>
                            <p class="text-muted text-[12px] leading-relaxed mb-3">
                                Jl. Soragan No.29, Ngestiharjo, Kasihan, Bantul 55184
                            </p>
                            <span class="inline-block text-[11px] font-mono text-primary font-bold bg-soft px-2 py-1 rounded-[3px]">
                                07.00 &ndash; 00.00 WIB
                            </span>
                        </div>
                    </div>
                    <div class="p-5 pt-0 flex items-center justify-between gap-2 border-t border-line/40 mt-4">
                        <a
                            href="https://maps.google.com/?q=Nabe+Coffee+Soragan+Bantul"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-[11px] font-bold text-primary hover:underline"
                        >
                            Peta Lokasi ↗
                        </a>
                        <a
                            href="https://wa.me/6281234567890"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-[11px] font-bold text-muted hover:text-ink"
                        >
                            WhatsApp ↗
                        </a>
                    </div>
                </article>

                <!-- Cabang 4: Batikan (THE PADEL) -->
                <article class="bg-white border border-line rounded-[5px] overflow-hidden flex flex-col justify-between hover:border-primary/50 transition-colors shadow-sm">
                    <div>
                        <div class="aspect-[4/3] asset-photo">
                            <img
                                src="{{ asset('images/placeholders/cabang-batikan.jpg') }}"
                                alt="Nabe Coffee Batikan (THE PADEL)"
                                class="w-full h-full object-cover"
                            />
                            <small>CABANG BATIKAN · THE PADEL</small>
                        </div>
                        <div class="p-5">
                            <h3 class="font-display font-bold text-2xl text-ink uppercase tracking-tight mb-2">
                                Batikan (THE PADEL)
                            </h3>
                            <p class="text-muted text-[12px] leading-relaxed mb-3">
                                Jl. Batikan No.9, Umbulharjo, Kota Yogyakarta 55161
                            </p>
                            <span class="inline-block text-[11px] font-mono text-primary font-bold bg-soft px-2 py-1 rounded-[3px]">
                                09.00 &ndash; 22.00 WIB
                            </span>
                        </div>
                    </div>
                    <div class="p-5 pt-0 flex items-center justify-between gap-2 border-t border-line/40 mt-4">
                        <a
                            href="https://maps.google.com/?q=Nabe+Coffee+Batikan+Yogyakarta"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-[11px] font-bold text-primary hover:underline"
                        >
                            Peta Lokasi ↗
                        </a>
                        <a
                            href="https://wa.me/6281234567890"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-[11px] font-bold text-muted hover:text-ink"
                        >
                            WhatsApp ↗
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>
@endsection
