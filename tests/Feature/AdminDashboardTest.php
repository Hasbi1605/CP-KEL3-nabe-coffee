<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    public function test_dashboard_owner_menampilkan_menu_owner_dan_empat_cabang(): void
    {
        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Owner Demo')
            ->assertSee('Analitik')
            ->assertSee('Nabe Coffee Kusumanegara')
            ->assertSee('Nabe Coffee Batikan');
    }

    public function test_dashboard_admin_cabang_menampilkan_menu_dan_satu_cabang(): void
    {
        $this->get('/admin/dashboard?role=branch')
            ->assertOk()
            ->assertSee('Admin Cabang Demo')
            ->assertSee('Reservasi Hari Ini')
            ->assertDontSee('Analitik')
            ->assertSee('Nabe Coffee Kusumanegara')
            ->assertDontSee('Nabe Coffee Batikan');
    }

    public function test_tautan_dashboard_mempertahankan_mode_admin_cabang(): void
    {
        $url = route('admin.dashboard', ['role' => 'branch']);

        $this->get('/admin/dashboard?role=branch')
            ->assertOk()
            ->assertSee('href="'.$url.'"', false);
    }

    public function test_tautan_dashboard_mode_owner_tanpa_parameter_demo(): void
    {
        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('href="'.route('admin.dashboard').'"', false)
            ->assertDontSee('role=branch', false);
    }

    public function test_identitas_user_dan_cabang_tersedia_di_layout(): void
    {
        $this->get('/admin/dashboard?role=branch')
            ->assertOk()
            ->assertSee('Admin Cabang Demo')
            ->assertSee('Nabe Coffee Kusumanegara');
    }

    public function test_sidebar_punya_tombol_buka_dan_tutup_untuk_layar_kecil(): void
    {
        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('id="sidebar-open"', false)
            ->assertSee('id="sidebar-close"', false)
            ->assertSee('inert', false);
    }
}
