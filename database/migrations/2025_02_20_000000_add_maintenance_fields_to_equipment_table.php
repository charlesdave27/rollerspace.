<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->string('status')->default('available')->after('is_available');
            $table->text('maintenance_notes')->nullable()->after('status');
            $table->timestamp('last_maintenance_at')->nullable()->after('maintenance_notes');
        });
    }

    public function down(): void
    {
        Schema::table('equipment', function (Blueprint $table) {
            $table->dropColumn(['status', 'maintenance_notes', 'last_maintenance_at']);
        });
    }
};
