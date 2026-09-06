<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blend_versions', function (Blueprint $table) {
            $table->unsignedInteger('version')->change();
        });
    }

    public function down(): void
    {
        Schema::table('blend_versions', function (Blueprint $table) {
            $table->string('version')->change();
        });
    }
};
