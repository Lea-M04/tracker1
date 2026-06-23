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
        if (Schema::hasColumn('projects', 'start_date') && Schema::hasColumn('projects', 'deadline')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('description');
            $table->date('deadline')->nullable()->after('start_date');

            $table->index(['user_id', 'deadline']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('projects', 'start_date') && ! Schema::hasColumn('projects', 'deadline')) {
            return;
        }

        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'deadline']);
            $table->dropColumn(['start_date', 'deadline']);
        });
    }
};
