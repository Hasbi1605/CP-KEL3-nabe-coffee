<?php

namespace App\Http\Controllers\Menu;

use App\Enums\MenuCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Menu\MenuCatalogRequest;
use App\Services\Menu\MenuCatalogService;
use Illuminate\View\View;

class MenuCatalogController extends Controller
{
    public function __construct(private MenuCatalogService $catalog) {}

    public function index(MenuCatalogRequest $request): View
    {
        $filters = $request->filters();
        $branches = $this->catalog->branches();

        return view('menu.index', [
            'branches' => $branches->pluck('name')->all(),
            'branchModels' => $branches,
            'menus' => $this->catalog->catalog($filters)->all(),
            'categories' => MenuCategory::options(),
            'filters' => $filters,
        ]);
    }
}
