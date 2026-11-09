<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Sejak tugas 3.4 katalog membaca database beneran, bukan array
        // dummy lagi. Tes UI lama Riq disesuaikan: seed menu yang sama
        // namanya dengan dummy lama agar aser tampilannya tetap terpenuhi.
        $branch = Branch::factory()->create(['name' => 'Nabe Coffee Kusumanegara']);
        Menu::factory()->create(['branch_id' => $branch->id, 'name' => 'Eskopsu Lait', 'category' => 'coffee', 'price' => 13000, 'is_available' => true]);
        Menu::factory()->create(['branch_id' => $branch->id, 'name' => 'Matcha Latte', 'category' => 'non_coffee', 'price' => 25000, 'is_available' => true]);
    }

    public function test_menu_catalog_page_returns_successful_response(): void
    {
        $response = $this->get('/menu');

        $response->assertStatus(200);
    }

    public function test_menu_catalog_page_displays_hero_text_and_menus(): void
    {
        $response = $this->get('/menu');

        $response->assertStatus(200);
        $response->assertSee('GOOD MOOD, GREAT MENU.');
        $response->assertSee('Eskopsu Lait');
        $response->assertSee('Matcha Latte');
    }

    public function test_menu_catalog_page_uses_public_layout(): void
    {
        $response = $this->get('/menu');

        $response->assertStatus(200);
        $response->assertSee('GOOD COFFEE. GOOD COMPANY.');
        $response->assertSee('Beranda');
        $response->assertSee('HUBUNGI KAMI');
        $response->assertSee('JELAJAH');
    }
}
