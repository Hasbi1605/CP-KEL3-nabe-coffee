@extends('layouts.auth')

@section('title', 'Detail Promo — Nabe Coffee')

@section('content')
    <div class="w-full py-8">
        <span class="eyebrow text-muted mb-2">CMS PROMO</span>
        <h1 class="text-ink text-4xl font-display font-bold uppercase leading-none mb-4">{{ $promotion->title }}</h1>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-[5px] bg-sage border border-sage-ink/20 text-sage-ink text-[13px]" role="status">{{ session('status') }}</div>
        @endif

        @if ($promotion->banner)
            <img src="{{ asset('storage/'.$promotion->banner) }}" alt="Banner {{ $promotion->title }}" class="mb-6 w-full max-h-72 object-cover rounded-[5px] border border-line">
        @endif

        <dl class="space-y-3 text-[13px] bg-white border border-line rounded-[5px] p-5">
            <div><dt class="font-bold text-muted uppercase text-[11px] tracking-wider">Deskripsi</dt><dd>{{ $promotion->description ?? '-' }}</dd></div>
            <div><dt class="font-bold text-muted uppercase text-[11px] tracking-wider">Periode</dt><dd>{{ $promotion->starts_at->format('d M Y H:i') }} – {{ $promotion->ends_at->format('d M Y H:i') }}</dd></div>
            <div><dt class="font-bold text-muted uppercase text-[11px] tracking-wider">Cabang</dt><dd>{{ $promotion->branch?->name ?? 'Semua cabang' }}</dd></div>
            <div><dt class="font-bold text-muted uppercase text-[11px] tracking-wider">Syarat</dt><dd>{{ $promotion->terms ?? '-' }}</dd></div>
        </dl>

        <p class="mt-6 flex gap-4">
            <a href="{{ route('admin.promotions.edit', $promotion) }}" class="text-primary font-bold underline">Ubah promo</a>
            <a href="{{ route('admin.promotions.index') }}" class="text-primary font-bold underline">Kembali ke daftar</a>
        </p>
    </div>
@endsection
