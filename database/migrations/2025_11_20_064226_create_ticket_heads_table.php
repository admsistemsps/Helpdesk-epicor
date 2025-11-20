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
        Schema::create('ticket_heads', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_fuhd')->unique();
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('requestor_id');
            $table->string('priority')->default('Medium');
            $table->string('status')->default('pending');
            $table->datetime('created_date');
            $table->datetime('closed_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('requestor_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_heads');
    }
};
