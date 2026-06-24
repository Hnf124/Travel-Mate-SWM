<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Akun dibuat melalui halaman register. Seeder hanya mengisi data wisata.
        $this->call([
            TourismPlacesSeeder::class,
        ]);
    }
}
