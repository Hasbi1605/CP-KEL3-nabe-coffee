<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MenuRequest;
use App\Models\Branch;
use App\Models\Menu;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(): View
    {
        $menus = Menu::query()->with('branch')->orderBy('name')->paginate(15);

        return view('admin.menus.index', compact('menus'));
    }

    public function create(): View
    {
        $branches = Branch::query()->orderBy('name')->get();

        return view('admin.menus.create', compact('branches'));
    }

    public function store(MenuRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        // Checkbox yang tidak dicentang tidak mengirim field; itu berarti
        // tidak tersedia, bukan tersedia (default form: hidden input 0).
        $data['is_available'] = $request->boolean('is_available');

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('menus', 'public');

            // Disk public memakai 'throw' => false, jadi kegagalan tulis
            // mengembalikan false, bukan exception. Tolak sebelum record
            // dibuat agar photo tidak tersimpan sebagai "0".
            if ($photoPath === false) {
                return redirect()->back()
                    ->withErrors(['photo' => 'Foto menu gagal diunggah. Silakan coba lagi.'])
                    ->withInput();
            }

            $data['photo'] = $photoPath;
        }

        $menu = Menu::create($data);

        return redirect()
            ->route('admin.menus.show', $menu)
            ->with('success', 'Menu berhasil ditambahkan.');
    }

    public function show(Menu $menu): View
    {
        $menu->load('branch');

        return view('admin.menus.show', compact('menu'));
    }

    public function edit(Menu $menu): View
    {
        $branches = Branch::query()->orderBy('name')->get();

        return view('admin.menus.edit', compact('menu', 'branches'));
    }

    public function update(MenuRequest $request, Menu $menu): RedirectResponse
    {
        $data = $request->validated();
        $data['is_best_seller'] = $request->boolean('is_best_seller');
        $data['is_available'] = $request->boolean('is_available');

        $oldPhoto = $menu->photo;
        $newPhotoPath = null;

        if ($request->hasFile('photo')) {
            // Simpan foto baru dulu. Foto lama baru dihapus setelah
            // record berhasil diperbarui, supaya tidak hilang saat
            // penyimpanan foto atau database gagal.
            $newPhotoPath = $request->file('photo')->store('menus', 'public');

            // Disk public memakai 'throw' => false, jadi kegagalan tulis
            // mengembalikan false, bukan exception. Tolak sebelum record
            // diubah atau foto lama dihapus, agar photo tidak tersimpan
            // sebagai "0" dan file lama tetap utuh.
            if ($newPhotoPath === false) {
                return redirect()->back()
                    ->withErrors(['photo' => 'Foto menu gagal diunggah. Silakan coba lagi.'])
                    ->withInput();
            }

            $data['photo'] = $newPhotoPath;
        }

        try {
            $menu->update($data);
        } catch (\Throwable $exception) {
            // Record gagal diperbarui: bersihkan foto baru yang
            // sempat terunggah, foto lama tetap utuh.
            if ($newPhotoPath !== null) {
                Storage::disk('public')->delete($newPhotoPath);
            }

            throw $exception;
        }

        if ($newPhotoPath !== null && $oldPhoto !== null) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return redirect()
            ->route('admin.menus.show', $menu)
            ->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu): RedirectResponse
    {
        if ($menu->photo !== null) {
            Storage::disk('public')->delete($menu->photo);
        }

        $menu->delete();

        return redirect()
            ->route('admin.menus.index')
            ->with('success', 'Menu berhasil dihapus.');
    }
}
