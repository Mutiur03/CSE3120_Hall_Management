<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dining_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('meal_type');
            $table->boolean('present')->default(false);
            $table->time('time')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'date', 'meal_type']);
            $table->index(['date', 'meal_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dining_attendances');
    }
};
