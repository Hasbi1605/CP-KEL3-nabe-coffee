<?php

namespace App\Services\Menu;

use App\Models\Branch;
use App\Models\Menu;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class MenuCatalogService
{
    /**
     * Ambil katalog publik: hanya menu tersedia, bisa difilter kategori,
     * rentang harga, cabang, dan kata kunci nama/tag.
     *
     * @param  array{category?: string|null, max_price?: int|string|null, branch_id?: int|string|null, search?: string|null}  $filters
     * @return Collection<int, array> bentuk array untuk komponen menu-card
     */
    public function catalog(array $filters = []): Collection
    {
        // Penanda promo per menu: ada promo aktif untuk cabang menu itu
        // atau promo global. Cukup satu kueri agregat di sini, bukan per menu.
        $promotionBranchIds = Promotion::query()
            ->activeNow()
            ->pluck('branch_id')
            ->map(fn ($id) => $id === null ? 0 : (int) $id)
            ->unique()
            ->all();
        $hasGlobalPromotion = in_array(0, $promotionBranchIds, true);

        return $this->query($filters)
            ->orderByDesc('is_best_seller')
            ->orderBy('name')
            ->get()
            ->map(fn (Menu $menu) => $menu->toCatalogArray(
                $hasGlobalPromotion || in_array((int) $menu->branch_id, $promotionBranchIds, true)
            ))
            ->values();
    }

    /**
     * @param  array{category?: string|null, max_price?: int|string|null, branch_id?: int|string|null, search?: string|null}  $filters
     */
    public function query(array $filters = []): Builder
    {
        $category = $filters['category'] ?? null;
        $maxPrice = $this->nullableInt($filters['max_price'] ?? null);
        $branchId = $this->nullableInt($filters['branch_id'] ?? null);
        $search = trim((string) ($filters['search'] ?? ''));

        return Menu::query()
            ->where('is_available', true)
            ->when($category !== null && $category !== '' && $category !== 'all', fn (Builder $q) => $q->where('category', $category))
            ->when($maxPrice !== null, fn (Builder $q) => $q->where('price', '<=', $maxPrice))
            ->when($branchId !== null, fn (Builder $q) => $q->where('branch_id', $branchId))
            ->when($search !== '', function (Builder $q) use ($search): void {
                $q->where(function (Builder $inner) use ($search): void {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhereJsonContains('tags', $search);
                });
            })
            ->with('branch');
    }

    /**
     * @return Collection<int, Branch>
     */
    public function branches(): Collection
    {
        return Branch::query()->orderBy('name')->get();
    }

    public function branchOptions(): Collection
    {
        return $this->branches()->pluck('name');
    }

    private function nullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '' || $value === 'all') {
            return null;
        }

        return (int) $value;
    }
}
