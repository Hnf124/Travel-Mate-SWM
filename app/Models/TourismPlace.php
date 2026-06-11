<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourismPlace extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'category',
        'address',
        'description',
        'short_description',
        'image_url',
    ];

    // Relasi ke Favorites
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }
}