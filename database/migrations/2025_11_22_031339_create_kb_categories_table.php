<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration {
    public function up(): void
    {
        Schema::create('kb_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('kb_articles', function (Blueprint $table) {
            $table->foreignId('category_id')->after('id')->constrained('kb_categories')->cascadeOnDelete();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('kb_categories');
    }
};
