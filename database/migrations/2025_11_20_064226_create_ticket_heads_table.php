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
            $table->unsignedBigInteger('site_id');
            $table->string('nomor_fuhd')->unique();
            $table->string('slug');
            $table->unsignedBigInteger('menu_id');
            $table->unsignedBigInteger('sub_menu_id')->nullable();
            $table->unsignedBigInteger('requestor_id');
            $table->unsignedBigInteger('assignee_id')->nullable();
            $table->string('priority')->default('Medium');
            $table->string('status')->default('pending');
            $table->string('current_approval_level')->nullable();
            $table->decimal('current_approval_value', 6, 2)->nullable();
            $table->unsignedBigInteger('current_approval_position_id')->nullable();
            $table->unsignedBigInteger('current_approval_division_id')->nullable();
            $table->datetime('created_date');
            $table->datetime('closed_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('requestor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('menu_id')->references('id')->on('master_menus')->onDelete('cascade');
            $table->foreign('sub_menu_id')->references('id')->on('master_sub_menus')->onDelete('cascade');
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
