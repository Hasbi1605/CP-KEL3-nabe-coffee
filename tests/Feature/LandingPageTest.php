<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_renders_all_required_company_profile_sections(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // 1. Hero Section
        $response->assertSee('LIGHT UP YOUR DAY', false);
        $response->assertSee('with Nabe.', false);
        $response->assertSee('YOUR EVERYDAY HAPPY PLACE &middot; YOGYAKARTA', false);
        $response->assertSee('Reservasi Sekarang', false);
        $response->assertSee('href="#outlet"', false);
        $response->assertSee('hidden lg:flex flex-col items-center justify-center hero-sticker', false);

        // 2. Info Strip
        $response->assertSee('4 Cabang di Jogja', false);
        $response->assertSee('07.00 &ndash; 00.00 WIB', false);

        // 3. Cerita Brand & Visi Misi
        $response->assertSee('BUKAN CUMA KOPI.<br />', false);
        $response->assertSee('INI TEMPAT KITA.', false);
        $response->assertSee('Majja Decospace &middot; Jogja Padel &middot; MEandPartners', false);
        $response->assertSee('A place to belong.', false);
        $response->assertSee('Make good days happen.', false);

        // 4. Galeri Suasana
        $response->assertSee('OUR LITTLE CORNERS', false);
        $response->assertSee('THE EVERYDAY, BUT BETTER.', false);

        // 5. Section Promo
        $response->assertSee('PROMO &amp; SPESIAL BULAN INI', false);
        $response->assertSee('Belum ada promo yang aktif saat ini.');
        $response->assertDontSee('Pratinjau Konsep');
        $response->assertDontSee('Contoh Promo');
        $response->assertDontSee('Morning Fuel Combo');
        $response->assertDontSee('Group Table Gathering');

        // 6. Section 4 Cabang
        $response->assertSee('id="outlet"', false);
        $response->assertSee('INFO CABANG NABE COFFEE.', false); // judul default CMS 3.4 (sebelumnya FIND YOUR NABE.)
        $response->assertSee('Kusumanegara', false);
        $response->assertSee('Demangan', false);
        $response->assertSee('Soragan', false);
        $response->assertSee('Batikan (THE PADEL)', false);
        $response->assertSee('WhatsApp segera hadir', false);
        $response->assertDontSee('6281234567890');
    }
}
