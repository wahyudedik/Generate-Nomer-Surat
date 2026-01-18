<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_format_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('format_id')->constrained('letter_formats')->cascadeOnDelete();
            $table->unsignedInteger('order');
            $table->string('kind'); // sequence, text, month_roman, month_number, year
            $table->string('value')->nullable();
            $table->unsignedInteger('padding')->default(0);
            $table->timestamps();

            $table->unique(['format_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_format_segments');
    }
};
