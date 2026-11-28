<div
    class="chat-widget fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-40 flex flex-col items-end gap-3"
    data-chatbot-widget
    data-endpoint="{{ route('chatbot.message') }}"
>
    {{-- Panel Percakapan Chatbot --}}
    <section
        id="nabe-chatbot-panel"
        class="chat-panel hidden w-[min(360px,calc(100vw-32px))] max-h-[min(560px,calc(100dvh-104px))] flex-col overflow-hidden rounded-[5px] border border-line bg-cream shadow-[0_10px_50px_rgb(0_0_0_/_0.2)]"
        aria-label="Chat Nabe Buddy"
        aria-hidden="true"
    >
        {{-- Header Panel --}}
        <header class="flex items-center justify-between gap-3 bg-primary px-4 py-3.5 text-white">
            <div class="flex items-center gap-2.5 min-w-0">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/15 text-lime" aria-hidden="true">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                </span>
                <div class="min-w-0">
                    <strong class="block truncate text-[15px] font-bold leading-tight">Nabe Buddy</strong>
                    <small class="mt-0.5 block truncate text-[11px] font-normal text-white/85">Asisten informasi cabang &amp; menu</small>
                </div>
            </div>
            <button
                type="button"
                id="nabe-chatbot-close"
                aria-label="Tutup chatbot"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-[5px] text-white hover:bg-white/15 cursor-pointer"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </header>

        {{-- Daftar Pesan --}}
        <div
            id="nabe-chatbot-messages"
            class="chat-messages flex flex-1 flex-col gap-3 overflow-y-auto bg-cream p-4 min-h-[180px] max-h-[250px]"
            role="log"
            aria-live="polite"
            aria-atomic="false"
        >
            <div class="chat-bubble-bot self-start max-w-[92%] rounded-[5px] border border-line/70 bg-soft p-3 text-[12px] leading-relaxed text-ink">
                <p>Halo! Aku Nabe Buddy. Tanya rekomendasi menu terbaik, promo aktif, jam buka &amp; lokasi cabang, atau panduan reservasi di sini.</p>
            </div>
        </div>

        {{-- Status Loading & Error --}}
        <div
            id="nabe-chatbot-loading"
            class="hidden items-center gap-2 border-t border-line/60 bg-soft/60 px-4 py-2 text-[11px] font-medium text-muted"
            aria-live="polite"
        >
            <span class="chat-loading-dot inline-block h-2 w-2 rounded-full bg-primary" aria-hidden="true"></span>
            <span>Nabe Buddy sedang menyiapkan jawaban…</span>
        </div>

        <div
            id="nabe-chatbot-error"
            class="hidden items-center justify-between gap-2 border-t border-red/30 bg-blush/60 px-4 py-2 text-[11px] text-blush-ink"
            role="alert"
        >
            <span id="nabe-chatbot-error-text"></span>
            <button
                type="button"
                id="nabe-chatbot-error-dismiss"
                aria-label="Tutup pesan kendala"
                class="shrink-0 font-bold underline cursor-pointer"
            >
                Tutup
            </button>
        </div>

        {{-- Pemilih Cabang (Dari Database Laravel) --}}
        <div class="border-t border-line/70 bg-cream px-3.5 pt-2.5 pb-2">
            <label for="nabe-chatbot-branch" class="sr-only">Cabang acuan chatbot</label>
            <select
                id="nabe-chatbot-branch"
                aria-label="Cabang chatbot"
                class="w-full rounded-[5px] border border-line bg-white px-2.5 py-1.5 text-[11px] font-medium text-ink"
            >
                @if ($branches->isEmpty())
                    <option value="">Data cabang belum tersedia</option>
                @else
                    <option value="">Semua cabang Nabe Coffee</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        {{-- Topik Cepat --}}
        <div class="flex flex-wrap gap-1.5 bg-cream px-3.5 pb-2.5" aria-label="Pertanyaan cepat">
            @foreach ([
                'Rekomendasi menu',
                'Promo hari ini',
                'Jam & lokasi',
                'Cara reservasi',
                'Cek status',
            ] as $prompt)
                <button
                    type="button"
                    data-chatbot-prompt="{{ $prompt }}"
                    class="rounded-[5px] border border-line bg-white px-2.5 py-1 text-[11px] font-bold text-ink hover:border-primary hover:text-primary cursor-pointer"
                >
                    {{ $prompt }}
                </button>
            @endforeach
            <a
                href="{{ route('menu.index') }}"
                class="rounded-[5px] border border-line bg-white px-2.5 py-1 text-[11px] font-bold text-primary hover:border-primary"
            >
                Katalog menu ↗
            </a>
            <a
                href="{{ url('/#outlet') }}"
                class="rounded-[5px] border border-line bg-white px-2.5 py-1 text-[11px] font-bold text-primary hover:border-primary"
            >
                Info cabang ↗
            </a>
        </div>

        {{-- Form Input Pesan --}}
        <form id="nabe-chatbot-form" class="flex items-center gap-2 border-t border-line bg-white p-2.5" novalidate>
            <input
                type="text"
                id="nabe-chatbot-input"
                aria-label="Pesan chatbot"
                placeholder="Tanya menu, promo, jam buka, atau reservasi…"
                maxlength="500"
                autocomplete="off"
                class="min-w-0 flex-1 rounded-[5px] border border-line bg-cream/60 px-3 py-2 text-[12px] text-ink placeholder:text-muted"
            />
            <button
                type="submit"
                id="nabe-chatbot-submit"
                aria-label="Kirim pesan"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-primary text-white hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18" />
                </svg>
            </button>
        </form>
    </section>

    {{-- Tombol Pil Mengambang ("Hai, Nabe di sini!") --}}
    <button
        type="button"
        id="nabe-chatbot-launcher"
        aria-expanded="false"
        aria-controls="nabe-chatbot-panel"
        class="chat-launcher inline-flex items-center gap-2.5 rounded-full border border-white/40 bg-primary px-4 py-3 text-[12px] font-bold text-white shadow-[0_5px_20px_rgba(9,42,56,0.14)] hover:brightness-95 cursor-pointer"
    >
        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
        </svg>
        <span data-chatbot-launcher-label>Hai, Nabe di sini!</span>
        <span class="h-2 w-2 rounded-full bg-lime shrink-0" aria-hidden="true"></span>
    </button>
</div>
