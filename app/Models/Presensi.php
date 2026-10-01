<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'karyawan_id', 'tanggal', 'jam_masuk', 'status',
        'keterangan', 'foto_path', 'latitude', 'longitude', 'lokasi_gps', 'mandor_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getTerlambatAttribute(): bool
    {
        return in_array($this->status, ['Hadir', 'Telat'], true)
            && $this->jam_masuk
            && $this->jam_masuk > '08:00:00';
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }

    public function mandor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'mandor_id');
    }
}