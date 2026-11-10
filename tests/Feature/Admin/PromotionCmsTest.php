<?php

namespace Tests\Feature\Admin;

use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\Branch;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PromotionCmsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Buat user dengan 2FA terkonfirmasi tanpa bergantung pada urutan
     * merge PR 1.5: kolom rahasia diisi nilai acak terenkripsi lewat
     * cast model, dan flag sesi `two_factor.passed` disetel seperti
     * yang dilakukan challenge 1.5 setelah kode valid.
     */
    private function userWithTwoFactor(string $role): User
    {
        $factory = $role === 'owner'
            ? User::factory()->owner()
            : User::factory()->branchAdmin();

        $user = $factory->create([
            'two_factor_secret' => 'secret-untuk-tes-3.4',
            'two_factor_recovery_codes' => ['AAAA-1111', 'BBBB-2222'],
            'two_factor_confirmed_at' => now(),
        ]);
        session(['two_factor.passed' => true]);

        return $user;
    }

    private function verifiedOwner(): User
    {
        return $this->userWithTwoFactor('owner');
    }

    private function verifiedBranchAdmin(): User
    {
        return $this->userWithTwoFactor('branch_admin');
    }

    public function test_owner_can_crud_promotion_with_banner_upload(): void
    {
        Storage::fake('public');
        $owner = $this->verifiedOwner();
        $branch = Branch::factory()->create();

        $response = $this->actingAs($owner)->post(route('admin.promotions.store'), [
            'title' => 'Promo Kopi Pagi',
            'description' => 'Diskon untuk semua kopi pagi.',
            'starts_at' => now()->subDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addWeek()->format('Y-m-d H:i:s'),
            'terms' => 'Syarat berlaku.',
            'branch_id' => $branch->id,
            'banner' => UploadedFile::fake()->image('banner.jpg'),
        ]);

        $promotion = Promotion::first();
        $this->assertNotNull($promotion);
        $response->assertRedirect(route('admin.promotions.show', $promotion));
        $this->assertSame('Promo Kopi Pagi', $promotion->title);
        $this->assertNotNull($promotion->banner);
        Storage::disk('public')->assertExists($promotion->banner);

        $this->get(route('admin.promotions.index'))
            ->assertOk()
            ->assertSee('Promo Kopi Pagi');

        $this->put(route('admin.promotions.update', $promotion), [
            'title' => 'Promo Kopi Pagi Diubah',
            'description' => 'Deskripsi baru.',
            'starts_at' => now()->subDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->addWeek()->format('Y-m-d H:i:s'),
            'branch_id' => '',
        ])->assertRedirect(route('admin.promotions.show', $promotion));

        $promotion->refresh();
        $this->assertSame('Promo Kopi Pagi Diubah', $promotion->title);
        $this->assertNull($promotion->branch_id);

        $this->delete(route('admin.promotions.destroy', $promotion))
            ->assertRedirect(route('admin.promotions.index'));
        $this->assertDatabaseCount('promotions', 0);
        Storage::disk('public')->assertMissing($promotion->banner);
    }

    public function test_promotion_validation_requires_valid_period(): void
    {
        $owner = $this->verifiedOwner();

        $this->actingAs($owner)->post(route('admin.promotions.store'), [
            'title' => '',
            'starts_at' => 'bukan-tanggal',
            'ends_at' => now()->subDay()->format('Y-m-d H:i:s'),
        ])->assertSessionHasErrors(['title', 'starts_at']);

        $this->post(route('admin.promotions.store'), [
            'title' => 'Promo Salah Periode',
            'starts_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'ends_at' => now()->format('Y-m-d H:i:s'),
        ])->assertSessionHasErrors('ends_at');

        $this->assertDatabaseCount('promotions', 0);
    }

    public function test_branch_admin_and_guest_cannot_access_promotion_cms(): void
    {
        $promotion = Promotion::factory()->create();

        $this->get(route('admin.promotions.index'))->assertRedirect(route('login'));

        $admin = $this->verifiedBranchAdmin();
        $this->actingAs($admin)->get(route('admin.promotions.index'))->assertForbidden();
        $this->actingAs($admin)->post(route('admin.promotions.store'), [
            'title' => 'Ilegal', 'starts_at' => now(), 'ends_at' => now()->addDay(),
        ])->assertForbidden();
        $this->actingAs($admin)->get(route('admin.promotions.edit', $promotion))->assertForbidden();

        $this->assertDatabaseCount('promotions', 1);
    }

    public function test_owner_must_pass_two_factor_before_cms(): void
    {
        if (! class_exists(EnsureTwoFactorVerified::class)) {
            $this->markTestSkipped('Middleware 2FA dari tugas 1.5 belum ada di branch ini.');
        }

        $owner = User::factory()->owner()->create([
            'two_factor_secret' => 'secret-untuk-tes-3.4',
            'two_factor_recovery_codes' => ['AAAA-1111'],
            'two_factor_confirmed_at' => now(),
        ]);

        // Belum lolos challenge -> dilempar ke halaman challenge, bukan CMS.
        $this->actingAs($owner)->get(route('admin.promotions.index'))
            ->assertRedirect(route('two-factor.login'));
    }

    public function test_owner_can_update_homepage_outlet_and_promo_text(): void
    {
        Storage::fake('local');
        $owner = $this->verifiedOwner();

        $this->actingAs($owner)->put(route('admin.homepage-content.update'), [
            'content' => [
                'outlet_info_title' => 'JUDUL OUTLET DARI CMS.',
                'outlet_info_text' => 'Teks outlet hasil CMS.',
                'promo_title' => 'PROMO DARI CMS.',
                'promo_text' => 'Teks promo hasil CMS.',
            ],
        ])->assertRedirect(route('admin.homepage-content.edit'))
            ->assertSessionHas('status');

        $this->get('/')
            ->assertOk()
            ->assertSee('JUDUL OUTLET DARI CMS.')
            ->assertSee('Teks outlet hasil CMS.')
            ->assertSee('PROMO DARI CMS.')
            ->assertSee('Teks promo hasil CMS.');
    }

    public function test_homepage_shows_default_text_without_cms_file(): void
    {
        Storage::fake('local');

        $this->get('/')
            ->assertOk()
            ->assertSee('INFO CABANG NABE COFFEE.')
            ->assertSee('PENAWARAN HARI BAIKMU.');
    }

    public function test_homepage_only_shows_active_promotions(): void
    {
        $branch = Branch::factory()->create(['name' => 'Cabang Istimewa']);
        Promotion::factory()->create([
            'title' => 'Promo <Aktif>', 'description' => 'Deskripsi <promo>', 'terms' => 'Syarat <khusus>',
            'starts_at' => now()->subDay(), 'ends_at' => now()->addDay(),
            'branch_id' => $branch->id, 'banner' => 'promotions/banner.jpg',
        ]);
        Promotion::factory()->create(['title' => 'Promo Mendatang', 'starts_at' => now()->addDay(), 'ends_at' => now()->addDays(2)]);
        Promotion::factory()->create(['title' => 'Promo Berakhir', 'starts_at' => now()->subDays(2), 'ends_at' => now()->subDay()]);

        $this->get('/')->assertOk()
            ->assertSee('Promo <Aktif>')->assertSee('Deskripsi <promo>')->assertSee('Syarat <khusus>')
            ->assertSee('Cabang Istimewa')->assertSee('/storage/promotions/banner.jpg')
            ->assertSee(now()->subDay()->format('d/m/Y H:i'))->assertSee(now()->addDay()->format('d/m/Y H:i'))
            ->assertDontSee('Promo Mendatang')->assertDontSee('Promo Berakhir')
            ->assertDontSee('Morning Fuel Combo')->assertDontSee('Pratinjau Konsep')
            ->assertDontSee('Belum ada promo yang aktif saat ini.');
    }

    public function test_homepage_shows_empty_promotion_state(): void
    {
        $this->get('/')->assertOk()->assertSee('Belum ada promo yang aktif saat ini.')
            ->assertDontSee('Morning Fuel Combo')->assertDontSee('Pratinjau Konsep');
    }

    public function test_failed_homepage_write_preserves_previous_content_and_input(): void
    {
        $disk = Storage::fake('local');
        $disk->put('cms/homepage-content.json', '{"promo_title":"Judul lama"}');
        $owner = $this->verifiedOwner();
        $failingDisk = \Mockery::mock();
        $failingDisk->shouldReceive('put')->once()->andReturnFalse();
        $failingDisk->shouldReceive('delete')->once()->andReturnTrue();
        Storage::shouldReceive('disk')->with('local')->andReturn($failingDisk);

        $this->actingAs($owner)->from(route('admin.homepage-content.edit'))
            ->put(route('admin.homepage-content.update'), ['content' => ['promo_title' => 'Judul baru']])
            ->assertRedirect(route('admin.homepage-content.edit'))
            ->assertSessionHasErrors('content')->assertSessionHasInput('content.promo_title', 'Judul baru')
            ->assertSessionMissing('status');
        $this->assertSame('{"promo_title":"Judul lama"}', $disk->get('cms/homepage-content.json'));
    }

    public function test_failed_homepage_replacement_preserves_previous_content_and_cleans_temporary_file(): void
    {
        $disk = Storage::fake('local');
        $disk->put('cms/homepage-content.json', '{"promo_title":"Judul lama"}');
        $owner = $this->verifiedOwner();
        $failingDisk = \Mockery::mock($disk);
        $failingDisk->shouldReceive('move')->once()->andReturnFalse();
        Storage::shouldReceive('disk')->with('local')->andReturn($failingDisk);

        $this->actingAs($owner)->from(route('admin.homepage-content.edit'))
            ->put(route('admin.homepage-content.update'), ['content' => ['promo_title' => 'Judul baru']])
            ->assertRedirect(route('admin.homepage-content.edit'))
            ->assertSessionHasErrors('content')->assertSessionHasInput('content.promo_title', 'Judul baru')
            ->assertSessionMissing('status');

        $this->assertSame('{"promo_title":"Judul lama"}', $disk->get('cms/homepage-content.json'));
        $this->assertSame(['cms/homepage-content.json'], $disk->allFiles('cms'));
    }

    public function test_failed_promotion_delete_preserves_record_and_banner(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('promotions/banner.jpg', 'banner');
        $promotion = Promotion::factory()->create(['banner' => 'promotions/banner.jpg']);
        $owner = $this->verifiedOwner();
        Promotion::deleting(fn () => throw new \RuntimeException('Gagal menghapus promo'));

        try {
            $this->actingAs($owner)->delete(route('admin.promotions.destroy', $promotion))->assertServerError();
            $this->assertDatabaseHas('promotions', ['id' => $promotion->id]);
            Storage::disk('public')->assertExists('promotions/banner.jpg');
        } finally {
            Promotion::flushEventListeners();
        }
    }

    public function test_canceled_promotion_delete_preserves_record_and_banner(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('promotions/banner.jpg', 'banner');
        $promotion = Promotion::factory()->create(['banner' => 'promotions/banner.jpg']);
        $owner = $this->verifiedOwner();
        Promotion::deleting(fn () => false);

        try {
            $this->actingAs($owner)->delete(route('admin.promotions.destroy', $promotion))
                ->assertServerError()->assertSessionMissing('status');
            $this->assertDatabaseHas('promotions', ['id' => $promotion->id]);
            Storage::disk('public')->assertExists('promotions/banner.jpg');
        } finally {
            Promotion::flushEventListeners();
        }
    }
}
