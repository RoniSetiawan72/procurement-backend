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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tender_id')->constrained();
            $table->foreignId('vendor_id')->constrained();
            $table->decimal('offered_price', 15, 2);
            $table->string('bid_document_path')->nullable();
            $table->boolean('is_winner')->default(false);
            $table->timestamps();

            $table->unique(['tender_id', 'vendor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
