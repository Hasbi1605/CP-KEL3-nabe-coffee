@extends('layouts.admin')

@section('title', 'Teks Halaman Utama — Nabe Coffee')
@section('page_title', 'Teks Halaman Utama')

@section('content')
    <div class="w-full py-8">
        <span class="eyebrow text-muted mb-2">CMS HALAMAN UTAMA</span>
        <h1 class="text-ink text-4xl font-display font-bold uppercase leading-none mb-3">TEKS INFO OUTLET &amp; PROMO.</h1>
        <p class="text-muted text-[13px] leading-relaxed mb-6">Teks ini tampil di section info cabang dan section promo halaman utama. Kosongkan untuk kembali ke teks bawaan.</p>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-[5px] bg-sage border border-sage-ink/20 text-sage-ink text-[13px]" role="status">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-[5px] bg-blush border border-red/30 text-blush-ink text-[13px]" role="alert">
                <strong class="font-bold block">Data tidak valid:</strong>
                <ul class="list-disc list-inside mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.homepage-content.update') }}" class="space-y-5">
            @csrf
            @method('PUT')
            <x-input name="content[outlet_info_title]" label="Judul info outlet" :value="old('content.outlet_info_title', $content['outlet_info_title'])" maxlength="255" />
            <div>
                <label for="outlet_info_text" class="block text-ink text-[12px] font-bold uppercase tracking-wider mb-2">Teks info outlet</label>
                <textarea name="content[outlet_info_text]" id="outlet_info_text" rows="3" maxlength="2000" class="w-full bg-white text-ink px-3 py-2.5 rounded-[5px] border border-line focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none">{{ old('content.outlet_info_text', $content['outlet_info_text']) }}</textarea>
            </div>
            <x-input name="content[promo_title]" label="Judul section promo" :value="old('content.promo_title', $content['promo_title'])" maxlength="255" />
            <div>
                <label for="promo_text" class="block text-ink text-[12px] font-bold uppercase tracking-wider mb-2">Teks section promo</label>
                <textarea name="content[promo_text]" id="promo_text" rows="3" maxlength="2000" class="w-full bg-white text-ink px-3 py-2.5 rounded-[5px] border border-line focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none">{{ old('content.promo_text', $content['promo_text']) }}</textarea>
            </div>
            <x-button type="submit" variant="primary" class="w-full justify-center text-[13px] py-3 uppercase tracking-wider">Simpan Teks</x-button>
        </form>

        <p class="mt-6"><a href="{{ route('admin.promotions.index') }}" class="text-primary font-bold underline">Kembali ke CMS promo</a></p>
    </div>
@endsection
