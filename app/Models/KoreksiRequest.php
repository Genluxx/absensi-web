<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KoreksiRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'presensi_id', 'mandor_id', 'status_baru', 'keterangan_baru',
        'alasan', 'status', 'approved_by', 'catatan_admin',
    ];

    public function presensi(): BelongsTo
    {
        return $this->belongsTo(Presensi::class, 'presensi_id');
    }

    public function mandor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mandor_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}