<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // operator_id sekarang berisi sarpras yang mencatat pengajuan
        Schema::table('loans', function (Blueprint $table) {
            $table->renameColumn('operator_id', 'created_by');
        });

        // catat siapa dan kapan keputusan ACC/tolak/pending diberikan
        Schema::table('loans', function (Blueprint $table) {
            $table->foreignId('decided_by')
                ->nullable()
                ->after('status_note')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('decided_at')->nullable()->after('decided_by');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('decided_by');
            $table->dropColumn('decided_at');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->renameColumn('created_by', 'operator_id');
        });
    }
};
