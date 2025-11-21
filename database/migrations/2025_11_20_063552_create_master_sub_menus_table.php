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
        Schema::create('master_sub_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('placeholder')->nullable();
            $table->unsignedBigInteger('menu_id')->nullable();

            $table->foreign('menu_id')->references('id')->on('master_menus');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_sub_menus');
    }
};
