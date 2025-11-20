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
        Schema::create('ticket_assigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_head_id');
            $table->string('action');
            $table->text('comment')->nullable();
            $table->unsignedBigInteger('priority_id')->nullable();
            $table->unsignedBigInteger('assigned_by');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->boolean('is_consultant')->default(false);
            $table->datetime('assigned_date');
            $table->datetime('started_date')->nullable();
            $table->date('due_date')->nullable();
            $table->time('due_time')->nullable();
            $table->datetime('completed_date')->nullable();
            $table->foreign('assigned_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('ticket_head_id')->references('id')->on('ticket_heads')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_assigns');
    }
};
