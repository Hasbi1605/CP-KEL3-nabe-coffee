<?php

namespace Tests\Feature;

use Tests\TestCase;

class BranchCardTest extends TestCase
{
    public function test_branch_card_component_renders_all_default_branches(): void
    {
        $view = $this->blade('<x-branch-card />');

        // Memastikan ke-4 nama cabang dari NabeUI.tsx / PRD ada
        $view->assertSee('Nabe Coffee Kusumanegara');
        $view->assertSee('Nabe Coffee Demangan');
        $view->assertSee('Nabe Coffee Soragan');
        $view->assertSee('Nabe Coffee Batikan (THE PADEL)');

        // Memastikan alamat cabang muncul
        $view->assertSee('Jl. Kusumanegara No.11');
        $view->assertSee('Jl. Demangan Baru');
        $view->assertSee('Jl. Soragan No.29');
        $view->assertSee('Jl. Batikan No.9');

        // Memastikan jam operasional muncul
        $view->assertSee('07.00–00.00');
        $view->assertSee('09.00–22.00');

        // Memastikan tombol WhatsApp (wa.me) ada
        $view->assertSee('https://wa.me/628123456001', false);
        $view->assertSee('https://wa.me/628123456002', false);
        $view->assertSee('https://wa.me/628123456003', false);
        $view->assertSee('https://wa.me/628123456004', false);
        $view->assertSee('Hubungi WhatsApp');

        // Memastikan iframe Google Maps embed ada untuk setiap cabang (tanpa API key)
        $view->assertSee('<iframe', false);
        $view->assertSee('maps.google.com/maps?q=', false);
        $view->assertSee('output=embed', false);
    }

    public function test_branch_card_component_renders_single_branch(): void
    {
        $singleBranch = [
            'name' => 'Nabe Coffee Malioboro',
            'address' => 'Jl. Malioboro No. 50, Yogyakarta',
            'hours' => '08.00–23.00',
            'admin_whatsapp' => '08987654321',
            'facilities' => ['WiFi', 'Outdoor area'],
        ];

        $view = $this->blade('<x-branch-card :branch="$branch" />', ['branch' => $singleBranch]);

        $view->assertSee('Nabe Coffee Malioboro');
        $view->assertSee('Jl. Malioboro No. 50, Yogyakarta');
        $view->assertSee('08.00–23.00');
        $view->assertSee('https://wa.me/628987654321', false);
        $view->assertSee('Outdoor area');
        $view->assertSee('maps.google.com/maps?q=', false);

        // Memastikan cabang lain tidak ikut ter-render pada mode single
        $view->assertDontSee('Nabe Coffee Demangan');
    }

    public function test_branch_card_component_supports_eloquent_style_object(): void
    {
        $branchObj = (object) [
            'name' => 'Soragan',
            'address' => 'Jl. Soragan No.29, Bantul',
            'opens_at' => '07:00:00',
            'closes_at' => '23:00:00',
            'admin_whatsapp' => '08123456003',
            'maps_url' => 'https://maps.google.com/?cid=12345',
        ];

        $view = $this->blade('<x-branch-card :branch="$branch" />', ['branch' => $branchObj]);

        $view->assertSee('Nabe Coffee Soragan');
        $view->assertSee('Jl. Soragan No.29, Bantul');
        $view->assertSee('07.00–23.00');
        $view->assertSee('https://wa.me/628123456003', false);
        $view->assertSee('https://maps.google.com/?cid=12345', false);
    }
}
