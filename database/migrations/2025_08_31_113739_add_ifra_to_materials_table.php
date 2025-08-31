<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (! Schema::hasColumn('materials', 'ifra_max_pct')) {
                $table->decimal('ifra_max_pct', 5, 2)->nullable()->after('effects');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            if (Schema::hasColumn('materials', 'ifra_max_pct')) {
                $table->dropColumn('ifra_max_pct');
            }
        });
    }
};
