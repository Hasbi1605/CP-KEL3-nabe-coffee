@props(['menu'])

<article class="menu-card" data-menu-card data-category="{{ $menu['category'] }}" data-price="{{ $menu['price'] }}">
    <div class="relative aspect-4/3 overflow-hidden bg-soft">
        <img
            src="{{ asset($menu['image']) }}"
            alt="{{ $menu['name'] }}"
            class="size-full object-cover"
        >

        <div class="absolute left-3 top-3 flex flex-wrap gap-2">
            @if ($menu['best_seller'])
                <span class="rounded bg-gold px-2.5 py-1 text-[10px] font-bold tracking-wider text-gold-ink">Terlaris</span>
            @endif

            @if ($menu['promo'])
                <span class="rounded bg-blush px-2.5 py-1 text-[10px] font-bold tracking-wider text-blush-ink">Promo</span>
            @endif
        </div>
    </div>

    <div class="flex flex-1 flex-col items-start p-5">
        <p class="text-[10px] font-bold tracking-wider text-primary">{{ $menu['category'] }}</p>
        <h3 class="mt-2 text-xl font-bold text-ink">{{ $menu['name'] }}</h3>
        <p class="mt-2 text-sm leading-6 text-muted">{{ $menu['description'] }}</p>
        <strong class="mt-5 text-base text-ink">Rp{{ number_format($menu['price'], 0, ',', '.') }}</strong>

        <button
            type="button"
            class="menu-detail-trigger mt-5 inline-flex min-h-11 items-center justify-center rounded-[5px] bg-primary px-5 text-[13px] font-bold text-white hover:brightness-95"
            data-menu-detail
            data-name="{{ $menu['name'] }}"
            data-category="{{ $menu['category'] }}"
            data-description="{{ $menu['description'] }}"
            data-price="Rp{{ number_format($menu['price'], 0, ',', '.') }}"
            data-image="{{ asset($menu['image']) }}"
            data-best-seller="{{ $menu['best_seller'] ? 'Terlaris' : '' }}"
            data-promo="{{ $menu['promo'] ? 'Promo' : '' }}"
        >
            Lihat detail
        </button>
    </div>
</article>
