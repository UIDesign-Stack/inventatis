<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Loan;
use App\Models\Teacher;
use App\Services\HandoverLetterNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use RuntimeException;

class LoanController extends Controller
{
    public const STATUSES = [
        Loan::STATUS_PENDING => 'Pending',
        Loan::STATUS_APPROVED => 'Disetujui',
        Loan::STATUS_REJECTED => 'Ditolak',
        Loan::STATUS_HANDED_OVER => 'Dipinjam',
        Loan::STATUS_RETURNED => 'Dikembalikan',
        Loan::STATUS_CANCELLED => 'Dibatalkan',
    ];

    public function __construct()
    {
        $this->middleware('permission:loan.view')->only(['index', 'show', 'returns']);
        $this->middleware('permission:loan.create')->only(['create', 'store', 'destroy', 'returnLoan']);
        $this->middleware('permission:loan.approve')->only(['decide', 'cancel']);
        $this->middleware('permission:loan.handover')->only(['handover']);
        $this->middleware('permission:stock-out.report')->only(['report']);
    }

    public function index(Request $request): View
    {
        $loans = Loan::query()
            ->with(['teacher', 'item'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->whereHas('teacher', fn ($t) => $t->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('item', fn ($i) => $i->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%"));
                });
            })
            ->latest('loan_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $statuses = self::STATUSES;

        return view('loans.index', compact('loans', 'statuses'));
    }

    public function create(): View
    {
        return view('loans.create', [
            'teachers' => Teacher::orderBy('name')->get(),
            'items' => Item::orderBy('name')->get(),
        ]);
    }

    /** Sarpras mencatat pengajuan guru; status awal selalu pending */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'item_id' => ['required', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'loan_date' => ['required', 'date'],
        ]);

        $loan = Loan::create($validated + [
            'created_by' => $request->user()->id,
            'status' => Loan::STATUS_PENDING,
        ]);

        return redirect()
            ->route('loans.show', $loan)
            ->with('success', 'Pengajuan peminjaman berhasil dicatat. Silakan putuskan ACC, tolak, atau pending.');
    }

    public function show(Loan $loan): View
    {
        $loan->load(['teacher', 'item', 'creator', 'decider', 'sarpras', 'returner']);

        $statuses = self::STATUSES;

        return view('loans.show', compact('loan', 'statuses'));
    }

    /** Sarpras memutuskan: approved / rejected / pending. Jika disetujui, stok langsung dikurangi */
    public function decide(Request $request, Loan $loan): RedirectResponse
    {
        $validated = $request->validate([
            'decision' => ['required', Rule::in([
                Loan::STATUS_APPROVED,
                Loan::STATUS_REJECTED,
                Loan::STATUS_PENDING,
            ])],
            'status_note' => ['nullable', 'string', 'max:500', 'required_if:decision,rejected,pending'],
        ], [
            'status_note.required_if' => 'Alasan wajib diisi jika peminjaman ditolak atau di-pending.',
        ]);

        try {
            DB::transaction(function () use ($loan, $validated, $request) {
                // kunci baris supaya dua ACC bersamaan tidak lolos dengan stok yang sama
                $loan = Loan::lockForUpdate()->findOrFail($loan->id);

                if ($loan->status !== Loan::STATUS_PENDING) {
                    throw new RuntimeException('Keputusan hanya bisa diberikan pada peminjaman yang berstatus pending.');
                }

                if ($validated['decision'] === Loan::STATUS_APPROVED) {
                    $item = Item::lockForUpdate()->findOrFail($loan->item_id);

                    if ($item->stock < $loan->quantity) {
                        throw new RuntimeException(
                            "Stok {$item->name} tidak cukup (tersisa {$item->stock} {$item->unit}). Tolak atau pending peminjaman ini."
                        );
                    }

                    $item->decrement('stock', $loan->quantity);
                }

                $loan->update([
                    'status' => $validated['decision'],
                    'status_note' => $validated['status_note'] ?? null,
                    'decided_by' => $request->user()->id,
                    'decided_at' => now(),
                ]);
            });
        } catch (RuntimeException $e) {
            return redirect()
                ->route('loans.show', $loan)
                ->with('error', $e->getMessage());
        }

        $message = match ($validated['decision']) {
            Loan::STATUS_APPROVED => 'Peminjaman disetujui dan stok barang telah dikurangi. Silakan lanjutkan dengan serah terima.',
            Loan::STATUS_REJECTED => 'Peminjaman ditolak.',
            default => 'Peminjaman tetap pending.',
        };

        return redirect()->route('loans.show', $loan)->with('success', $message);
    }

    /** Sarpras menyerahkan barang; nomor surat dibuat otomatis (stok sudah dikurangi saat ACC) */
    public function handover(Request $request, Loan $loan, HandoverLetterNumber $numbers): RedirectResponse
    {
        try {
            DB::transaction(function () use ($loan, $request, $numbers) {
                $loan = Loan::lockForUpdate()->findOrFail($loan->id);

                if ($loan->status !== Loan::STATUS_APPROVED) {
                    throw new RuntimeException('Hanya peminjaman yang sudah disetujui yang bisa diserahkan.');
                }

                $loan->update([
                    'status' => Loan::STATUS_HANDED_OVER,
                    'sarpras_id' => $request->user()->id,
                    'handover_letter_number' => $numbers->generate(now()),
                    'handed_over_at' => now(),
                ]);
            });
        } catch (RuntimeException $e) {
            return redirect()
                ->route('loans.show', $loan)
                ->with('error', $e->getMessage());
        }

        $loan->refresh();

        return redirect()
            ->route('loans.show', $loan)
            ->with('success', "Serah terima berhasil dicatat dengan nomor surat {$loan->handover_letter_number}.");
    }

    /** Daftar peminjaman yang disetujui / sedang dipinjam dan belum dikembalikan */
    public function returns(Request $request): View
    {
        $returnableStatuses = [Loan::STATUS_APPROVED, Loan::STATUS_HANDED_OVER];

        $loans = Loan::query()
            ->with(['teacher', 'item'])
            ->whereIn('status', $returnableStatuses)
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->whereHas('teacher', fn ($t) => $t->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('item', fn ($i) => $i->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%"));
                });
            })
            ->orderBy('loan_date')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        // semua yang bisa dikembalikan (tanpa pagination) untuk pilihan di form "Catat Pengembalian"
        $returnable = Loan::query()
            ->with(['teacher', 'item'])
            ->whereIn('status', $returnableStatuses)
            ->orderBy('loan_date')
            ->orderBy('id')
            ->get();

        return view('loans.returns', compact('loans', 'returnable'));
    }

    /** Barang dikembalikan guru; stok bertambah lagi */
    public function returnLoan(Request $request, Loan $loan): RedirectResponse
    {
        $validated = $request->validate([
            'return_note' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($loan, $validated, $request) {
                $loan = Loan::lockForUpdate()->findOrFail($loan->id);

                // stok sudah dikurangi sejak ACC, jadi disetujui maupun dipinjam aman dikembalikan
                if (! in_array($loan->status, [Loan::STATUS_APPROVED, Loan::STATUS_HANDED_OVER], true)) {
                    throw new RuntimeException('Hanya peminjaman yang disetujui atau sedang dipinjam yang bisa dikembalikan.');
                }

                Item::lockForUpdate()->findOrFail($loan->item_id)->increment('stock', $loan->quantity);

                $loan->update([
                    'status' => Loan::STATUS_RETURNED,
                    'returned_at' => now(),
                    'returned_by' => $request->user()->id,
                    'return_note' => $validated['return_note'] ?? null,
                ]);
            });
        } catch (RuntimeException $e) {
            return redirect()
                ->route('loans.show', $loan)
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('loans.returns')
            ->with('success', 'Pengembalian berhasil dicatat dan stok barang telah ditambahkan kembali.');
    }

    /** Batalkan peminjaman yang sudah di-ACC tapi belum diserahkan; stok dikembalikan */
    public function cancel(Request $request, Loan $loan): RedirectResponse
    {
        $validated = $request->validate([
            'status_note' => ['required', 'string', 'max:500'],
        ], [
            'status_note.required' => 'Alasan pembatalan wajib diisi.',
        ]);

        try {
            DB::transaction(function () use ($loan, $validated) {
                $loan = Loan::lockForUpdate()->findOrFail($loan->id);

                if ($loan->status !== Loan::STATUS_APPROVED) {
                    throw new RuntimeException('Hanya peminjaman yang sudah disetujui dan belum diserahkan yang bisa dibatalkan.');
                }

                Item::lockForUpdate()->findOrFail($loan->item_id)->increment('stock', $loan->quantity);

                $loan->update([
                    'status' => Loan::STATUS_CANCELLED,
                    'status_note' => $validated['status_note'],
                ]);
            });
        } catch (RuntimeException $e) {
            return redirect()
                ->route('loans.show', $loan)
                ->with('error', $e->getMessage());
        }

        return redirect()
            ->route('loans.show', $loan)
            ->with('success', 'Peminjaman dibatalkan dan stok barang telah dikembalikan.');
    }

    /** Hanya pengajuan yang belum diproses / ditolak / dibatalkan yang boleh dihapus */
    public function destroy(Loan $loan): RedirectResponse
    {
        $deletable = [Loan::STATUS_PENDING, Loan::STATUS_REJECTED, Loan::STATUS_CANCELLED];

        if (! in_array($loan->status, $deletable, true)) {
            return redirect()
                ->route('loans.index')
                ->with('error', 'Peminjaman yang sudah disetujui, dipinjam, atau dikembalikan tidak bisa dihapus.');
        }

        $loan->delete();

        return redirect()
            ->route('loans.index')
            ->with('success', 'Data peminjaman berhasil dihapus.');
    }

    /**
     * Laporan peminjaman (bisa dicetak).
     * status = "decided" menampilkan semua yang disetujui + ditolak.
     */
    public function report(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'status' => ['nullable', Rule::in([...array_keys(self::STATUSES), 'decided'])],
        ]);

        $status = $validated['status'] ?? null;

        $loans = Loan::query()
            ->with(['teacher', 'item.category', 'creator', 'decider', 'sarpras'])
            ->when(
                $validated['from'] ?? null,
                fn ($q, $from) => $q->whereRaw('DATE(COALESCE(decided_at, loan_date)) >= ?', [$from])
            )
            ->when(
                $validated['to'] ?? null,
                fn ($q, $to) => $q->whereRaw('DATE(COALESCE(decided_at, loan_date)) <= ?', [$to])
            )
            ->when(
                $validated['teacher_id'] ?? null,
                fn ($q, $id) => $q->where('teacher_id', $id)
            )
            ->when($status, function ($q, $status) {
                if ($status === 'decided') {
                    return $q->whereIn('status', [Loan::STATUS_APPROVED, Loan::STATUS_REJECTED]);
                }

                return $q->where('status', $status);
            })
            ->orderByDesc('loan_date')
            ->orderByDesc('id')
            ->get();

        // jumlah data per status
        $summary = $loans->countBy('status');

        // total barang hanya dihitung dari peminjaman yang disetujui (bukan pending / ditolak / batal)
        $totalQuantity = $loans
            ->whereIn('status', [
                Loan::STATUS_APPROVED,
                Loan::STATUS_HANDED_OVER,
                Loan::STATUS_RETURNED,
            ])
            ->sum('quantity');

        $teachers = Teacher::orderBy('name')->get();
        $statuses = self::STATUSES;

        return view('loans.report', compact('loans', 'summary', 'totalQuantity', 'teachers', 'statuses'));
    }
}
