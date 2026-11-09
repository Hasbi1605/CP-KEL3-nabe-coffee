@extends('layouts.auth')

@section('title', 'CMS Promo — Nabe Coffee')

@section('content')
    <div class="w-full py-8">
        <span class="eyebrow text-muted mb-2">CMS PROMO</span>
        <h1 class="text-ink text-4xl font-display font-bold uppercase leading-none mb-3">DAFTAR PROMO.</h1>
        <p class="text-muted text-[13px] leading-relaxed mb-6">Kelola promo yang tampil di katalog dan halaman utama. Promo tanpa cabang berlaku untuk semua cabang.</p>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-[5px] bg-sage border border-sage-ink/20 text-sage-ink text-[13px]" role="status">{{ session('status') }}</div>
        @endif

        <p class="mb-6">
            <a href="{{ route('admin.promotions.create') }}" class="font-bold text-primary underline">+ Tambah promo</a>
            <span class="text-muted mx-2">·</span>
            <a href="{{ route('admin.homepage-content.edit') }}" class="font-bold text-primary underline">Teks info outlet &amp; promo</a>
        </p>

        <div class="bg-white border border-line rounded-[5px] overflow-x-auto">
            <table class="w-full text-left text-[13px]">
                <thead>
                    <tr class="border-b border-line text-muted uppercase text-[11px] tracking-wider">
                        <th class="px-4 py-3">Judul</th>
                        <th class="px-4 py-3">Cabang</th>
                        <th class="px-4 py-3">Periode</th>
                        <th class="px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($promotions as $promotion)
                        <tr class="border-b border-line/60">
                            <td class="px-4 py-3 font-bold text-ink">{{ $promotion->title }}</td>
                            <td class="px-4 py-3">{{ $promotion->branch?->name ?? 'Semua cabang' }}</td>
                            <td class="px-4 py-3">{{ $promotion->starts_at->format('d M Y H:i') }} – {{ $promotion->ends_at->format('d M Y H:i') }}</td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.promotions.show', $promotion) }}" class="text-primary font-bold underline">Lihat</a>
                                <a href="{{ route('admin.promotions.edit', $promotion) }}" class="text-primary font-bold underline ml-2">Ubah</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-muted">Belum ada promo. Tambah promo pertama lu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $promotions->links() }}</div>
    </div>
@endsection
