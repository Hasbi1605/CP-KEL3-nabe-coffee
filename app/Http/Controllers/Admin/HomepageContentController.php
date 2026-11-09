<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HomepageContentRequest;
use App\Services\Menu\HomepageContentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageContentController extends Controller
{
    public function __construct(private HomepageContentService $content) {}

    public function edit(Request $request): View
    {
        abort_unless($request->user()?->isOwner() === true, 403, 'Lu nggak punya akses ke halaman ini.');

        return view('admin.homepage-content.edit', [
            'content' => $this->content->all(),
        ]);
    }

    public function update(HomepageContentRequest $request): RedirectResponse
    {
        abort_unless($request->user()?->isOwner() === true, 403, 'Lu nggak punya akses ke halaman ini.');
        $this->content->update($request->input('content', []));

        return redirect()->route('admin.homepage-content.edit')
            ->with('status', 'Teks info outlet & promo halaman utama berhasil diperbarui.');
    }
}
