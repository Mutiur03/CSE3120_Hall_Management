<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('seats', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('seat_allocations', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('room_change_requests', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('seats', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('seat_allocations', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('room_change_requests', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
