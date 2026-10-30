<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_counters', function (Blueprint $table) {
            $table->unsignedSmallInteger('year')->primary();
            $table->unsignedInteger('last_number')->default(0);
        });

        // nomor surat tidak boleh kembar (kolom nullable, jadi loan yang belum diserahkan aman)
        Schema::table('loans', function (Blueprint $table) {
            $table->unique('handover_letter_number');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropUnique(['handover_letter_number']);
        });

        Schema::dropIfExists('letter_counters');
    }
};
