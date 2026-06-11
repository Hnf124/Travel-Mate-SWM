<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesTable extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tourism_place_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id','tourism_place_id']); // user tidak boleh duplikat favorit
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
}