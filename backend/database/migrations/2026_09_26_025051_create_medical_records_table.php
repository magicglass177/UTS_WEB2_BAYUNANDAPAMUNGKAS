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
    Schema::create('medical_records', function (Blueprint $table) {
        $table->id();

        $table->foreignId('patient_id')
            ->constrained()
            ->cascadeOnUpdate()
            ->restrictOnDelete();

        $table->foreignId('user_id')
            ->constrained()
            ->cascadeOnUpdate()
            ->restrictOnDelete();

        $table->date('examination_date');
        $table->text('complaint');
        $table->string('diagnosis', 255);
        $table->text('treatment')->nullable();
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::dropIfExists('medical_records');
}
};
