<?php

namespace App\Models;

use App\Enums\MenuCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    /** Nilai kategori sesuai enum di migration resmi (1.1). */
    public const CATEGORIES = ['coffee', 'non_coffee', 'heavy_meal', 'snack'];

    public const CATEGORY_LABELS = [
        'coffee' => 'Kopi',
        'non_coffee' => 'Non-Kopi',
        'heavy_meal' => 'Makanan Berat',
        'snack' => 'Camilan',
    ];

    protected $fillable = [
        'branch_id', 'name', 'category', 'price',
        'photo', 'tags', 'is_best_seller', 'is_available',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_best_seller' => 'boolean',
        'is_available' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * @return array{name: string, category: string, description: string, price: int, image: string, best_seller: bool, promo: bool}
     */
    public function toCatalogArray(bool $hasActivePromotion = false): array
    {
        $tags = collect($this->tags ?? []);
        $description = $tags->isNotEmpty()
            ? $tags->map(fn (string $tag) => ucfirst($tag))->join(', ').'.'
            : 'Menu pilihan Nabe Coffee, disiapkan segar setiap hari.';

        return [
            'name' => $this->name,
            'category' => MenuCategory::from($this->category)->label(),
            'description' => $description,
            'price' => (int) $this->price,
            'image' => $this->photo ?: 'images/menu/1461023058943-07fcbe16d735.jpg',
            'best_seller' => (bool) $this->is_best_seller,
            'promo' => $hasActivePromotion,
        ];
    }
}
