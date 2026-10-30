<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_HANDED_OVER = 'handed_over';
    public const STATUS_RETURNED = 'returned';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'teacher_id',
        'item_id',
        'quantity',
        'loan_date',
        'created_by',
        'status',
        'status_note',
        'decided_by',
        'decided_at',
        'sarpras_id',
        'handover_letter_number',
        'handed_over_at',
        'returned_at',
        'returned_by',
        'return_note',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'loan_date' => 'date',
        'decided_at' => 'datetime',
        'handed_over_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /** Sarpras yang mencatat pengajuan guru */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Sarpras yang memutuskan ACC / tolak / pending */
    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    /** Sarpras yang menyerahkan barang */
    public function sarpras(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sarpras_id');
    }

    /** Sarpras yang menerima pengembalian barang */
    public function returner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'returned_by');
    }

    /** Semua peminjaman yang pernah diserahkan (masih dipinjam maupun sudah dikembalikan) */
    public function scopeHandedOver(Builder $query): Builder
    {
        return $query->whereNotNull('handed_over_at');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /** Barang sedang dipinjam guru (sudah diserahkan, belum kembali) */
    public function isHandedOver(): bool
    {
        return $this->status === self::STATUS_HANDED_OVER;
    }

    public function isReturned(): bool
    {
        return $this->status === self::STATUS_RETURNED;
    }
}
