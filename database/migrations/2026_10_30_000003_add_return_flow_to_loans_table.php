<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // tambah status 'returned' dan 'cancelled'
        if (in_array(DB::getDriverName(), ['mysql', 'mariadb'], true)) {
            DB::statement(
                "ALTER TABLE loans MODIFY status ENUM('pending','approved','rejected','handed_over','returned','cancelled') NOT NULL DEFAULT 'pending'"
            );
        } else {
            Schema::table('loans', function (Blueprint $table) {
                $table->string('status', 20)->default('pending')->change();
            });
        }

        Schema::table('loans', function (Blueprint $table) {
            $table->timestamp('returned_at')->nullable()->after('handed_over_at');
            $table->foreignId('returned_by')
                ->nullable()
                ->after('returned_at')
                ->constrained('users')
                ->nullOnDelete();
            $table->text('return_note')->nullable()->after('returned_by');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('returned_by');
            $table->dropColumn(['returned_at', 'return_note']);
        });
        // enum status sengaja tidak dikembalikan agar data returned/cancelled tidak rusak
    }
};
