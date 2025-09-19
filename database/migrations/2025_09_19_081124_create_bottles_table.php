<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bottles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->string('supplier_name')->nullable();
            $table->string('supplier_url')->nullable();
            $table->string('batch_code')->nullable();
            $table->string('method')->nullable();
            $table->string('plant_part')->nullable();
            $table->string('origin_country')->nullable();
            $table->date('distillation_date')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('density', 6, 3)->nullable();
            $table->decimal('volume_ml', 8, 2)->nullable();
            $table->decimal('price', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bottles');
    }
};
