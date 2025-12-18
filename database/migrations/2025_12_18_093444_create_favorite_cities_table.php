<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorite_cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('city_name');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('country')->nullable();
            $table->string('admin1')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'city_name', 'latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_cities');
    }
};
