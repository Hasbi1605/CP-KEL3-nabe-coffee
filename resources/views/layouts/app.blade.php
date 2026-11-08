<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Nabe Coffee — Good Coffee. Good Company.' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/brand/favicon-64.png') }}">

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream text-ink font-sans antialiased min-h-screen flex flex-col selection:bg-lime selection:text-primary-dark">
    <!-- Topline Banner -->
    <div class="bg-primary text-white text-[10px] tracking-[1.4px] font-sans h-[30px] flex items-center justify-center gap-4 px-4 uppercase text-center">
        <span>GOOD COFFEE. GOOD COMPANY.</span>
        <span class="text-lime tracking-normal font-semibold lowercase first-letter:uppercase">See you at Nabe ↗</span>
    </div>

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-30 bg-cream/95 backdrop-blur-sm border-b border-line px-6 lg:px-12 h-[84px] lg:h-[99px] flex items-center justify-between transition-all">
        <!-- Navigation Left (Desktop) -->
        <nav class="hidden lg:flex items-center gap-8 flex-1">
            <a href="{{ url('/') }}" class="text-[11px] font-bold tracking-wider uppercase text-ink hover:text-primary transition-colors">
                HOME
            </a>
            <a href="{{ url('/#about') }}" class="text-[11px] font-bold tracking-wider uppercase text-ink hover:text-primary transition-colors">
                ABOUT US
            </a>
            <a href="{{ url('/#outlet') }}" class="text-[11px] font-bold tracking-wider uppercase text-ink hover:text-primary transition-colors">
                OUR OUTLET
            </a>
        </nav>

        <!-- Brand Logo Center -->
        <div class="flex-shrink-0 px-2 lg:px-6">
            <a href="{{ url('/') }}" aria-label="Nabe Coffee Beranda" class="inline-block transition-transform duration-200 hover:scale-105">
                <img src="{{ asset('images/brand/logo-nabe-480.png') }}" alt="Nabe Coffee" class="h-10 lg:h-12 w-auto object-contain" />
            </a>
        </div>

        <!-- Navigation Right (Desktop) -->
        <nav class="hidden lg:flex items-center justify-end gap-8 flex-1">
            <a href="{{ url('/#menu') }}" class="text-[11px] font-bold tracking-wider uppercase text-ink hover:text-primary transition-colors">
                OUR MENU
            </a>
            <a href="#contact" class="text-[11px] font-bold tracking-wider uppercase text-ink hover:text-primary transition-colors">
                CONTACT
            </a>
            <x-button variant="primary" href="{{ url('/#reservation') }}" class="text-[11px] py-3 px-5 uppercase whitespace-nowrap tracking-wider">
                Reservasi Sekarang <span>↗</span>
            </x-button>
        </nav>

        <!-- Mobile Menu Toggle Button -->
        <div class="flex items-center gap-3 lg:hidden">
            <x-button variant="primary" href="{{ url('/#reservation') }}" class="text-[10px] py-2 px-3 uppercase tracking-wider">
                Reservasi ↗
            </x-button>
            <button
                type="button"
                id="mobile-nav-toggle"
                aria-label="Buka menu navigasi"
                aria-expanded="false"
                class="w-10 h-10 flex items-center justify-center rounded-[5px] border border-line bg-white text-ink hover:bg-soft focus:outline-none focus:ring-2 focus:ring-primary"
            >
                <svg id="hamburger-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg id="close-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </header>

    <!-- Mobile Navigation Drawer -->
    <div id="mobile-nav-drawer" class="hidden lg:hidden border-b border-line bg-cream px-6 py-5 flex-col gap-4 shadow-lg animate-fadeIn">
        <a href="{{ url('/') }}" class="text-[12px] font-bold tracking-wider uppercase text-ink hover:text-primary py-2 border-b border-line/40 flex items-center justify-between">
            <span>HOME</span>
            <span class="text-muted text-[10px]">↗</span>
        </a>
        <a href="{{ url('/#about') }}" class="text-[12px] font-bold tracking-wider uppercase text-ink hover:text-primary py-2 border-b border-line/40 flex items-center justify-between">
            <span>ABOUT US</span>
            <span class="text-muted text-[10px]">↗</span>
        </a>
        <a href="{{ url('/#outlet') }}" class="text-[12px] font-bold tracking-wider uppercase text-ink hover:text-primary py-2 border-b border-line/40 flex items-center justify-between">
            <span>OUR OUTLET</span>
            <span class="text-muted text-[10px]">↗</span>
        </a>
        <a href="{{ url('/#menu') }}" class="text-[12px] font-bold tracking-wider uppercase text-ink hover:text-primary py-2 border-b border-line/40 flex items-center justify-between">
            <span>OUR MENU</span>
            <span class="text-muted text-[10px]">↗</span>
        </a>
        <a href="#contact" class="text-[12px] font-bold tracking-wider uppercase text-ink hover:text-primary py-2 border-b border-line/40 flex items-center justify-between">
            <span>CONTACT</span>
            <span class="text-muted text-[10px]">↗</span>
        </a>
        <a href="{{ route('login') }}" class="text-[12px] font-bold tracking-wider uppercase text-primary py-2 flex items-center justify-between">
            <span>LOGIN ADMIN</span>
            <span class="text-primary text-[10px]">↗</span>
        </a>
    </div>

    <!-- Main Content Slot -->
    <main class="flex-1 w-full">
        @isset($slot)
            {{ $slot }}
        @else
            @yield('content')
        @endisset
    </main>

    <!-- Footer -->
    <footer id="contact" class="bg-cream border-t border-line pt-14 pb-8 px-6 lg:px-[7.1%] mt-auto">
        <div class="max-w-[1600px] mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[1.2fr_1fr_0.7fr] gap-12 lg:gap-20">
            <!-- Col 1: Brand & Social -->
            <div>
                <a href="{{ url('/') }}" class="inline-block mb-4">
                    <img src="{{ asset('images/brand/logo-nabe-480.png') }}" alt="Nabe Coffee" class="h-10 w-auto object-contain" />
                </a>
                <p class="text-[12px] text-muted leading-relaxed max-w-[320px] mb-5">
                    Good coffee. Good company.<br>
                    Empat sudut Jogja, satu tempat buat kita.
                </p>
                <a
                    href="https://www.instagram.com/nabecoffee.id/"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 text-[12px] font-bold text-primary hover:underline"
                >
                    <span>@nabecoffee.id</span>
                    <span>↗</span>
                </a>
            </div>

            <!-- Col 2: Locations & Opening Hours -->
            <div>
                <span class="eyebrow text-muted mb-3">COME SAY HELLO</span>
                <p class="text-[13px] font-medium text-ink mb-1">Cabang Utama (Kusumanegara)</p>
                <p class="text-[12px] text-muted leading-relaxed mb-3">
                    Jl. Kusumanegara No.11, Semaki, Kec. Umbulharjo, Kota Yogyakarta, DIY 55166
                </p>
                <p class="text-[11px] text-muted mb-4 font-mono">
                    Buka setiap hari · 07.00 – 00.00 WIB
                </p>
                <a
                    href="https://wa.me/6281234567890"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-1.5 text-[12px] font-bold text-primary hover:underline"
                >
                    <span>Hubungi Admin Cabang</span>
                    <span>↗</span>
                </a>
            </div>

            <!-- Col 3: Explore Navigation -->
            <div>
                <span class="eyebrow text-muted mb-3">EXPLORE</span>
                <ul class="space-y-2.5 text-[12px]">
                    <li>
                        <a href="{{ url('/#outlet') }}" class="text-ink hover:text-primary transition-colors">Our Outlet</a>
                    </li>
                    <li>
                        <a href="{{ url('/#menu') }}" class="text-ink hover:text-primary transition-colors">Our Menu</a>
                    </li>
                    <li>
                        <a href="{{ url('/#reservation') }}" class="text-ink hover:text-primary transition-colors">Reservasi Online</a>
                    </li>
                    <li>
                        <a href="{{ url('/status') }}" class="text-ink hover:text-primary transition-colors">Cek Status Reservasi</a>
                    </li>
                    <li class="pt-2 border-t border-line/60">
                        <a href="{{ route('login') }}" class="text-primary font-bold hover:underline flex items-center gap-1">
                            <span>Panel Admin</span>
                            <span class="text-[10px]">↗</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="max-w-[1600px] mx-auto border-t border-line mt-12 pt-6 flex flex-col sm:flex-row justify-between items-center gap-3 text-[11px] text-muted">
            <span>&copy; {{ date('Y') }} Nabe Coffee · Semua Hak Dilindungi</span>
            <span>Yogyakarta, Indonesia</span>
            <span class="font-bold tracking-wider text-ink">MADE FOR GOOD DAYS ↗</span>
        </div>
    </footer>

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('mobile-nav-toggle');
            const drawer = document.getElementById('mobile-nav-drawer');
            const hamburgerIcon = document.getElementById('hamburger-icon');
            const closeIcon = document.getElementById('close-icon');

            if (toggleBtn && drawer) {
                toggleBtn.addEventListener('click', function () {
                    const isExpanded = toggleBtn.getAttribute('aria-expanded') === 'true';
                    toggleBtn.setAttribute('aria-expanded', !isExpanded);
                    drawer.classList.toggle('hidden');
                    drawer.classList.toggle('flex');
                    hamburgerIcon.classList.toggle('hidden');
                    closeIcon.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>

