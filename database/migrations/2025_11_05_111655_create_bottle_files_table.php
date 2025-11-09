<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bottle_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bottle_id')->constrained()->cascadeOnDelete();

            // storage metadata
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('note', 255)->nullable();
            $table->timestamps();
            // index for quick listing
            $table->index(['bottle_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bottle_files');
    }
};
