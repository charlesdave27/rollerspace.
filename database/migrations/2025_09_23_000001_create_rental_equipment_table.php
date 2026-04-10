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
    Schema::create('rental_equipment', function (Blueprint $table) {
      $table->id();
      $table->foreignId('rental_management_id')->constrained('rental_management')->cascadeOnDelete();
      $table->foreignId('equipment_id')->constrained('equipment')->cascadeOnDelete();
      $table->boolean('returned')->default(false);
      $table->timestamps();
      $table->unique(['rental_management_id', 'equipment_id']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('rental_equipment');
  }
};
