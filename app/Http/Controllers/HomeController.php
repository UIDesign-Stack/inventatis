<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /** Stok dianggap menipis jika sama dengan atau di bawah angka ini */
    private const LOW_STOCK = 5;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $inventory = null;
        $loans = null;

        // Ringkasan stok: untuk yang mengelola data barang (operator)
        if ($user->can('item.manage')) {
            $inventory = [
                'stats' => [
                    [
                        'label' => 'Jenis barang',
                        'value' => Item::count(),
                        'note' => 'Record barang tercatat',
                    ],
                    [
                        'label' => 'Stok tersedia',
                        'value' => (int) Item::sum('stock'),
                        'note' => 'Total stok di gudang saat ini',
                    ],
                    [
                        'label' => 'Barang masuk bulan ini',
                        'value' => Item::whereBetween('received_date', [$monthStart->toDateString(), $monthEnd->toDateString()])->count(),
                        'note' => 'Record barang masuk',
                    ],
                    [
                        'label' => 'Stok menipis',
                        'value' => Item::where('stock', '<=', self::LOW_STOCK)->count(),
                        'note' => 'Stok '.self::LOW_STOCK.' atau kurang',
                    ],
                    [
                        'label' => 'Rusak / perbaikan',
                        'value' => Item::whereIn('condition', ['rusak', 'perbaikan'])->count(),
                        'note' => 'Barang perlu perhatian',
                    ],
                ],
                'latest' => Item::with('category')
                    ->orderByDesc('received_date')
                    ->orderByDesc('id')
                    ->limit(5)
                    ->get(),
                'low' => Item::where('stock', '<=', self::LOW_STOCK)
                    ->orderBy('stock')
                    ->orderBy('name')
                    ->limit(5)
                    ->get(),
                'threshold' => self::LOW_STOCK,
            ];
        }

        // Ringkasan peminjaman: untuk yang menangani peminjaman (sarpras)
        if ($user->can('loan.view')) {
            $borrowedStatuses = [Loan::STATUS_APPROVED, Loan::STATUS_HANDED_OVER];

            $loans = [
                'stats' => [
                    [
                        'label' => 'Menunggu keputusan',
                        'value' => Loan::where('status', Loan::STATUS_PENDING)->count(),
                        'note' => 'Pengajuan berstatus pending',
                    ],
                    [
                        'label' => 'Sedang dipinjam',
                        'value' => Loan::whereIn('status', $borrowedStatuses)->count(),
                        'note' => 'Belum dikembalikan',
                    ],
                    [
                        'label' => 'Dikembalikan bulan ini',
                        'value' => Loan::whereBetween('returned_at', [$monthStart, $monthEnd])->count(),
                        'note' => 'Transaksi pengembalian',
                    ],
                    [
                        'label' => 'Barang keluar bulan ini',
                        'value' => (int) Loan::whereBetween('handed_over_at', [$monthStart, $monthEnd])->sum('quantity'),
                        'note' => 'Jumlah unit diserahkan',
                    ],
                ],
                'pending' => Loan::with(['teacher', 'item'])
                    ->where('status', Loan::STATUS_PENDING)
                    ->orderBy('loan_date')
                    ->orderBy('id')
                    ->limit(5)
                    ->get(),
                'borrowed' => Loan::with(['teacher', 'item'])
                    ->whereIn('status', $borrowedStatuses)
                    ->orderBy('handed_over_at')
                    ->orderBy('id')
                    ->limit(5)
                    ->get(),
            ];
        }

        return view('home', compact('inventory', 'loans'));
    }
}
