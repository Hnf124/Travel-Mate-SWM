<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourismPlace;

class TourismPlacesSeeder extends Seeder
{
    public function run(): void
    {
        $places = [
            [
                'name' => 'Pantai Lampuuk',
                'city' => 'Banda Aceh',
                'category' => 'Beach',
                'address' => 'Aceh Besar, Banda Aceh',
                'description' => 'Pantai pasir putih dengan ombak tenang, cocok untuk wisata keluarga.',
                'short_description' => 'Pantai pasir putih di Banda Aceh.',
                'image_url' => 'https://example.com/lampuuk.jpg'
            ],
            [
                'name' => 'Museum Tsunami',
                'city' => 'Banda Aceh',
                'category' => 'Museum',
                'address' => 'Jl. Sultan Iskandar Muda No. 1',
                'description' => 'Museum edukasi tentang tsunami dan mitigasi bencana.',
                'short_description' => 'Museum edukasi tsunami di Banda Aceh.',
                'image_url' => 'https://example.com/tsunami.jpg'
            ],
            [
                'name' => 'Masjid Raya Baiturrahman',
                'city' => 'Banda Aceh',
                'category' => 'Heritage',
                'address' => 'Jl. Sultan Iskandar Muda No. 1',
                'description' => 'Masjid bersejarah ikon kota Banda Aceh.',
                'short_description' => 'Masjid bersejarah Banda Aceh.',
                'image_url' => 'https://example.com/masjid.jpg'
            ],
            [
                'name' => 'Pantai Lhoknga',
                'city' => 'Banda Aceh',
                'category' => 'Beach',
                'address' => 'Jl. Pantai Lhoknga, Aceh Besar',
                'description' => 'Pantai populer untuk surfing dan piknik keluarga.',
                'short_description' => 'Pantai populer di Banda Aceh.',
                'image_url' => 'https://example.com/lhoknga.jpg'
            ],
            [
                'name' => 'Gunongan Historical Park',
                'city' => 'Banda Aceh',
                'category' => 'Heritage',
                'address' => 'Jl. Teuku Umar No. 1',
                'description' => 'Taman bersejarah peninggalan Kesultanan Aceh.',
                'short_description' => 'Taman bersejarah Banda Aceh.',
                'image_url' => 'https://example.com/gunongan.jpg'
            ],
            [
                'name' => 'Pantai Ulee Lheue',
                'city' => 'Banda Aceh',
                'category' => 'Beach',
                'address' => 'Jl. Ulee Lheue, Banda Aceh',
                'description' => 'Pantai dengan pemandangan sunset dan restoran seafood.',
                'short_description' => 'Pantai sunset Banda Aceh.',
                'image_url' => 'https://example.com/ulee_lheue.jpg'
            ],
            [
                'name' => 'Museum Aceh',
                'city' => 'Banda Aceh',
                'category' => 'Museum',
                'address' => 'Jl. Sultan Iskandar Muda No. 18',
                'description' => 'Museum budaya dan sejarah Aceh.',
                'short_description' => 'Museum budaya Aceh.',
                'image_url' => 'https://example.com/museum_aceh.jpg'
            ],
            [
                'name' => 'Pantai Rancong',
                'city' => 'Banda Aceh',
                'category' => 'Beach',
                'address' => 'Jl. Pantai Rancong, Aceh Besar',
                'description' => 'Pantai tenang cocok untuk berenang dan memancing.',
                'short_description' => 'Pantai tenang di Banda Aceh.',
                'image_url' => 'https://example.com/rancong.jpg'
            ],
            [
                'name' => 'Taman Sari Gunongan',
                'city' => 'Banda Aceh',
                'category' => 'Heritage',
                'address' => 'Jl. Teuku Umar, Banda Aceh',
                'description' => 'Situs peninggalan kesultanan Aceh, taman dan bangunan klasik.',
                'short_description' => 'Situs heritage Banda Aceh.',
                'image_url' => 'https://example.com/taman_sari.jpg'
            ],
            [
                'name' => 'Pantai Lampulo',
                'city' => 'Banda Aceh',
                'category' => 'Beach',
                'address' => 'Jl. Pantai Lampulo, Banda Aceh',
                'description' => 'Pantai dengan ombak lembut dan pemandangan indah.',
                'short_description' => 'Pantai Lampulo Banda Aceh.',
                'image_url' => 'https://example.com/lampulo.jpg'
            ],
        ];

        foreach ($places as $place) {
            TourismPlace::create($place);
        }
    }
}