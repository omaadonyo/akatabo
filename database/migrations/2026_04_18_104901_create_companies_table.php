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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('logo')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('address');
            $table->boolean('active')->default(false);
            $table->text('invoice_notes')->nullable();
            $table->text('quotation_notes')->nullable();
            $table->text('receipt_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
