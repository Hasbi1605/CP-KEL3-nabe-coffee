<?php

namespace Tests\Feature\Menu;

use App\Models\Branch;
use App\Models\Menu;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuCatalogBackendTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_reads_menus_from_database_not_dummy(): void
    {
        $branch = Branch::factory()->create(['name' => 'Nabe Coffee Kusumanegara']);
        $menu = Menu::factory()->create([
            'branch_id' => $branch->id,
            'name' => 'Menu Dari Database Unik',
            'category' => 'coffee',
            'price' => 17000,
            'is_available' => true,
            'is_best_seller' => true,
        ]);
        Menu::factory()->create([
            'branch_id' => $branch->id,
            'name' => 'Menu Tidak Tersedia Unik',
            'category' => 'coffee',
            'price' => 17000,
            'is_available' => false,
        ]);

        $this->assertSame('coffee', $menu->category);
        $this->assertSame('Kopi', $menu->toCatalogArray()['category']);

        $this->get('/menu')
            ->assertOk()
            ->assertSee('Menu Dari Database Unik')
            ->assertSee('Terlaris')
            ->assertDontSee('Menu Tidak Tersedia Unik')
            ->assertDontSee('Eskopsu Lait');
    }

    public function test_catalog_filters_by_category_price_branch_and_search(): void
    {
        $branchA = Branch::factory()->create(['name' => 'Cabang A']);
        $branchB = Branch::factory()->create(['name' => 'Cabang B']);

        Menu::factory()->create(['branch_id' => $branchA->id, 'name' => 'Kopi Murah A', 'category' => 'coffee', 'price' => 12000]);
        Menu::factory()->create(['branch_id' => $branchA->id, 'name' => 'Kopi Mahal A', 'category' => 'coffee', 'price' => 45000]);
        Menu::factory()->create(['branch_id' => $branchA->id, 'name' => 'Camilan A', 'category' => 'snack', 'price' => 15000]);
        Menu::factory()->create(['branch_id' => $branchB->id, 'name' => 'Kopi Cabang B', 'category' => 'coffee', 'price' => 13000]);

        $this->get('/menu?category=coffee&max_price=20000&branch_id='.$branchA->id)
            ->assertOk()
            ->assertSee('Kopi Murah A')
            ->assertDontSee('Kopi Mahal A')
            ->assertDontSee('Camilan A')
            ->assertDontSee('Kopi Cabang B');

        $this->get('/menu?search='.urlencode('Camilan A'))
            ->assertOk()
            ->assertSee('Camilan A')
            ->assertDontSee('Kopi Murah A');
    }

    public function test_catalog_marks_promo_when_active_promotion_exists(): void
    {
        $branch = Branch::factory()->create();
        Menu::factory()->create(['branch_id' => $branch->id, 'name' => 'Menu Promo Unik', 'category' => 'coffee']);
        Promotion::factory()->create([
            'branch_id' => null,
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        $this->get('/menu')
            ->assertOk()
            ->assertSee('Menu Promo Unik')
            ->assertSee('Promo');

        // Promo yang sudah lewat tidak menandai menu.
        Promotion::query()->delete();
        Promotion::factory()->create([
            'branch_id' => null,
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->subDay(),
        ]);

        $this->get('/menu')->assertOk()->assertDontSee('>Promo<', false);
    }

    public function test_catalog_rejects_invalid_filter_values(): void
    {
        Branch::factory()->create();

        $this->get('/menu?category=kategori-ngawur')->assertSessionHasErrors('category');
        $this->get('/menu?branch_id=999999')->assertSessionHasErrors('branch_id');
    }
}
