<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'starts_at', 'ends_at',
        'terms', 'banner', 'branch_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Promo yang sedang berjalan saat ini. branch_id null artinya
     * berlaku untuk semua cabang.
     */
    public function scopeActiveNow($query)
    {
        $now = now();

        return $query->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now);
    }

    public function scopeForBranch($query, ?int $branchId)
    {
        return $query->where(function ($q) use ($branchId): void {
            $q->whereNull('branch_id');

            if ($branchId !== null) {
                $q->orWhere('branch_id', $branchId);
            }
        });
    }
}
