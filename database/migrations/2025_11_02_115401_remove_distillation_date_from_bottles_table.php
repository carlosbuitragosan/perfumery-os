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
        if (Schema::hasColumn('bottles', 'distillation_date') && DB::getDriverName() !== 'sqlite') {
            Schema::table('bottles', function (Blueprint $table) {
                $table->dropColumn('distillation_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bottles', function (Blueprint $table) {
            $table->date('distillation_date')->nullable()->after('origin_country');
        });
    }
};
