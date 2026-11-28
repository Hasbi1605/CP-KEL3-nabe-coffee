<?php

namespace App\Services\Chatbot;

use App\Enums\MenuCategory;
use App\Models\Branch;
use App\Models\Menu;
use App\Models\Promotion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ChatbotReplyService
{
    /**
     * Susun jawaban terstruktur berbahasa Indonesia dari data database Laravel.
     * Titik integrasi NLP FastAPI (tugas 6.1) dapat disambungkan lewat resolveIntent().
     *
     * @return array{
     *     intent: string,
     *     source: string,
     *     ai_ready: bool,
     *     reply: string,
     *     items: array<int, array{title: string, meta: string, badges: array<int, string>, url: string|null}>,
     *     actions: array<int, array{label: string, url: string}>,
     *     empty: bool
     * }
     */
    public function reply(string $message, ?int $branchId = null): array
    {
        $branch = $branchId !== null ? Branch::query()->find($branchId) : null;
        $intent = $this->resolveIntent($message);

        return match ($intent) {
            'rekomendasi_menu' => $this->menuRecommendations($branch),
            'tanya_promo' => $this->activePromotions($branch),
            'tanya_jam', 'tanya_lokasi' => $this->branchLocationsAndHours($branch),
            'tanya_menu' => $this->menuCatalogOverview($branch),
            'reservasi_baru' => $this->reservationGuide($branch),
            'cek_reservasi', 'ubah_reservasi', 'batal_reservasi' => $this->reservationStatusGuide(),
            default => $this->politeFallback(),
        };
    }

    /**
     * Pencocokan intent sementara di sisi Laravel untuk alur UI sebelum model NLP (6.1) terhubung.
     */
    private function resolveIntent(string $message): string
    {
        $query = Str::lower(trim($message));

        return match (true) {
            Str::contains($query, ['rekomendasi', 'terlaris', 'favorit', 'best seller', 'enak', 'saran']) => 'rekomendasi_menu',
            Str::contains($query, ['promo', 'diskon', 'penawaran', 'voucher', 'hemat']) => 'tanya_promo',
            Str::contains($query, ['status', 'tiket', 'nab-', 'ubah reservasi', 'batal reservasi', 'cek reservasi']) => 'cek_reservasi',
            Str::contains($query, ['reservasi', 'booking', 'pesan tempat', 'meja', 'slot', 'amankan']) => 'reservasi_baru',
            Str::contains($query, ['jam', 'buka', 'tutup', 'operasional']) => 'tanya_jam',
            Str::contains($query, ['lokasi', 'alamat', 'cabang', 'outlet', 'peta', 'maps', 'di mana', 'dimana']) => 'tanya_lokasi',
            Str::contains($query, ['menu', 'kopi', 'minuman', 'makanan', 'camilan', 'harga', 'katalog']) => 'tanya_menu',
            default => 'fallback',
        };
    }

    /**
     * Maksimal 3 menu terbaik dari data best-seller & promo aktif (cold-start PRD §6).
     */
    private function menuRecommendations(?Branch $branch): array
    {
        $promotionBranchIds = Promotion::query()
            ->activeNow()
            ->pluck('branch_id')
            ->map(fn ($id) => $id === null ? 0 : (int) $id)
            ->unique()
            ->all();
        $hasGlobalPromotion = in_array(0, $promotionBranchIds, true);

        $menus = Menu::query()
            ->where('is_available', true)
            ->when($branch !== null, fn ($q) => $q->where('branch_id', $branch->id))
            ->with('branch')
            ->orderByDesc('is_best_seller')
            ->orderBy('name')
            ->limit(3)
            ->get();

        $catalogUrl = route('menu.index', $branch !== null ? ['branch_id' => $branch->id] : []);
        $branchLabel = $branch?->name ?? 'seluruh cabang Nabe Coffee';

        if ($menus->isEmpty()) {
            return [
                'intent' => 'rekomendasi_menu',
                'source' => 'laravel_data',
                'ai_ready' => false,
                'reply' => "Belum ada menu yang tersedia di database saat ini untuk {$branchLabel}.",
                'items' => [],
                'actions' => [
                    ['label' => 'Lihat Katalog Menu', 'url' => route('menu.index')],
                ],
                'empty' => true,
            ];
        }

        $items = $menus->map(function (Menu $menu) use ($hasGlobalPromotion, $promotionBranchIds): array {
            $badges = [];
            if ($menu->is_best_seller) {
                $badges[] = 'Terlaris';
            }
            if ($hasGlobalPromotion || in_array((int) $menu->branch_id, $promotionBranchIds, true)) {
                $badges[] = 'Promo';
            }

            $categoryLabel = MenuCategory::tryFrom((string) $menu->category)?->label() ?? 'Menu';
            $branchSuffix = $menu->branch ? " · {$menu->branch->name}" : '';

            return [
                'title' => $menu->name,
                'meta' => $categoryLabel.' · '.$this->formatRupiah((int) $menu->price).$branchSuffix,
                'badges' => $badges,
                'url' => route('menu.index', ['branch_id' => $menu->branch_id]),
            ];
        })->values()->all();

        return [
            'intent' => 'rekomendasi_menu',
            'source' => 'laravel_data',
            'ai_ready' => false,
            'reply' => "Berikut 3 pilihan menu terbaik untuk {$branchLabel}:",
            'items' => $items,
            'actions' => [
                ['label' => 'Buka Katalog Lengkap', 'url' => $catalogUrl],
            ],
            'empty' => false,
        ];
    }

    private function activePromotions(?Branch $branch): array
    {
        $promotions = Promotion::query()
            ->activeNow()
            ->forBranch($branch?->id)
            ->with('branch')
            ->orderByDesc('starts_at')
            ->limit(3)
            ->get();

        if ($promotions->isEmpty()) {
            $branchScope = $branch !== null ? " di {$branch->name}" : '';

            return [
                'intent' => 'tanya_promo',
                'source' => 'laravel_data',
                'ai_ready' => false,
                'reply' => "Belum ada promo yang aktif saat ini{$branchScope}. Pantau bagian promo di beranda atau Instagram @nabecoffee.id untuk info terbaru.",
                'items' => [],
                'actions' => [
                    ['label' => 'Cek Bagian Promo', 'url' => url('/#promo')],
                ],
                'empty' => true,
            ];
        }

        $items = $promotions->map(function (Promotion $promotion): array {
            $targetBranch = $promotion->branch?->name ?? 'Semua Cabang';
            $period = $promotion->starts_at && $promotion->ends_at
                ? $promotion->starts_at->format('d M').' – '.$promotion->ends_at->format('d M Y')
                : 'Periode berjalan';
            $terms = $promotion->terms ? " · {$promotion->terms}" : '';

            return [
                'title' => $promotion->title,
                'meta' => "{$targetBranch} · {$period}{$terms}",
                'badges' => ['Promo'],
                'url' => url('/#promo'),
            ];
        })->values()->all();

        return [
            'intent' => 'tanya_promo',
            'source' => 'laravel_data',
            'ai_ready' => false,
            'reply' => 'Promo Nabe Coffee yang sedang aktif saat ini:',
            'items' => $items,
            'actions' => [
                ['label' => 'Lihat Detail Promo', 'url' => url('/#promo')],
            ],
            'empty' => false,
        ];
    }

    private function branchLocationsAndHours(?Branch $branch): array
    {
        /** @var Collection<int, Branch> $branches */
        $branches = $branch !== null
            ? collect([$branch])
            : Branch::query()->orderBy('name')->get();

        if ($branches->isEmpty()) {
            return [
                'intent' => 'tanya_lokasi',
                'source' => 'laravel_data',
                'ai_ready' => false,
                'reply' => 'Data cabang belum tersedia di sistem saat ini.',
                'items' => [],
                'actions' => [
                    ['label' => 'Lihat Bagian Outlet', 'url' => url('/#outlet')],
                ],
                'empty' => true,
            ];
        }

        $items = $branches->map(function (Branch $item): array {
            $hours = $this->formatBranchHours($item->opens_at, $item->closes_at);
            $mapsUrl = $item->maps_url ?: 'https://www.google.com/maps/search/?api=1&query='.urlencode($item->name.' '.$item->address);

            return [
                'title' => $item->name,
                'meta' => "{$item->address} · Jam buka: {$hours}",
                'badges' => [],
                'url' => $mapsUrl,
            ];
        })->values()->all();

        $reply = $branch !== null
            ? "Informasi alamat dan jam operasional {$branch->name}:"
            : 'Berikut daftar cabang dan jam operasional Nabe Coffee:';

        return [
            'intent' => 'tanya_lokasi',
            'source' => 'laravel_data',
            'ai_ready' => false,
            'reply' => $reply,
            'items' => $items,
            'actions' => [
                ['label' => 'Lihat Peta & Info Cabang', 'url' => url('/#outlet')],
            ],
            'empty' => false,
        ];
    }

    private function menuCatalogOverview(?Branch $branch): array
    {
        $query = Menu::query()
            ->where('is_available', true)
            ->when($branch !== null, fn ($q) => $q->where('branch_id', $branch->id));

        $count = $query->count();
        $catalogUrl = route('menu.index', $branch !== null ? ['branch_id' => $branch->id] : []);

        if ($count === 0) {
            return [
                'intent' => 'tanya_menu',
                'source' => 'laravel_data',
                'ai_ready' => false,
                'reply' => 'Belum ada data menu yang tersedia di katalog saat ini.',
                'items' => [],
                'actions' => [
                    ['label' => 'Buka Halaman Menu', 'url' => $catalogUrl],
                ],
                'empty' => true,
            ];
        }

        $categories = $query->distinct()->pluck('category')
            ->map(fn (string $category) => MenuCategory::tryFrom($category)?->label())
            ->filter()
            ->values()
            ->join(', ');

        $branchText = $branch !== null ? " di {$branch->name}" : ' di seluruh cabang';

        return [
            'intent' => 'tanya_menu',
            'source' => 'laravel_data',
            'ai_ready' => false,
            'reply' => "Tersedia {$count} menu aktif{$branchText} ({$categories}). Kamu bisa memfilter kategori, rentang harga, dan memilih cabang di halaman katalog.",
            'items' => [],
            'actions' => [
                ['label' => 'Jelajahi Katalog Menu', 'url' => $catalogUrl],
            ],
            'empty' => false,
        ];
    }

    private function reservationGuide(?Branch $branch): array
    {
        $branchHint = $branch !== null ? " untuk {$branch->name}" : '';
        $hasBookingRoute = Route::has('reservations.create');

        $reply = "Untuk melakukan reservasi{$branchHint}, kamu akan memilih cabang, tanggal & jam, mengecek sisa kuota slot, mengisi jumlah tamu & kebutuhan fasilitas, lalu memilih Pre-Order atau Order di Outlet.";

        if (! $hasBookingRoute) {
            $reply .= ' Form reservasi online sedang disiapkan di modul berikutnya; sementara ini silakan cek info cabang di bagian Outlet atau hubungi @nabecoffee.id.';
        }

        return [
            'intent' => 'reservasi_baru',
            'source' => 'laravel_data',
            'ai_ready' => false,
            'reply' => $reply,
            'items' => [],
            'actions' => [
                [
                    'label' => $hasBookingRoute ? 'Mulai Reservasi' : 'Lihat Lokasi Cabang',
                    'url' => $hasBookingRoute ? route('reservations.create') : url('/#outlet'),
                ],
            ],
            'empty' => false,
        ];
    }

    private function reservationStatusGuide(): array
    {
        $hasStatusRoute = Route::has('reservations.status');

        if ($hasStatusRoute) {
            return [
                'intent' => 'cek_reservasi',
                'source' => 'laravel_data',
                'ai_ready' => false,
                'reply' => 'Kamu dapat mengecek, mengubah jadwal, atau membatalkan reservasi menggunakan kode tiket dan nomor HP melalui halaman Cek Status Reservasi.',
                'items' => [],
                'actions' => [
                    ['label' => 'Buka Cek Status Reservasi', 'url' => route('reservations.status')],
                ],
                'empty' => false,
            ];
        }

        return [
            'intent' => 'cek_reservasi',
            'source' => 'laravel_data',
            'ai_ready' => false,
            'reply' => 'Layanan cek, ubah, atau batal reservasi mandiri belum aktif di website saat ini. Untuk bantuan jadwal atau konfirmasi, silakan hubungi kanal resmi @nabecoffee.id.',
            'items' => [],
            'actions' => [
                ['label' => 'Hubungi @nabecoffee.id', 'url' => 'https://www.instagram.com/nabecoffee.id/'],
            ],
            'empty' => false,
        ];
    }

    private function politeFallback(): array
    {
        return [
            'intent' => 'fallback',
            'source' => 'fallback',
            'ai_ready' => false,
            'reply' => 'Maaf, aku belum memahami pertanyaan itu (integrasi penuh asisten NLP akan segera tersedia). Sementara ini, coba tanyakan tentang rekomendasi menu, promo aktif, jam & lokasi cabang, atau panduan reservasi.',
            'items' => [],
            'actions' => [
                ['label' => 'Lihat Katalog Menu', 'url' => route('menu.index')],
                ['label' => 'Info 4 Cabang', 'url' => url('/#outlet')],
            ],
            'empty' => false,
        ];
    }

    private function formatRupiah(int $amount): string
    {
        return 'Rp'.number_format($amount, 0, ',', '.');
    }

    private function formatBranchHours(?string $opensAt, ?string $closesAt): string
    {
        if ($opensAt === null || $closesAt === null) {
            return 'Jam operasional belum tersedia';
        }

        $open = str_replace(':', '.', substr($opensAt, 0, 5));
        $close = str_replace(':', '.', substr($closesAt, 0, 5));

        return "{$open}–{$close} WIB";
    }
}
