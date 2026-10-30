<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ItemController extends Controller
{
    private const CONDITIONS = ['baik', 'rusak', 'perbaikan'];

    private function generateItemCode(): string
    {
        $year = date('Y');
        $prefix = 'BRG-' . $year . '-';

        $lastItem = Item::where('code', 'like', $prefix . '%')
            ->orderByDesc('code')
            ->value('code');

        $lastNumber = $lastItem
            ? (int) substr($lastItem, -4)
            : 0;

        $nextNumber = $lastNumber + 1;

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
    public function index(Request $request): View
    {
        $items = Item::query()
            ->with(['category', 'supplier'])
            ->when($request->search, function ($q, $search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($request->category_id, fn ($q, $id) => $q->where('category_id', $id))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('items.index', compact('items', 'categories'));
    }

    public function create(): View
    {
        return view('items.create', [
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'conditions' => self::CONDITIONS,
        ]);
    }
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'received_date' => ['required', 'date'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:20'],
            'stock' => ['required', 'integer', 'min:0'],
            'condition' => ['required', Rule::in(self::CONDITIONS)],
        ]);
        $validated['user_id'] = $request->user()->id;
        $validated['code'] = $this->generateItemCode();
        Item::create($validated);

        return redirect()
            ->route('items.index')
            ->with('success', 'Barang masuk berhasil dicatat.');
    }

    public function show(Item $item): View
    {
        $item->load(['category', 'supplier', 'receiver']);

        return view('items.show', compact('item'));
    }

    public function edit(Item $item): View
    {
        return view('items.edit', [
            'item' => $item,
            'categories' => Category::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'conditions' => self::CONDITIONS,
        ]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'received_date' => ['required', 'date'],
            'code' => ['required', 'string', 'max:50', Rule::unique('items', 'code')->ignore($item->id)],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit' => ['required', 'string', 'max:20'],
            'stock' => ['required', 'integer', 'min:0'],
            'condition' => ['required', Rule::in(self::CONDITIONS)],
        ]);

        $item->update($validated);

        return redirect()
            ->route('items.index')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        if ($item->loans()->exists()) {
            return redirect()
                ->route('items.index')
                ->with('error', 'Barang tidak bisa dihapus karena sudah punya riwayat peminjaman.');
        }

        $item->delete();

        return redirect()
            ->route('items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
    public function report(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
        ]);

        $items = Item::query()
            ->with(['category', 'supplier', 'receiver'])
            ->when($validated['from'] ?? null, fn ($q, $from) => $q->whereDate('received_date', '>=', $from))
            ->when($validated['to'] ?? null, fn ($q, $to) => $q->whereDate('received_date', '<=', $to))
            ->when($validated['supplier_id'] ?? null, fn ($q, $id) => $q->where('supplier_id', $id))
            ->orderBy('received_date', 'desc')
            ->get();

        $totalStock = $items->sum('stock');
        $suppliers = Supplier::orderBy('name')->get();

        return view('items.report', compact('items', 'totalStock', 'suppliers'));
    }
}
