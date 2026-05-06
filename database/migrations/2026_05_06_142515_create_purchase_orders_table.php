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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->nullable()->constrained();
            $table->foreignId('vendor_id')->constrained();
            $table->string('po_number')->unique();
            $table->decimal('total_amount', 15, 2);
            $table->string('pdf_path')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
