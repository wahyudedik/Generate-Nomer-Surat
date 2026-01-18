<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('letter_formats', function (Blueprint $table) {
            $table->string('period_mode')->default('year')->after('type'); // year, month, all
            $table->string('counter_scope')->default('global')->after('period_mode'); // global, unit
        });
    }

    public function down(): void
    {
        Schema::table('letter_formats', function (Blueprint $table) {
            $table->dropColumn(['period_mode', 'counter_scope']);
        });
    }
};
