<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->integer('fiscal_year');
            $table->decimal('total_amount', 15, 2);
            $table->decimal('used_amount', 15, 2)->default(0);
            $table->decimal('reserved_amount', 15, 2)->default(0);
            $table->timestamps();
        });

        DB::statement('ALTER TABLE budgets ADD CONSTRAINT budget_check CHECK (used_amount + reserved_amount <= total_amount)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
