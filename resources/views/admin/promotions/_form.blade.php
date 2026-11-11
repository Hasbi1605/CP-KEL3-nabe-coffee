@php
    $isEdit = isset($promotion);
@endphp

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

<div class="space-y-5">
    <x-input name="title" label="Judul promo" :value="old('title', $promotion->title ?? '')" required maxlength="255" />
    <div>
        <label for="description" class="block text-ink text-[12px] font-bold uppercase tracking-wider mb-2">Deskripsi</label>
        <textarea name="description" id="description" rows="3" maxlength="2000" class="w-full bg-white text-ink px-3 py-2.5 rounded-[5px] border border-line focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none">{{ old('description', $promotion->description ?? '') }}</textarea>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-input name="starts_at" type="datetime-local" label="Mulai" :value="old('starts_at', isset($promotion) ? $promotion->starts_at->format('Y-m-d\TH:i') : '')" required />
        <x-input name="ends_at" type="datetime-local" label="Selesai" :value="old('ends_at', isset($promotion) ? $promotion->ends_at->format('Y-m-d\TH:i') : '')" required />
    </div>
    <div>
        <label for="terms" class="block text-ink text-[12px] font-bold uppercase tracking-wider mb-2">Syarat &amp; ketentuan</label>
        <textarea name="terms" id="terms" rows="2" maxlength="2000" class="w-full bg-white text-ink px-3 py-2.5 rounded-[5px] border border-line focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none">{{ old('terms', $promotion->terms ?? '') }}</textarea>
    </div>
    <div>
        <label for="branch_id" class="block text-ink text-[12px] font-bold uppercase tracking-wider mb-2">Cabang</label>
        <select name="branch_id" id="branch_id" class="w-full bg-white text-ink px-3 py-2.5 rounded-[5px] border border-line">
            <option value="">Semua cabang</option>
            @foreach ($branches as $branch)
                <option value="{{ $branch->id }}" @selected(old('branch_id', $promotion->branch_id ?? null) == $branch->id)>{{ $branch->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label for="banner" class="block text-ink text-[12px] font-bold uppercase tracking-wider mb-2">Banner (JPG/PNG/WebP, maks 2 MB)</label>
        <input type="file" name="banner" id="banner" accept=".jpg,.jpeg,.png,.webp" class="w-full bg-white text-ink px-3 py-2.5 rounded-[5px] border border-line">
        @if ($isEdit && $promotion->banner)
            <p class="mt-2 text-[12px] text-muted">Banner sekarang: <a href="{{ asset('storage/'.$promotion->banner) }}" target="_blank" class="text-primary underline">{{ $promotion->banner }}</a></p>
            <label class="mt-2 flex items-center gap-2 text-[12px]"><input type="checkbox" name="remove_banner" value="1" @checked(old('remove_banner'))> Hapus banner</label>
        @endif
    </div>
    <x-button type="submit" variant="primary" class="w-full justify-center text-[13px] py-3 uppercase tracking-wider">{{ $isEdit ? 'Simpan Perubahan' : 'Tambah Promo' }}</x-button>
</div>
