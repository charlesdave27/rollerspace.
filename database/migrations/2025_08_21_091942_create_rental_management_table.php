<?php

use App\Models\Equipment;
use App\Models\RentalPackage;
use App\Models\LoyaltyMember;
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
        Schema::create('rental_management', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LoyaltyMember::class)->nullable();
            $table->string('name')->nullable();
            $table->foreignIdFor(RentalPackage::class)->nullable();
            $table->foreignIdFor(Equipment::class)->nullable();
            $table->foreignId('reward_id')->nullable();
            $table->decimal('price_paid', 8, 2)->nullable();
            $table->integer('points')->nullable();
            $table->timestamp('deadline')->nullable();
            $table->boolean('returned')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_management');
    }
};
