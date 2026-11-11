@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@php
    $role = $role ?? 'owner';

    // DATA DUMMY. Ganti dengan data asli saat model reservasi sudah ada.
    $stats = $role === 'owner'
        ? [
            ['label' => 'Reservasi hari ini', 'value' => 42, 'detail' => 'Kunjungan terjadwal'],
            ['label' => 'Pending', 'value' => 9, 'detail' => 'Menunggu konfirmasi'],
            ['label' => '7 hari terakhir', 'value' => 268, 'detail' => 'Berdasarkan tanggal kunjungan'],
            ['label' => 'Risiko no-show tinggi', 'value' => 6, 'detail' => 'Perlu konfirmasi H-1'],
        ]
        : [
            ['label' => 'Reservasi hari ini', 'value' => 11, 'detail' => 'Kunjungan terjadwal'],
            ['label' => 'Pending', 'value' => 3, 'detail' => 'Menunggu konfirmasi'],
            ['label' => '7 hari terakhir', 'value' => 67, 'detail' => 'Berdasarkan tanggal kunjungan'],
            ['label' => 'Risiko no-show tinggi', 'value' => 2, 'detail' => 'Perlu konfirmasi H-1'],
        ];

    // Nama cabang sesuai PRD §3. Ganti dengan data dari tabel branches nanti.
    $branchRows = [
        ['name' => 'Nabe Coffee Kusumanegara', 'today' => 11, 'week' => 67, 'month' => 281],
        ['name' => 'Nabe Coffee Demangan', 'today' => 9, 'week' => 58, 'month' => 240],
        ['name' => 'Nabe Coffee Soragan', 'today' => 12, 'week' => 74, 'month' => 305],
        ['name' => 'Nabe Coffee Batikan', 'today' => 10, 'week' => 69, 'month' => 262],
    ];
    if ($role !== 'owner') {
        $branchRows = array_slice($branchRows, 0, 1);
    }
@endphp

@section('content')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-3 rounded-[5px] border border-line bg-white p-[22px]">
        <div>
            <span class="eyebrow text-primary">Your daily overview</span>
            <h2 class="mt-2 text-4xl text-ink sm:text-5xl">Good plans start here.</h2>
            <p class="mt-2 text-muted">
                {{ $role === 'owner' ? 'Semua cabang, satu pandangan.' : 'Pantau cabangmu hari ini.' }}
            </p>
        </div>
        <span class="text-[13px] text-muted">{{ now()->translatedFormat('j F Y') }}</span>
    </div>

    <p class="mb-4 rounded-[5px] bg-sky px-4 py-2.5 text-[13px] text-primary-dark">
        Data di halaman ini masih contoh (dummy) dan belum terhubung ke database.
    </p>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $stat)
            <x-stat-card :label="$stat['label']" :value="$stat['value']" :detail="$stat['detail']" />
        @endforeach
    </div>

    <section class="mt-6 rounded-[5px] border border-line bg-white">
        <div class="flex items-center justify-between gap-3 border-b border-line px-[22px] py-4">
            <h3 class="text-xl font-bold leading-tight text-ink">Ringkasan cabang</h3>
            <span class="eyebrow text-muted">{{ $role === 'owner' ? 'Tampilan owner' : 'Cabang kamu' }}</span>
        </div>
        <div class="overflow-x-auto" tabindex="0" aria-label="Tabel ringkasan cabang, dapat digeser ke samping">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-soft text-[13px] text-muted">
                        <th scope="col" class="px-3 py-3 sm:px-[22px] font-bold">Cabang</th>
                        <th scope="col" class="px-3 py-3 sm:px-[22px] text-right font-bold">Hari ini</th>
                        <th scope="col" class="px-3 py-3 sm:px-[22px] text-right font-bold">7 hari</th>
                        <th scope="col" class="px-3 py-3 sm:px-[22px] text-right font-bold">30 hari</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($branchRows as $row)
                        <tr class="border-t border-line even:bg-cream">
                            <th scope="row" class="px-3 py-3 sm:px-[22px] font-bold text-ink">{{ $row['name'] }}</th>
                            <td class="px-3 py-3 sm:px-[22px] text-right">{{ $row['today'] }}</td>
                            <td class="px-3 py-3 sm:px-[22px] text-right">{{ $row['week'] }}</td>
                            <td class="px-3 py-3 sm:px-[22px] text-right">{{ $row['month'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-[22px] py-8 text-center text-muted">Belum ada data cabang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
@endsection
