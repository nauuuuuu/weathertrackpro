<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('temperature_unit', ['celsius', 'fahrenheit'])->default('celsius');
            $table->enum('wind_speed_unit', ['kmh', 'ms', 'mph'])->default('kmh');
            $table->boolean('auto_location')->default(true);
            $table->string('default_city')->nullable();
            $table->decimal('default_latitude', 10, 7)->nullable();
            $table->decimal('default_longitude', 10, 7)->nullable();
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_preferences');
    }
};
