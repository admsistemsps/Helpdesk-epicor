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
        Schema::create('master_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('division_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('department_id')->references('id')->on('master_departments')->onDelete('cascade');
            $table->foreign('division_id')->references('id')->on('master_divisions')->onDelete('cascade');
        });

        Schema::table(('ticket_heads'), function (Blueprint $table) {
            $table->foreign('menu_id')->references('id')->on('master_menus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_menus');
    }
};
