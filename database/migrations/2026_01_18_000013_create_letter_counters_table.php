<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_counters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('format_id')->constrained('letter_formats')->cascadeOnDelete();
            $table->string('period');
            $table->string('unit_code')->nullable();
            $table->unsignedInteger('current_number')->default(0);
            $table->timestamps();

            $table->unique(['format_id', 'period', 'unit_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_counters');
    }
};
