<?php

namespace App\Contracts;

interface TravelMateServiceInterface
{
    // Ambil daftar tempat wisata berdasarkan kota
    public function getTourismPlacesByCity(string $city);

    // Ambil detail satu tempat wisata
    public function getTourismPlaceDetail(int $id);

    // Simpan favorit
    public function addFavorite(int $userId, int $tourismPlaceId);

    // Hapus favorit
    public function removeFavorite(int $userId, int $tourismPlaceId);

    // Ambil daftar favorit user
    public function getFavorites(int $userId);

    // Simpan riwayat pencarian
    public function saveSearchHistory(int $userId, string $keyword, string $type);

    // Ambil riwayat pencarian user (maksimal 20)
    public function getSearchHistory(int $userId);
}