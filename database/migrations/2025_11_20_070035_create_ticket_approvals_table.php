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
        Schema::create('ticket_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->string('level');
            $table->string('action');
            $table->string('note')->nullable();
            $table->datetime('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ticket_id')->references('id')->on('ticket_heads')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('position_id')->references('id')->on('master_positions')->onDelete('cascade');
            $table->foreign('division_id')->references('id')->on('master_divisions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_approvals');
    }
};
