<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->restrictOnDelete();
            $table->foreignId('item_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->date('loan_date');
            $table->foreignId('operator_id')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected', 'handed_over'])->default('pending');
            $table->text('status_note')->nullable();
            $table->foreignId('sarpras_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('handover_letter_number')->nullable();
            $table->timestamp('handed_over_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'loan_date']);
            $table->index('handed_over_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
