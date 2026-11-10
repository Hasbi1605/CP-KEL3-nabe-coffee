<?php

namespace App\Services\Menu;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class HomepageContentService
{
    private const FILE = 'cms/homepage-content.json';

    /**
     * @var array<string, string>
     */
    private const DEFAULTS = [
        'outlet_info_title' => 'INFO CABANG NABE COFFEE.',
        'outlet_info_text' => 'Empat cabang Nabe Coffee siap menyambut hari baikmu. Cek alamat, jam operasional, dan peta tiap cabang di bawah ini.',
        'promo_title' => 'PENAWARAN HARI BAIKMU.',
        'promo_text' => 'Promo aktif dari Nabe Coffee untuk menemani kopi dan camilan favoritmu.',
    ];

    /**
     * @return array<string, string>
     */
    public function all(): array
    {
        if (! Storage::disk('local')->exists(self::FILE)) {
            return self::DEFAULTS;
        }

        $decoded = json_decode((string) Storage::disk('local')->get(self::FILE), true);

        if (! is_array($decoded)) {
            return self::DEFAULTS;
        }

        return array_merge(self::DEFAULTS, array_filter(
            $decoded,
            fn ($value) => is_string($value)
        ));
    }

    /**
     * @param  array<string, mixed>  $content
     * @return array<string, string>
     */
    public function update(array $content): array
    {
        $clean = [];

        foreach (self::DEFAULTS as $key => $default) {
            $value = $content[$key] ?? null;
            $clean[$key] = is_string($value) && trim($value) !== '' ? trim($value) : $default;
        }

        $disk = Storage::disk('local');
        $temporary = self::FILE.'.'.Str::uuid().'.tmp';

        if (! $disk->put($temporary, json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) || ! $disk->move($temporary, self::FILE)) {
            $disk->delete($temporary);

            throw ValidationException::withMessages(['content' => 'Teks halaman utama gagal disimpan. Silakan coba lagi.']);
        }

        return $clean;
    }

    public function get(string $key): string
    {
        return $this->all()[$key] ?? (self::DEFAULTS[$key] ?? '');
    }
}
