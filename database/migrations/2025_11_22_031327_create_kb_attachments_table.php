<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kb_attachments', function (Blueprint $table) {
            $table->id();
            // Artikel yang dilampiri
            $table->foreignId('article_id')->constrained('kb_articles')->cascadeOnDelete();
            // Path file relatif dari disk "public" (mis. "kb/attachments/xxxx.pdf")
            $table->string('path');
            // Nama asli file untuk ditampilkan
            $table->string('original_name');
            // Mime dan size untuk info kecil
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('article_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kb_attachments');
    }
};
