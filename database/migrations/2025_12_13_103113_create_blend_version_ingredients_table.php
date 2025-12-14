<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blend_version_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blend_version_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('drops');
            $table->unsignedInteger('dilution');
            $table->timestamps();
            $table->unique(['blend_version_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blend_version_ingredients');
    }
};
