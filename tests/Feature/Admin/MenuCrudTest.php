<?php

namespace Tests\Feature\Admin;

use App\Models\Branch;
use App\Models\Menu;
use App\Models\User;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MenuCrudTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::factory()->owner()->create();
    }

    private function branchAdmin(): User
    {
        return User::factory()->branchAdmin()->create();
    }

    /** @return array<string, mixed> */
    private function validData(int $branchId): array
    {
        return [
            'branch_id' => $branchId,
            'name' => 'Es Kopi Nabe',
            'category' => 'coffee',
            'price' => 25000,
            'tags' => 'kopi, es',
            'is_best_seller' => false,
            'is_available' => true,
        ];
    }

    /**
     * Ganti disk public dengan adapter yang selalu gagal menulis, untuk
     * mensimulasikan store() yang mengembalikan false (disk memakai
     * 'throw' => false, jadi gagal tulis tidak melempar exception).
     * Baca dan hapus file tetap memakai driver asli di atas fake disk.
     */
    private function diskGagalSimpanFoto(): void
    {
        $real = Storage::disk('public');

        $failing = new class($real->getDriver(), $real->getAdapter(), $real->getConfig()) extends FilesystemAdapter
        {
            public function putFile($path, $file = null, $options = [])
            {
                return false;
            }

            public function putFileAs($path, $file, $name = null, $options = [])
            {
                return false;
            }
        };

        Storage::set('public', $failing);
    }

    public function test_owner_dapat_membuat_menu_dengan_upload_foto(): void
    {
        Storage::fake('public');
        $branch = Branch::factory()->create();

        $response = $this->actingAs($this->owner())
            ->post(route('admin.menus.store'), array_merge($this->validData($branch->id), [
                'photo' => UploadedFile::fake()->image('es-kopi.jpg'),
            ]));

        $response->assertRedirect();
        $menu = Menu::first();
        $this->assertNotNull($menu);
        $this->assertSame('Es Kopi Nabe', $menu->name);
        $this->assertSame('coffee', $menu->category);
        // Tag dikirim sebagai teks dipisah koma, disimpan sebagai JSON (array).
        $this->assertSame(['kopi', 'es'], $menu->tags);
        $this->assertNotNull($menu->photo);
        Storage::disk('public')->assertExists($menu->photo);
    }

    public function test_owner_dapat_melihat_daftar_dan_detail_menu(): void
    {
        $menu = Menu::factory()->create(['name' => 'Kopi Susu Gula Aren']);

        $this->actingAs($this->owner())->get(route('admin.menus.index'))
            ->assertOk()->assertSee('Kopi Susu Gula Aren');

        $this->actingAs($this->owner())->get(route('admin.menus.show', $menu))
            ->assertOk()->assertSee('Kopi Susu Gula Aren');
    }

    public function test_owner_dapat_mengubah_menu_dan_foto_lama_dihapus(): void
    {
        Storage::fake('public');
        $menu = Menu::factory()->create([
            'photo' => UploadedFile::fake()->image('lama.jpg')->store('menus', 'public'),
        ]);

        $oldPhoto = $menu->photo;

        $response = $this->actingAs($this->owner())
            ->put(route('admin.menus.update', $menu), array_merge($this->validData($menu->branch_id), [
                'name' => 'Es Kopi Nabe Baru',
                'photo' => UploadedFile::fake()->image('baru.jpg'),
            ]));

        $response->assertRedirect();
        $this->assertDatabaseHas('menus', ['id' => $menu->id, 'name' => 'Es Kopi Nabe Baru']);
        Storage::disk('public')->assertMissing($oldPhoto);
        Storage::disk('public')->assertExists($menu->fresh()->photo);
    }

    public function test_foto_lama_tidak_hilang_saat_pembaruan_menu_gagal(): void
    {
        Storage::fake('public');
        $menu = Menu::factory()->create([
            'photo' => UploadedFile::fake()->image('lama.jpg')->store('menus', 'public'),
        ]);

        $oldPhoto = $menu->photo;

        // Simulasikan kegagalan penyimpanan record setelah foto baru terunggah.
        Menu::saving(function (): void {
            throw new \RuntimeException('simulasi gagal simpan');
        });

        try {
            $response = $this->actingAs($this->owner())
                ->put(route('admin.menus.update', $menu), array_merge($this->validData($menu->branch_id), [
                    'photo' => UploadedFile::fake()->image('baru.jpg'),
                ]));

            $response->assertServerError();
        } finally {
            Menu::flushEventListeners();
        }

        // Foto lama tetap utuh dan record masih menunjuk ke sana.
        Storage::disk('public')->assertExists($oldPhoto);
        $this->assertSame($oldPhoto, $menu->fresh()->photo);
        // Foto baru yang sempat terunggah ikut dibersihkan.
        $this->assertSame([$oldPhoto], Storage::disk('public')->allFiles('menus'));
    }

    public function test_upload_foto_gagal_tidak_menghapus_foto_lama_saat_mengubah_menu(): void
    {
        Storage::fake('public');
        $menu = Menu::factory()->create([
            'photo' => UploadedFile::fake()->image('lama.jpg')->store('menus', 'public'),
        ]);

        $oldPhoto = $menu->photo;

        // Simulasikan store() yang mengembalikan false, bukan exception.
        $this->diskGagalSimpanFoto();

        $response = $this->actingAs($this->owner())
            ->put(route('admin.menus.update', $menu), array_merge($this->validData($menu->branch_id), [
                'photo' => UploadedFile::fake()->image('baru.jpg'),
            ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors('photo');
        $response->assertSessionMissing('success');
        // Foto lama dan path di database tetap utuh.
        Storage::disk('public')->assertExists($oldPhoto);
        $this->assertSame($oldPhoto, $menu->fresh()->photo);
        $this->assertSame([$oldPhoto], Storage::disk('public')->allFiles('menus'));
    }

    public function test_upload_foto_gagal_tidak_membuat_menu_baru(): void
    {
        Storage::fake('public');
        $branch = Branch::factory()->create();

        $this->diskGagalSimpanFoto();

        $response = $this->actingAs($this->owner())
            ->post(route('admin.menus.store'), array_merge($this->validData($branch->id), [
                'photo' => UploadedFile::fake()->image('es-kopi.jpg'),
            ]));

        $response->assertRedirect();
        $response->assertSessionHasErrors('photo');
        $response->assertSessionMissing('success');
        $this->assertDatabaseMissing('menus', ['name' => 'Es Kopi Nabe']);
        $this->assertSame([], Storage::disk('public')->allFiles('menus'));
    }

    public function test_owner_dapat_menghapus_menu_dan_fotonya(): void
    {
        Storage::fake('public');
        $menu = Menu::factory()->create([
            'photo' => UploadedFile::fake()->image('hapus.jpg')->store('menus', 'public'),
        ]);
        $photo = $menu->photo;

        $response = $this->actingAs($this->owner())
            ->delete(route('admin.menus.destroy', $menu));

        $response->assertRedirect(route('admin.menus.index'));
        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
        Storage::disk('public')->assertMissing($photo);
    }

    public function test_admin_cabang_ditolak_dengan_403(): void
    {
        $branch = Branch::factory()->create();

        $this->actingAs($this->branchAdmin())
            ->get(route('admin.menus.index'))
            ->assertForbidden();

        $this->actingAs($this->branchAdmin())
            ->post(route('admin.menus.store'), $this->validData($branch->id))
            ->assertForbidden();
    }

    public function test_guest_ditolak_dengan_403(): void
    {
        $branch = Branch::factory()->create();

        $this->get(route('admin.menus.index'))->assertForbidden();
        $this->post(route('admin.menus.store'), $this->validData($branch->id))->assertForbidden();
    }

    public function test_menu_baru_tanpa_centang_tersedia_tersimpan_tidak_tersedia(): void
    {
        $branch = Branch::factory()->create();

        // Checkbox yang tidak dicentang tidak mengirim field sama sekali,
        // seperti form browser; sebelumnya malah tersimpan tersedia.
        $data = $this->validData($branch->id);
        unset($data['is_available']);

        $response = $this->actingAs($this->owner())
            ->post(route('admin.menus.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('menus', [
            'name' => 'Es Kopi Nabe',
            'is_available' => false,
        ]);
    }

    public function test_menu_baru_dengan_centang_tersedia_tersimpan_tersedia(): void
    {
        $branch = Branch::factory()->create();

        // Form mengirim hidden input 0 lalu checkbox 1 saat dicentang.
        $response = $this->actingAs($this->owner())
            ->post(route('admin.menus.store'), array_merge(
                $this->validData($branch->id),
                ['is_available' => '1']
            ));

        $response->assertRedirect();
        $this->assertDatabaseHas('menus', [
            'name' => 'Es Kopi Nabe',
            'is_available' => true,
        ]);
    }

    public function test_mengubah_menu_tanpa_centang_tersedia_menjadi_tidak_tersedia(): void
    {
        $menu = Menu::factory()->create(['is_available' => true]);

        $data = $this->validData($menu->branch_id);
        unset($data['is_available']);

        $response = $this->actingAs($this->owner())
            ->put(route('admin.menus.update', $menu), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'is_available' => false,
        ]);
    }

    public function test_validasi_gagal_menampilkan_pesan_bahasa_indonesia(): void
    {
        $branch = Branch::factory()->create();

        $response = $this->actingAs($this->owner())
            ->post(route('admin.menus.store'), [
                'branch_id' => $branch->id,
                'name' => '',
                'category' => 'jus',
                'price' => -1000,
            ]);

        $response->assertSessionHasErrors(['name', 'category', 'price']);

        $errors = session('errors')->getBag('default');
        $this->assertStringContainsString('wajib diisi', $errors->first('name'));
        $this->assertStringContainsString('tidak boleh kurang dari 0', $errors->first('price'));
    }
}
