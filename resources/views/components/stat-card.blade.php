@props(['label', 'value', 'detail' => null])

<article {{ $attributes->merge(['class' => 'rounded-[5px] border border-line bg-white p-[22px]']) }}>
    <span class="block text-[13px] text-muted">{{ $label }}</span>
    <strong class="mt-2 block font-display text-5xl font-bold leading-none text-ink">{{ $value }}</strong>
    @if ($detail)
        <small class="mt-2 block text-[13px] text-muted">{{ $detail }}</small>
    @endif
</article>
