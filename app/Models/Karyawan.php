<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Karyawan extends Model
{
    use HasFactory;

    protected $table = 'karyawan';

    protected $fillable = [
        'nik', 'nama', 'jabatan', 'tipe', 'lokasi', 'mandor_id',
    ];

    public function mandor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mandor_id');
    }

    public function presensi(): HasMany
    {
        return $this->hasMany(Presensi::class, 'karyawan_id');
    }
}