<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('planning_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_id')
                ->constrained('plannings')
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('slot_order');
            $table->string('slot_name');
            $table->unsignedInteger('original_quantity');
            $table->unsignedInteger('balanced_quantity')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
 
            $table->unique(['planning_id', 'slot_order']);
        });
 
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE planning_slots ADD CONSTRAINT chk_quantity_non_negative CHECK (original_quantity >= 0 AND (balanced_quantity IS NULL OR balanced_quantity >= 0))');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planning_slots');
    }
};
