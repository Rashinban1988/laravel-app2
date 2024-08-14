<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('manual_id');
            $table->foreign('manual_id')->references('id')->on('manuals')->onDelete('cascade');
            $table->string('file_path');
            $table->unsignedBigInteger('file_size');
            $table->string('file_type');
            $table->string('mime_type');
            $table->text('comment')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->boolean('exist')->nullable()->storedAs('case when deleted_at is null then 1 else null end');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};