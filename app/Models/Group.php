<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'created_id'];

    // Anggota di dalam grup ini
   public function users()
    {
        return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id');
    }

    // Pesan-pesan yang ada di grup ini
    public function messages()
    {
       return $this->hasMany(Message::class);
    }
}