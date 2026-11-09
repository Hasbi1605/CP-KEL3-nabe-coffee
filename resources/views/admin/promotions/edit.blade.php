@extends('layouts.auth')

@section('title', 'Ubah Promo — Nabe Coffee')

@section('content')
    <div class="w-full py-8">
        <span class="eyebrow text-muted mb-2">CMS PROMO</span>
        <h1 class="text-ink text-4xl font-display font-bold uppercase leading-none mb-6">UBAH PROMO.</h1>
        <form method="POST" action="{{ route('admin.promotions.update', $promotion) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.promotions._form')
        </form>
        <form method="POST" action="{{ route('admin.promotions.destroy', $promotion) }}" class="mt-8" onsubmit="return confirm('Hapus promo ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red font-bold underline">Hapus promo ini</button>
        </form>
        <p class="mt-6"><a href="{{ route('admin.promotions.index') }}" class="text-primary font-bold underline">Kembali ke daftar promo</a></p>
    </div>
@endsection
