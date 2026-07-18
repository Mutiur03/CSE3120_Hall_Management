<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->boolean('breakfast')->default(true);
            $table->boolean('lunch')->default(true);
            $table->boolean('dinner')->default(true);
            $table->boolean('meal_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'date']);
            $table->index(['date', 'meal_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
