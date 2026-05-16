<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    // Field yang boleh diisi (sesuaikan dengan migration tadi)
    protected $fillable = ['name', 'description', 'created_by'];

    /**
     * Relasi Balik: Mengetahui siapa saja anggota di grup ini
     */
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members');
    }

    /**
     * Relasi ke Pesan: Mengambil semua chat yang ada di grup ini
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
