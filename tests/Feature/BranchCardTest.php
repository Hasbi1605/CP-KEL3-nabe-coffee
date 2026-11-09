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

        // Memastikan atribut class pada kartu mode daftar dikutip dengan benar (Poin 1)
        $view->assertSee('class="outlet-detail bg-white border border-line rounded-[5px] overflow-hidden shadow-xs grid grid-cols-1 lg:grid-cols-2"', false);

        // Memastikan alamat cabang muncul
        $view->assertSee('Jl. Kusumanegara No.11');
        $view->assertSee('Jl. Demangan Baru');
        $view->assertSee('Jl. Soragan No.29');
        $view->assertSee('Jl. Batikan No.9');

        // Memastikan jam operasional muncul
        $view->assertSee('07.00–00.00');
        $view->assertSee('09.00–22.00');

        // Pada data default (belum ada no WA resmi di PRD), tombol WA nonaktif & tidak ada link wa.me (Poin 2)
        $view->assertSee('WhatsApp Belum Dikonfigurasi');
        $view->assertSee('aria-disabled="true"', false);
        $view->assertDontSee('https://wa.me/', false);

        // Memastikan penanda "konfirmasi" dan catatan fasilitas PRD muncul pada data belum terverifikasi (Poin 3)
        $view->assertSee('WiFi · konfirmasi');
        $view->assertSee('Colokan · konfirmasi');
        $view->assertSee('Meeting room · konfirmasi');
        $view->assertSee('Parkir & musholla · konfirmasi');
        $view->assertSee('Area padel · THE PADEL');
        $view->assertSee('Detail ketersediaan fasilitas selain area padel belum diberikan dalam PRD.');

        // Memastikan tidak ada focus-visible:outline-none agar outline global aktif (Poin 4)
        $view->assertDontSee('focus-visible:outline-none');

        // Memastikan hanya cabang yang eksplisit is_main = true yang berlabel "Cabang Utama" (Poin 5)
        $renderedHtml = (string) $view;
        $this->assertSame(1, substr_count($renderedHtml, 'Cabang Utama'));

        // Memastikan iframe Google Maps embed ada untuk setiap cabang (tanpa API key)
        $view->assertSee('<iframe', false);
        $view->assertSee('maps.google.com/maps?q=', false);
        $view->assertSee('output=embed', false);
    }

    public function test_branch_card_component_renders_single_branch_with_active_whatsapp(): void
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

        // Tombol WhatsApp aktif saat nomor valid diberikan (Poin 2)
        $view->assertSee('https://wa.me/628987654321', false);
        $view->assertSee('Hubungi WhatsApp');
        $view->assertDontSee('WhatsApp Belum Dikonfigurasi');

        // Fasilitas terverifikasi tidak memunculkan catatan unconfirmed PRD (Poin 3)
        $view->assertSee('WiFi');
        $view->assertSee('Outdoor area');
        $view->assertDontSee('Detail ketersediaan fasilitas selain area padel belum diberikan dalam PRD.');

        // Memastikan cabang lain tidak ikut ter-render pada mode single
        $view->assertDontSee('Nabe Coffee Demangan');
    }

    public function test_single_branch_without_is_main_does_not_show_cabang_utama(): void
    {
        // Kasus review Hasbi: objek Soragan yang dirender sendirian tanpa is_main tidak boleh berlabel Cabang Utama
        $soragan = [
            'name' => 'Nabe Coffee Soragan',
            'address' => 'Jl. Soragan No.29, Soragan, Ngestiharjo, Kasihan, Bantul 55184',
            'hours' => '07.00–00.00',
            'admin_whatsapp' => null,
        ];

        $view = $this->blade('<x-branch-card :branch="$branch" />', ['branch' => $soragan]);

        $view->assertSee('Nabe Coffee Soragan');
        $view->assertDontSee('Cabang Utama'); // (Poin 5)
        $view->assertSee('WhatsApp Belum Dikonfigurasi'); // (Poin 2)
        $view->assertDontSee('https://wa.me/', false);
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
            'is_main' => false,
        ];

        $view = $this->blade('<x-branch-card :branch="$branch" />', ['branch' => $branchObj]);

        $view->assertSee('Nabe Coffee Soragan');
        $view->assertSee('Jl. Soragan No.29, Bantul');
        $view->assertSee('07.00–23.00');
        $view->assertSee('https://wa.me/628123456003', false);
        $view->assertSee('https://maps.google.com/?cid=12345', false);
        $view->assertDontSee('Cabang Utama');
    }
}
