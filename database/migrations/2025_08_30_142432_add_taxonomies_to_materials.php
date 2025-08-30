<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (! Schema::hasColumn('materials', 'families')) {
                $table->json('families')->nullable()->after('pyramid');
            }
            if (! Schema::hasColumn('materials', 'functions')) {
                $table->json('functions')->nullable()->after('families');
            }
            if (! Schema::hasColumn('materials', 'safety')) {
                $table->json('safety')->nullable()->after('functions');
            }
            if (! Schema::hasColumn('materials', 'effects')) {
                $table->json('effects')->nullable()->after('safety');
            }
            if (! Schema::hasColumn('materials', 'ifra_max_pct')) {
                $table->decimal('ifra_max_pct', 5, 2)->nullable()->after('effects');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn(['families', 'functions', 'safety', 'effects', 'ifra_max_pct']);
        });
    }
};
