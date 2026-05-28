<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id');
            $table->string('type');
            $table->string('document_number');
            $table->unsignedBigInteger('document_id');
            $table->string('document_type');
            $table->decimal('amount', 12, 2)->default(0);
            $table->date('date');
            $table->string('status');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['document_type', 'document_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
