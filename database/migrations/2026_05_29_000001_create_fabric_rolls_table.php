<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fabric_rolls', function (Blueprint $table) {
            $table->id();
            $table->string('roll_code');
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('fabric_name');
            $table->string('color');
            $table->string('supplier');
            $table->date('date_received');
            $table->decimal('claimed_meters', 12, 2);
            $table->decimal('verified_meters', 12, 2);
            $table->decimal('buying_price_per_meter', 12, 2);
            $table->decimal('selling_price_per_meter', 12, 2);
            $table->decimal('fabric_width', 8, 2)->nullable();
            $table->string('status')->default('in_stock');
            $table->decimal('remaining_meters', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id', 'roll_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fabric_rolls');
    }
};
