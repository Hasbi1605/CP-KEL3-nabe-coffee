<?php

namespace Tests\Feature;

use Tests\TestCase;

class MenuCatalogTest extends TestCase
{
    public function test_menu_catalog_page_returns_successful_response(): void
    {
        $response = $this->get('/menu');

        $response->assertStatus(200);
    }

    public function test_menu_catalog_page_displays_hero_text_and_dummy_menus(): void
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
