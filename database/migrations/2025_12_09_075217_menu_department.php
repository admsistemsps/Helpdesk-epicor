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
        Schema::create('menu_department', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('master_menus')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('master_departments')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_department');
    }
};
