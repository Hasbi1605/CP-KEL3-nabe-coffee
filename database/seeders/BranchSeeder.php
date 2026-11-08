<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        // TODO: isi alamat, jam, dan kontak ASLI dari laporan Bab I
        $branches = [
            [
                'name' => 'Kusumanegara',
                'address' => 'TODO: alamat dari laporan Bab I',
                'opens_at' => '07:00:00', // TODO: jam dari laporan Bab I
                'closes_at' => '23:00:00', // TODO: jam dari laporan Bab I
                'admin_whatsapp' => null, // TODO: no WA admin
                'telegram_chat_id' => null, // TODO: telegram_chat_id
                'pic_email' => null, // TODO: email PJ
                'maps_url' => null, // TODO: link Google Maps
            ],
            [
                'name' => 'Demangan',
                'address' => 'TODO: alamat dari laporan Bab I',
                'opens_at' => '07:00:00', // TODO: jam dari laporan Bab I
                'closes_at' => '23:00:00', // TODO: jam dari laporan Bab I
                'admin_whatsapp' => null,
                'telegram_chat_id' => null,
                'pic_email' => null,
                'maps_url' => null,
            ],
            [
                'name' => 'Soragan',
                'address' => 'TODO: alamat dari laporan Bab I',
                'opens_at' => '07:00:00', // jam dari laporan (07.00–00.00)
                'closes_at' => '00:00:00', // jam dari laporan (07.00–00.00)
                'admin_whatsapp' => null,
                'telegram_chat_id' => null,
                'pic_email' => null,
                'maps_url' => null,
            ],
            [
                'name' => 'Batikan',
                'address' => 'TODO: alamat dari laporan Bab I',
                'opens_at' => '07:00:00', // TODO: jam dari laporan Bab I
                'closes_at' => '23:00:00', // TODO: jam dari laporan Bab I
                'admin_whatsapp' => null,
                'telegram_chat_id' => null,
                'pic_email' => null,
                'maps_url' => null,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
