<?php

namespace App\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class HandoverLetterNumber
{
    private const ROMAN_MONTHS = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
    ];

    /**
     * Buat nomor surat berikutnya. Wajib dipanggil di dalam DB::transaction
     * supaya nomor tidak ganda dan tidak bolong kalau serah terima gagal.
     */
    public function generate(CarbonInterface $date): string
    {
        if (DB::transactionLevel() === 0) {
            throw new RuntimeException('Nomor surat harus dibuat di dalam transaksi database.');
        }

        $year = $date->year;

        DB::table('letter_counters')->insertOrIgnore(['year' => $year, 'last_number' => 0]);

        // kunci baris counter tahun ini supaya serah terima bersamaan tidak dapat nomor sama
        $counter = DB::table('letter_counters')->where('year', $year)->lockForUpdate()->first();
        $next = $counter->last_number + 1;

        DB::table('letter_counters')->where('year', $year)->update(['last_number' => $next]);

        return strtr(config('school.letter_format'), [
            '{no}' => str_pad((string) $next, config('school.letter_padding', 3), '0', STR_PAD_LEFT),
            '{month}' => self::ROMAN_MONTHS[$date->month],
            '{year}' => (string) $year,
        ]);
    }
}
