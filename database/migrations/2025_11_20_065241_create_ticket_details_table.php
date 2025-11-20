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
        Schema::create('ticket_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_head_id');
            $table->integer('ticket_line');
            $table->string('nomor');
            $table->text('desc_before');
            $table->text('desc_after');
            $table->text('comment')->nullable();
            $table->string('reason')->nullable();
            $table->date('created_date');
            $table->date('closed_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ticket_head_id')->references('id')->on('ticket_heads')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_details');
    }
};
