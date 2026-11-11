<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PromotionRequest;
use App\Models\Branch;
use App\Models\Promotion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PromotionController extends Controller
{
    /**
     * Pagar owner untuk semua aksi CMS. Middleware `role:owner` dari
     * tugas 1.3 menjadi lapisan utama setelah PR itu merged; cek ini
     * memastikan branch_admin tetap tertolak (403) walau alias
     * middleware belum terdaftar.
     */
    private function ensureOwner(Request $request): void
    {
        abort_unless($request->user()?->isOwner() === true, 403, 'Lu nggak punya akses ke halaman ini.');
    }

    public function index(Request $request): View
    {
        $this->ensureOwner($request);
        $promotions = Promotion::query()
            ->with('branch')
            ->orderByDesc('starts_at')
            ->paginate(15);

        return view('admin.promotions.index', compact('promotions'));
    }

    public function create(Request $request): View
    {
        $this->ensureOwner($request);
        $branches = Branch::query()->orderBy('name')->get();

        return view('admin.promotions.create', compact('branches'));
    }

    public function store(PromotionRequest $request): RedirectResponse
    {
        $this->ensureOwner($request);
        $data = $request->promotionData();

        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('promotions', 'public');

            if ($bannerPath === false) {
                return back()->withErrors(['banner' => 'Banner promo gagal diunggah. Silakan coba lagi.'])->withInput();
            }

            $data['banner'] = $bannerPath;
        }

        $promotion = Promotion::create($data);

        return redirect()->route('admin.promotions.show', $promotion)
            ->with('status', 'Promo berhasil ditambahkan.');
    }

    public function show(Request $request, Promotion $promotion): View
    {
        $this->ensureOwner($request);
        $promotion->load('branch');

        return view('admin.promotions.show', compact('promotion'));
    }

    public function edit(Request $request, Promotion $promotion): View
    {
        $this->ensureOwner($request);
        $branches = Branch::query()->orderBy('name')->get();

        return view('admin.promotions.edit', compact('promotion', 'branches'));
    }

    public function update(PromotionRequest $request, Promotion $promotion): RedirectResponse
    {
        $this->ensureOwner($request);
        $data = $request->promotionData();
        $oldBanner = $promotion->banner;
        $newBannerPath = null;

        if ($request->hasFile('banner')) {
            $newBannerPath = $request->file('banner')->store('promotions', 'public');

            if ($newBannerPath === false) {
                return back()->withErrors(['banner' => 'Banner promo gagal diunggah. Silakan coba lagi.'])->withInput();
            }

            $data['banner'] = $newBannerPath;
        } elseif ($request->boolean('remove_banner')) {
            $data['banner'] = null;
        }

        try {
            $promotion->update($data);
        } catch (\Throwable $exception) {
            if ($newBannerPath !== null) {
                Storage::disk('public')->delete($newBannerPath);
            }

            throw $exception;
        }

        if (($newBannerPath !== null || ($request->boolean('remove_banner') && $oldBanner !== null)) && $oldBanner !== null) {
            Storage::disk('public')->delete($oldBanner);
        }

        return redirect()->route('admin.promotions.show', $promotion)
            ->with('status', 'Promo berhasil diperbarui.');
    }

    public function destroy(Request $request, Promotion $promotion): RedirectResponse
    {
        $this->ensureOwner($request);
        abort_unless($promotion->delete(), 500, 'Promo gagal dihapus.');

        if ($promotion->banner !== null) {
            Storage::disk('public')->delete($promotion->banner);
        }

        return redirect()->route('admin.promotions.index')
            ->with('status', 'Promo berhasil dihapus.');
    }
}
