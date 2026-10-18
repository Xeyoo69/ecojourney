<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('rating'); // 1–5 bintang
            $table->string('title')->nullable();
            $table->text('comment');
            $table->timestamps();

            $table->unique(['user_id', 'destination_id']);
            $table->index('destination_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
