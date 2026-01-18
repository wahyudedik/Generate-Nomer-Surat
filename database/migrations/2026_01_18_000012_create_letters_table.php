<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letters', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // in / out
            $table->foreignId('format_id')->nullable()->constrained('letter_formats')->nullOnDelete();
            $table->string('number')->nullable();
            $table->unsignedInteger('sequence')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('scan_path')->nullable();
            $table->string('status')->default('draft');
            $table->date('issued_at')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['type', 'number']);
            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};
