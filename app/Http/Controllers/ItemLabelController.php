<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;

/**
 * Cetak label barang. Tidak menyimpan apa pun ke database:
 * isi label dibuat langsung dari tabel items (code, name, category).
 *
 * Mode:
 *  - unit (default): satu label per unit barang, bernomor (KODE-001, KODE-002, ...).
 *    Jumlahnya = stok + barang yang sedang dipinjam, jadi tetap konsisten
 *    walaupun sebagian unit sedang keluar.
 *  - type: satu label per jenis barang, diperbanyak sesuai 'copies'.
 */
class ItemLabelController extends Controller
{
    /** Batas label per sekali cetak, supaya halaman tidak terlalu berat. */
    private const MAX_LABELS = 300;

    public function __invoke(Request $request)
    {
        $data = $request->validate([
            'ids'    => ['required', 'array', 'min:1'],
            'ids.*'  => ['integer', 'exists:items,id'],
            'mode'   => ['nullable', 'in:type,unit'],
            'copies' => ['nullable', 'integer', 'min:1', 'max:50'],
        ], [
            'ids.required' => 'Pilih minimal satu barang untuk dicetak labelnya.',
        ]);

        $mode   = $data['mode'] ?? 'unit';
        $copies = (int) ($data['copies'] ?? 1);

        $items = Item::with('category')
            ->whereIn('id', $data['ids'])
            ->orderBy('code')
            ->get();

        // jumlah yang sedang keluar (sudah di-ACC atau sedang dipinjam), per barang
        $borrowed = Loan::whereIn('item_id', $items->pluck('id'))
            ->whereIn('status', [Loan::STATUS_APPROVED, Loan::STATUS_HANDED_OVER])
            ->selectRaw('item_id, SUM(quantity) as qty')
            ->groupBy('item_id')
            ->pluck('qty', 'item_id');

        // total unit fisik milik record ini = stok di gudang + yang sedang keluar
        $unitCount = fn (Item $item): int => $item->stock + (int) ($borrowed[$item->id] ?? 0);

        $total = $mode === 'unit'
            ? (int) $items->sum($unitCount)
            : $items->count() * $copies;

        if ($total > self::MAX_LABELS) {
            return back()->with('error', 'Terlalu banyak label ('.$total.'). Maksimal '.self::MAX_LABELS.' label sekali cetak.');
        }

        $qr = new QRCode(new QROptions([
            'outputInterface' => QRMarkupSVG::class,
            'outputBase64'    => true,   // hasil berupa data URI, langsung dipakai di <img src>
            'eccLevel'        => EccLevel::M,
            'addQuietzone'    => true,
            'quietzoneSize'   => 1,
        ]));

        $qrCache = [];
        $make = function (Item $item, string $code) use ($qr, &$qrCache): array {
            $qrCache[$code] ??= $qr->render($code);

            return [
                'code'     => $code,
                'name'     => $item->name,
                'category' => $item->category->name ?? null,
                'qr'       => $qrCache[$code],
            ];
        };

        $labels = collect();

        foreach ($items as $item) {
            if ($mode === 'unit') {
                $count = $unitCount($item);

                for ($n = 1; $n <= $count; $n++) {
                    $labels->push($make($item, sprintf('%s-%03d', $item->code, $n)));
                }
            } else {
                for ($i = 0; $i < $copies; $i++) {
                    $labels->push($make($item, $item->code));
                }
            }
        }

        return view('items.labels', [
            'labels' => $labels,
            'items'  => $items,
            'ids'    => $data['ids'],
            'mode'   => $mode,
            'copies' => $copies,
        ]);
    }
}
