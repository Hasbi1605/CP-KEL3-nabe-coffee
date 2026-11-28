<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Menu;
use App\Models\Promotion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotWidgetTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_chatbot_widget_with_branches_from_database(): void
    {
        Branch::factory()->create(['name' => 'Nabe Coffee Kusumanegara']);
        Branch::factory()->create(['name' => 'Nabe Coffee Demangan']);

        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('data-chatbot-widget', false);
        $homeResponse->assertSee('id="nabe-chatbot-launcher"', false);
        $homeResponse->assertSee('id="nabe-chatbot-panel"', false);
        $homeResponse->assertSee('Hai, Nabe di sini!');
        $homeResponse->assertSee('Nabe Buddy');
        $homeResponse->assertSee('Rekomendasi menu');
        $homeResponse->assertSee('Promo hari ini');
        $homeResponse->assertSee('Nabe Coffee Kusumanegara');
        $homeResponse->assertSee('Nabe Coffee Demangan');
        $homeResponse->assertSee('Nabe Buddy sedang menyiapkan jawaban');
        $homeResponse->assertDontSee('6281234567890');

        $menuResponse = $this->get('/menu');
        $menuResponse->assertStatus(200);
        $menuResponse->assertSee('data-chatbot-widget', false);
        $menuResponse->assertSee('Hai, Nabe di sini!');
    }

    public function test_login_page_does_not_render_public_chatbot_widget(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertDontSee('data-chatbot-widget', false);
    }

    public function test_chatbot_endpoint_validates_message_and_branch_id(): void
    {
        $response = $this->postJson('/chatbot/message', [
            'message' => '',
            'branch_id' => 9999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message', 'branch_id']);
    }

    public function test_chatbot_returns_at_most_three_best_menus_from_database(): void
    {
        $kusuma = Branch::factory()->create(['name' => 'Nabe Coffee Kusumanegara']);
        $demangan = Branch::factory()->create(['name' => 'Nabe Coffee Demangan']);

        Menu::factory()->create([
            'branch_id' => $kusuma->id,
            'name' => 'Eskopsu Lait',
            'category' => 'coffee',
            'price' => 18000,
            'is_best_seller' => true,
            'is_available' => true,
        ]);
        Menu::factory()->create([
            'branch_id' => $kusuma->id,
            'name' => 'Matcha Latte',
            'category' => 'non_coffee',
            'price' => 24000,
            'is_best_seller' => true,
            'is_available' => true,
        ]);
        Menu::factory()->create([
            'branch_id' => $kusuma->id,
            'name' => 'Butter Croissant',
            'category' => 'snack',
            'price' => 22000,
            'is_best_seller' => false,
            'is_available' => true,
        ]);
        Menu::factory()->create([
            'branch_id' => $kusuma->id,
            'name' => 'Nasi Goreng Nabe',
            'category' => 'heavy_meal',
            'price' => 32000,
            'is_best_seller' => false,
            'is_available' => true,
        ]);
        Menu::factory()->create([
            'branch_id' => $demangan->id,
            'name' => 'Cold Brew Demangan',
            'category' => 'coffee',
            'price' => 23000,
            'is_best_seller' => true,
            'is_available' => true,
        ]);

        Promotion::factory()->create([
            'branch_id' => $kusuma->id,
            'title' => 'Pagi Hemat Kusuma',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDay(),
        ]);

        $response = $this->postJson('/chatbot/message', [
            'message' => 'Rekomendasi menu',
            'branch_id' => $kusuma->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('intent', 'rekomendasi_menu')
            ->assertJsonPath('empty', false)
            ->assertJsonCount(3, 'items');

        $titles = collect($response->json('items'))->pluck('title')->all();
        $this->assertContains('Eskopsu Lait', $titles);
        $this->assertContains('Matcha Latte', $titles);
        $this->assertNotContains('Cold Brew Demangan', $titles);
    }

    public function test_chatbot_returns_empty_state_in_indonesian_when_no_menus_or_promos_exist(): void
    {
        $branch = Branch::factory()->create(['name' => 'Nabe Coffee Soragan']);

        $menuResponse = $this->postJson('/chatbot/message', [
            'message' => 'Rekomendasi menu',
            'branch_id' => $branch->id,
        ]);

        $menuResponse->assertStatus(200)
            ->assertJsonPath('intent', 'rekomendasi_menu')
            ->assertJsonPath('empty', true)
            ->assertJsonCount(0, 'items');

        $promoResponse = $this->postJson('/chatbot/message', [
            'message' => 'Promo hari ini',
            'branch_id' => $branch->id,
        ]);

        $promoResponse->assertStatus(200)
            ->assertJsonPath('intent', 'tanya_promo')
            ->assertJsonPath('empty', true)
            ->assertJsonCount(0, 'items');
    }

    public function test_chatbot_returns_active_promotions_branch_hours_and_guides(): void
    {
        $branch = Branch::factory()->create([
            'name' => 'Nabe Coffee Batikan (THE PADEL)',
            'address' => 'Jl. Batikan No.9, Yogyakarta',
            'opens_at' => '09:00:00',
            'closes_at' => '22:00:00',
        ]);

        Promotion::factory()->create([
            'branch_id' => $branch->id,
            'title' => 'Padel Coffee Bundle',
            'terms' => 'Berlaku pukul 09.00–12.00 WIB',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(2),
        ]);

        $this->postJson('/chatbot/message', [
            'message' => 'Promo hari ini',
            'branch_id' => $branch->id,
        ])
            ->assertStatus(200)
            ->assertJsonPath('intent', 'tanya_promo')
            ->assertJsonPath('empty', false)
            ->assertJsonPath('items.0.title', 'Padel Coffee Bundle');

        $this->postJson('/chatbot/message', [
            'message' => 'Jam & lokasi',
            'branch_id' => $branch->id,
        ])
            ->assertStatus(200)
            ->assertJsonPath('intent', 'tanya_lokasi')
            ->assertJsonPath('items.0.title', 'Nabe Coffee Batikan (THE PADEL)')
            ->assertSee('09.00', false);

        $this->postJson('/chatbot/message', ['message' => 'Cara reservasi'])
            ->assertStatus(200)
            ->assertJsonPath('intent', 'reservasi_baru');

        $this->postJson('/chatbot/message', ['message' => 'Cek status'])
            ->assertStatus(200)
            ->assertJsonPath('intent', 'cek_reservasi');

        $this->postJson('/chatbot/message', ['message' => 'Pertanyaan acak tidak dikenal'])
            ->assertStatus(200)
            ->assertJsonPath('intent', 'fallback')
            ->assertJsonPath('source', 'fallback');
    }
}
