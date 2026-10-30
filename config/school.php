<?php

return [
    'name' => env('SCHOOL_NAME', 'SMPN 6 Babelan'),

    /*
    | Format nomor surat serah terima.
    | Placeholder: {no} = nomor urut, {month} = bulan romawi, {year} = tahun.
    | Ganti bagian lainnya (BAST, SARPRAS, SMKN1) sesuai format surat sekolah Anda.
    | Contoh hasil: 001/BAST/SARPRAS/SMKN1/X/2026
    */
    'letter_format' => env('SCHOOL_LETTER_FORMAT', '{no}/BAST/SARPRAS/SMPN6BABELAN/{month}/{year}'),

    // jumlah digit nomor urut (3 => 001)
    'letter_padding' => 3,
];
