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
        Schema::create('master_positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('master_division_id')->nullable();
            $table->unsignedBigInteger('master_department_id')->nullable();
            $table->string('jabatan')->nullable();
            $table->decimal('level')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('master_division_id')->references('id')->on('master_divisions')->onDelete('cascade');
            $table->foreign('master_department_id')->references('id')->on('master_departments')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('master_position_id')->references('id')->on('master_positions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_positions');
    }
};
