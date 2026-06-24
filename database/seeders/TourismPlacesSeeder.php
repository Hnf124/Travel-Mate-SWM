<?php

namespace Database\Seeders;

use App\Models\TourismPlace;
use Illuminate\Database\Seeder;

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
                'image_url' => '/images/beach.svg',
            ],
            [
                'name' => 'Museum Tsunami',
                'city' => 'Banda Aceh',
                'category' => 'Museum',
                'address' => 'Jl. Sultan Iskandar Muda No. 3, Banda Aceh',
                'description' => 'Museum edukasi tentang tsunami, sejarah bencana, dan mitigasi.',
                'short_description' => 'Museum edukasi tsunami di Banda Aceh.',
                'image_url' => '/images/museum.svg',
            ],
            [
                'name' => 'Masjid Raya Baiturrahman',
                'city' => 'Banda Aceh',
                'category' => 'Heritage',
                'address' => 'Jl. Moh. Jam, Banda Aceh',
                'description' => 'Masjid bersejarah dan salah satu ikon utama Kota Banda Aceh.',
                'short_description' => 'Masjid bersejarah Banda Aceh.',
                'image_url' => '/images/heritage.svg',
            ],
            [
                'name' => 'Pantai Lhoknga',
                'city' => 'Banda Aceh',
                'category' => 'Beach',
                'address' => 'Lhoknga, Aceh Besar',
                'description' => 'Pantai populer untuk menikmati laut, berselancar, dan piknik keluarga.',
                'short_description' => 'Pantai populer di kawasan Banda Aceh.',
                'image_url' => '/images/beach.svg',
            ],
            [
                'name' => 'Gunongan Historical Park',
                'city' => 'Banda Aceh',
                'category' => 'Heritage',
                'address' => 'Sukaramai, Baiturrahman, Banda Aceh',
                'description' => 'Kompleks bersejarah peninggalan Kesultanan Aceh dengan arsitektur khas.',
                'short_description' => 'Taman bersejarah peninggalan Kesultanan Aceh.',
                'image_url' => '/images/heritage.svg',
            ],
            [
                'name' => 'Pantai Ulee Lheue',
                'city' => 'Banda Aceh',
                'category' => 'Beach',
                'address' => 'Ulee Lheue, Meuraxa, Banda Aceh',
                'description' => 'Kawasan pantai untuk menikmati matahari terbenam dan kuliner laut.',
                'short_description' => 'Pantai untuk menikmati sunset Banda Aceh.',
                'image_url' => '/images/beach.svg',
            ],
            [
                'name' => 'Museum Aceh',
                'city' => 'Banda Aceh',
                'category' => 'Museum',
                'address' => 'Jl. Sultan Alaiddin Mahmudsyah, Banda Aceh',
                'description' => 'Museum yang menyimpan koleksi budaya, sejarah, dan kehidupan masyarakat Aceh.',
                'short_description' => 'Museum budaya dan sejarah Aceh.',
                'image_url' => '/images/museum.svg',
            ],
            [
                'name' => 'Pantai Rancong',
                'city' => 'Banda Aceh',
                'category' => 'Beach',
                'address' => 'Jl. Pantai Rancong, Aceh Besar',
                'description' => 'Pantai tenang yang cocok untuk berenang, memancing, dan bersantai.',
                'short_description' => 'Pantai tenang di Banda Aceh.',
                'image_url' => '/images/beach.svg',
            ],
            [
                'name' => 'Taman Sari Gunongan',
                'city' => 'Banda Aceh',
                'category' => 'Heritage',
                'address' => 'Sukaramai, Baiturrahman, Banda Aceh',
                'description' => 'Situs bersejarah Kesultanan Aceh berupa taman dan bangunan klasik.',
                'short_description' => 'Situs heritage Banda Aceh.',
                'image_url' => '/images/heritage.svg',
            ],
            [
                'name' => 'Pantai Lampulo',
                'city' => 'Banda Aceh',
                'category' => 'Beach',
                'address' => 'Jl. Pantai Lampulo, Banda Aceh',
                'description' => 'Pantai dengan ombak lembut dan pemandangan pesisir yang indah.',
                'short_description' => 'Pantai Lampulo Banda Aceh.',
                'image_url' => '/images/beach.svg',
            ],
        ];

        foreach ($places as $place) {
            TourismPlace::updateOrCreate(
                [
                    'name' => $place['name'],
                    'city' => $place['city'],
                ],
                $place
            );
        }
    }
}
