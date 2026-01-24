<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uploads', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip', 45)->nullable()->index();

            // number of files uploaded in this request
            $table->unsignedInteger('file_count');

            // Stored zip file identifier + path on disk
            $table->string('zip_name')->unique();
            $table->string('zip_path');

            $table->timestamp('expires_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();

            $table->timestamps();

            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uploads');
    }
};

