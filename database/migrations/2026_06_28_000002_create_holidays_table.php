<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table): void {
            $table->id();
            $table->date('date')->index();
            $table->string('name');
            $table->enum('type', ['national', 'collective'])->default('national')->index();
            $table->string('source')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['date', 'name', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
