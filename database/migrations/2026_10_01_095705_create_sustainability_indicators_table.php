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
    Schema::create('sustainability_indicators', function (Blueprint $table) {
        $table->id();

        $table->foreignId('destination_id')
            ->constrained('destinations')
            ->cascadeOnDelete();

        $table->unsignedTinyInteger('eco_transport')->default(0);
        $table->unsignedTinyInteger('waste_management')->default(0);
        $table->unsignedTinyInteger('local_community')->default(0);
        $table->unsignedTinyInteger('conservation')->default(0);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sustainability_indicators');
    }
};
