<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTourismPlacesTable extends Migration
{
    public function up(): void
    {
        Schema::create('tourism_places', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->string('category');
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tourism_places');
    }
}