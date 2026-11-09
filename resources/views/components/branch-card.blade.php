@props([
    'branch' => null,
    'branches' => null,
    'index' => null,
])

@php
    // Default data 4 cabang Nabe Coffee (diambil dari NabeUI.tsx & PRD.md)
    // admin_whatsapp dibiarkan null karena nomor resmi belum diberikan di PRD (D6)
    // Disiapkan agar mudah diganti jika mengambil langsung dari tabel `branches` (Issue #2).
    $defaultBranches = [
        [
            'id' => 1,
            'name' => 'Nabe Coffee Kusumanegara',
            'short_name' => 'Kusumanegara',
            'city' => 'Yogyakarta',
            'address' => 'Jl. Kusumanegara No.11, Semaki, Kec. Umbulharjo, Kota Yogyakarta, DIY 55166',
            'hours' => '07.00–00.00',
            'opens_at' => '07:00:00',
            'closes_at' => '00:00:00',
            'admin_whatsapp' => null,
            'image' => 'images/branches/kusumanegara.jpg',
            'image_placeholder' => '1442512595331-e89e73853f31',
            'is_main' => true,
            'facilities' => ['WiFi · konfirmasi', 'Colokan · konfirmasi', 'Meeting room · konfirmasi', 'Parkir & musholla · konfirmasi'],
            'maps_query' => 'Nabe Coffee Kusumanegara, Jl. Kusumanegara No.11, Semaki, Kec. Umbulharjo, Kota Yogyakarta, DIY 55166',
        ],
        [
            'id' => 2,
            'name' => 'Nabe Coffee Demangan',
            'short_name' => 'Demangan',
            'city' => 'Sleman',
            'address' => 'Jl. Demangan Baru, Mrican, Caturtunggal, Kec. Depok, Kab. Sleman, DIY 55281',
            'hours' => '07.00–00.00',
            'opens_at' => '07:00:00',
            'closes_at' => '00:00:00',
            'admin_whatsapp' => null,
            'image' => 'images/branches/demangan.jpg',
            'image_placeholder' => '1554118811-1e0d58224f24',
            'is_main' => false,
            'facilities' => ['WiFi · konfirmasi', 'Colokan · konfirmasi', 'Meeting room · konfirmasi', 'Parkir & musholla · konfirmasi'],
            'maps_query' => 'Nabe Coffee Demangan, Jl. Demangan Baru, Mrican, Caturtunggal, Kec. Depok, Kab. Sleman, DIY 55281',
        ],
        [
            'id' => 3,
            'name' => 'Nabe Coffee Soragan',
            'short_name' => 'Soragan',
            'city' => 'Bantul',
            'address' => 'Jl. Soragan No.29, Soragan, Ngestiharjo, Kec. Kasihan, Kab. Bantul, DIY 55184',
            'hours' => '07.00–00.00',
            'opens_at' => '07:00:00',
            'closes_at' => '00:00:00',
            'admin_whatsapp' => null,
            'image' => 'images/branches/soragan.jpg',
            'image_placeholder' => '1501339847302-ac426a4a7cbb',
            'is_main' => false,
            'facilities' => ['WiFi · konfirmasi', 'Colokan · konfirmasi', 'Meeting room · konfirmasi', 'Parkir & musholla · konfirmasi'],
            'maps_query' => 'Nabe Coffee Soragan, Jl. Soragan No.29, Soragan, Ngestiharjo, Kec. Kasihan, Kab. Bantul, DIY 55184',
        ],
        [
            'id' => 4,
            'name' => 'Nabe Coffee Batikan (THE PADEL)',
            'short_name' => 'Batikan',
            'city' => 'Yogyakarta',
            'address' => 'Jl. Batikan No.9, THE PADEL, Kec. Umbulharjo, Kota Yogyakarta, DIY 55161',
            'hours' => '09.00–22.00',
            'opens_at' => '09:00:00',
            'closes_at' => '22:00:00',
            'admin_whatsapp' => null,
            'image' => 'images/branches/batikan.jpg',
            'image_placeholder' => '1521017432531-fbd92d768814',
            'is_main' => false,
            'facilities' => ['WiFi · konfirmasi', 'Colokan · konfirmasi', 'Meeting room · konfirmasi', 'Parkir & musholla · konfirmasi', 'Area padel · THE PADEL'],
            'maps_query' => 'Nabe Coffee Batikan, Jl. Batikan No.9, Umbulharjo, Kota Yogyakarta, DIY 55161',
        ],
    ];

    $isSingle = $branch !== null;
    $items = $isSingle ? [$branch] : ($branches ?? $defaultBranches);
@endphp

@if(!$isSingle)
    <div {{ $attributes->merge(['class' => 'outlet-detail-grid flex flex-col gap-8 w-full']) }}>
@endif

@foreach($items as $idx => $item)
    @php
        // Normalisasi data cabang dari Array atau Eloquent Model
        $rawName = is_array($item) ? ($item['name'] ?? '') : ($item->name ?? '');
        $name = str_starts_with($rawName, 'Nabe Coffee') ? $rawName : 'Nabe Coffee ' . $rawName;
        $cleanName = trim(preg_replace('/^Nabe Coffee\s*/i', '', $name));

        $address = is_array($item) ? ($item['address'] ?? '') : ($item->address ?? '');

        // Jam operasional
        $hours = is_array($item) ? ($item['hours'] ?? null) : ($item->hours ?? null);
        if (!$hours) {
            $opensAt = is_array($item) ? ($item['opens_at'] ?? null) : ($item->opens_at ?? null);
            $closesAt = is_array($item) ? ($item['closes_at'] ?? null) : ($item->closes_at ?? null);
            if ($opensAt && $closesAt) {
                $openFormatted = str_replace(':', '.', substr((string)$opensAt, 0, 5));
                $closeFormatted = str_replace(':', '.', substr((string)$closesAt, 0, 5));
                $hours = "{$openFormatted}–{$closeFormatted}";
            } else {
                $hours = '07.00–00.00';
            }
        }

        // WhatsApp admin & link wa.me (HANYA dibuat jika kontak valid dikonfigurasi)
        $itemIndex = $index ?? ($idx + 1);
        $paddedIndex = str_pad((string)$itemIndex, 2, '0', STR_PAD_LEFT);

        $whatsapp = is_array($item)
            ? ($item['admin_whatsapp'] ?? $item['whatsapp'] ?? $item['phone'] ?? null)
            : ($item->admin_whatsapp ?? $item->whatsapp ?? $item->phone ?? null);

        $waUrl = null;
        if (!empty($whatsapp)) {
            $cleanWa = preg_replace('/[^0-9]/', '', (string)$whatsapp);
            if ($cleanWa !== '') {
                if (str_starts_with($cleanWa, '0')) {
                    $cleanWa = '62' . substr($cleanWa, 1);
                } elseif (!str_starts_with($cleanWa, '62')) {
                    $cleanWa = '62' . $cleanWa;
                }
                $waText = rawurlencode("Halo Nabe Coffee {$cleanName}, saya ingin menanyakan informasi tentang reservasi / outlet.");
                $waUrl = "https://wa.me/{$cleanWa}?text={$waText}";
            }
        }

        // Google Maps Query & Embed URL (tanpa API key)
        $mapsUrl = is_array($item) ? ($item['maps_url'] ?? null) : ($item->maps_url ?? null);
        $mapsQuery = is_array($item) ? ($item['maps_query'] ?? null) : ($item->maps_query ?? null);
        if (!$mapsQuery) {
            $mapsQuery = "{$name}, {$address}";
        }
        $encodedQuery = urlencode($mapsQuery);

        $mapsEmbedUrl = is_array($item) ? ($item['maps_embed_url'] ?? null) : ($item->maps_embed_url ?? null);
        if (!$mapsEmbedUrl) {
            $mapsEmbedUrl = "https://maps.google.com/maps?q={$encodedQuery}&t=&z=15&ie=UTF8&iwloc=&output=embed";
        }

        $directMapsUrl = $mapsUrl ?: "https://www.google.com/maps/search/?api=1&query={$encodedQuery}";

        // Gambar cabang
        $image = is_array($item) ? ($item['image'] ?? null) : ($item->image ?? null);
        $imagePlaceholder = is_array($item) ? ($item['image_placeholder'] ?? null) : ($item->image_placeholder ?? null);

        if ($image && file_exists(public_path($image))) {
            $imageUrl = asset($image);
        } elseif ($image && (str_starts_with($image, 'http://') || str_starts_with($image, 'https://'))) {
            $imageUrl = $image;
        } elseif ($imagePlaceholder) {
            $imageUrl = "https://images.unsplash.com/photo-{$imagePlaceholder}?auto=format&fit=crop&w=1200&q=85";
        } else {
            $imageUrl = asset('images/branches/kusumanegara.jpg');
        }

        // Label Cabang Utama murni dari data eksplisit (tidak mengikuti posisi kartu)
        $isMain = is_array($item) ? (bool)($item['is_main'] ?? false) : (bool)($item->is_main ?? false);

        // Fasilitas (pertahankan label "konfirmasi" sesuai prototipe dan PRD jika belum diverifikasi)
        $facilities = is_array($item) ? ($item['facilities'] ?? null) : ($item->facilities ?? null);
        $hasUnconfirmedFacilities = false;
        if (!is_array($facilities)) {
            $facilities = ['WiFi · konfirmasi', 'Colokan · konfirmasi', 'Meeting room · konfirmasi', 'Parkir & musholla · konfirmasi'];
            if (str_contains(strtolower($name), 'batikan')) {
                $facilities[] = 'Area padel · THE PADEL';
            }
            $hasUnconfirmedFacilities = true;
        } else {
            foreach ($facilities as $fac) {
                if (str_contains(strtolower((string)$fac), 'konfirmasi')) {
                    $hasUnconfirmedFacilities = true;
                    break;
                }
            }
        }

        $eyebrowCity = is_array($item) ? ($item['city'] ?? 'YOGYAKARTA') : ($item->city ?? 'YOGYAKARTA');
    @endphp

    @if($isSingle)
        <article {{ $attributes->class(['outlet-detail bg-white border border-line rounded-[5px] overflow-hidden shadow-xs grid grid-cols-1 lg:grid-cols-2']) }}>
    @else
        <article class="outlet-detail bg-white border border-line rounded-[5px] overflow-hidden shadow-xs grid grid-cols-1 lg:grid-cols-2">
    @endif
        {{-- Kolom Kiri: Foto Cabang --}}
        <div class="relative w-full h-64 sm:h-80 lg:h-full min-h-[260px] lg:min-h-[460px] bg-soft overflow-hidden">
            <img
                src="{{ $imageUrl }}"
                alt="Foto Cabang {{ $cleanName }}"
                class="w-full h-full object-cover"
                loading="lazy"
            />
            <div class="absolute bottom-4 left-4 bg-ink/75 backdrop-blur-xs text-white text-[10px] font-medium tracking-wide uppercase px-2.5 py-1 rounded-[4px]">
                Foto Cabang {{ $cleanName }}
            </div>
            @if($isMain)
                <div class="absolute top-4 left-4 bg-gold text-gold-ink text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-[4px] shadow-xs">
                    Cabang Utama
                </div>
            @endif
        </div>

        {{-- Kolom Kanan: Detail Informasi, Embed Google Maps & Tombol Aksi --}}
        <div class="p-6 sm:p-8 lg:p-10 flex flex-col justify-between">
            <div>
                {{-- Eyebrow & Judul --}}
                <span class="text-[11px] font-bold uppercase tracking-wider text-muted block mb-2 font-sans">
                    {{ strtoupper($eyebrowCity) }} / {{ $paddedIndex }}
                </span>
                <h3 class="font-display font-bold text-3xl sm:text-4xl text-ink uppercase leading-none mb-4">
                    {{ $name }}
                </h3>

                {{-- Alamat --}}
                <div class="flex items-start gap-2.5 text-sm text-ink/85 leading-relaxed mb-3">
                    <svg class="w-4 h-4 text-primary shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                    </svg>
                    <p>{{ $address }}</p>
                </div>

                {{-- Jam Operasional --}}
                <div class="flex items-center gap-2 text-xs sm:text-sm text-muted mb-4 font-sans">
                    <svg class="w-4 h-4 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ $hours }} &middot; Setiap hari</span>
                </div>

                {{-- Fasilitas Cabang --}}
                <div class="flex flex-wrap gap-1.5 sm:gap-2 mb-2">
                    @foreach($facilities as $facility)
                        @php
                            $isPadel = str_contains(strtolower((string)$facility), 'padel');
                            $isUnconfirmed = str_contains(strtolower((string)$facility), 'konfirmasi');
                        @endphp
                        <span class="{{ $isPadel ? 'bg-sky text-primary-dark font-bold' : ($isUnconfirmed ? 'bg-soft text-muted font-medium' : 'bg-soft text-ink font-medium') }} text-[10px] uppercase tracking-wide px-2.5 py-1 rounded-[4px] border border-line">
                            {{ $facility }}
                        </span>
                    @endforeach
                </div>
                @if($hasUnconfirmedFacilities)
                    <p class="text-[11px] text-muted italic mb-3 font-sans">
                        Detail ketersediaan fasilitas selain area padel belum diberikan dalam PRD.
                    </p>
                @endif

                {{-- Embed Google Maps per Cabang (tanpa API key) --}}
                <div class="my-4">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-muted flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-primary shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.53 3.28A1.875 1.875 0 0113.88 21H10.12a1.875 1.875 0 01-1.65-.97L3.18 9.38A1.875 1.875 0 014.83 6.75h14.34a1.875 1.875 0 011.65 2.63l-5.29 10.65z" />
                            </svg>
                            Peta Lokasi Google Maps
                        </span>
                        <a
                            href="{{ $directMapsUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="text-[11px] font-bold text-primary hover:text-primary-dark inline-flex items-center gap-1 transition"
                        >
                            <span>Perbesar peta</span>
                            <span aria-hidden="true">&nearr;</span>
                        </a>
                    </div>
                    <div class="relative w-full h-44 sm:h-52 rounded-[5px] overflow-hidden border border-line bg-soft">
                        <iframe
                            title="Peta Google Maps {{ $name }}"
                            src="{{ $mapsEmbedUrl }}"
                            width="100%"
                            height="100%"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            class="w-full h-full border-0"
                        ></iframe>
                    </div>
                </div>
            </div>

            {{-- Tombol Aksi: WhatsApp, Google Maps Direct & Reservasi --}}
            <div class="pt-4 border-t border-line flex flex-wrap items-center gap-3">
                {{-- Tombol WhatsApp (wa.me) --}}
                @if($waUrl)
                    <a
                        href="{{ $waUrl }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center gap-2 bg-primary hover:brightness-95 text-white font-bold text-[13px] py-3 px-5 rounded-[5px] transition"
                    >
                        <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>Hubungi WhatsApp</span>
                        <span aria-hidden="true">&nearr;</span>
                    </a>
                @else
                    <span
                        class="inline-flex items-center justify-center gap-2 bg-soft text-muted font-bold text-[13px] py-3 px-5 rounded-[5px] border border-line cursor-not-allowed select-none"
                        aria-disabled="true"
                        title="Nomor WhatsApp cabang belum dikonfigurasi"
                    >
                        <svg class="w-4 h-4 fill-current shrink-0 opacity-60" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        <span>WhatsApp Belum Dikonfigurasi</span>
                    </span>
                @endif

                {{-- Tautan Langsung Google Maps --}}
                <a
                    href="{{ $directMapsUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-1.5 border border-line hover:border-primary text-ink hover:text-primary font-bold text-[12px] py-3 px-4 rounded-[5px] bg-white transition"
                >
                    <span>Buka Google Maps</span>
                    <span aria-hidden="true">&nearr;</span>
                </a>

                {{-- Tombol Reservasi di Cabang Ini --}}
                <a
                    href="#reservasi"
                    class="inline-flex items-center justify-center gap-1.5 border border-primary text-primary hover:bg-primary hover:text-white font-bold text-[12px] py-3 px-4 rounded-[5px] transition"
                >
                    <span>Reservasi di Cabang Ini</span>
                    <span aria-hidden="true">&nearr;</span>
                </a>
            </div>
        </div>
    </article>
@endforeach

@if(!$isSingle)
    </div>
@endif
