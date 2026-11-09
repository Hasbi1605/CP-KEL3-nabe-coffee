@extends('layouts.auth')

@section('title', 'Tambah Promo — Nabe Coffee')

@section('content')
    <div class="w-full py-8">
        <span class="eyebrow text-muted mb-2">CMS PROMO</span>
        <h1 class="text-ink text-4xl font-display font-bold uppercase leading-none mb-6">TAMBAH PROMO.</h1>
        <form method="POST" action="{{ route('admin.promotions.store') }}" enctype="multipart/form-data">
            @csrf
            @include('admin.promotions._form')
        </form>
        <p class="mt-6"><a href="{{ route('admin.promotions.index') }}" class="text-primary font-bold underline">Kembali ke daftar promo</a></p>
    </div>
@endsection
