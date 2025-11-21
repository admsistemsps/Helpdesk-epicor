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
        Schema::create('log_ticket_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_detail_id');
            $table->unsignedBigInteger('ticket_head_id');
            $table->integer('ticket_line');
            $table->string('nomor');
            $table->text('desc_before');
            $table->text('desc_after');
            $table->text('comment')->nullable();
            $table->string('reason')->nullable();
            $table->string('action');
            $table->unsignedBigInteger('logged_by');
            $table->datetime('logged_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_ticket_details');
    }
};
