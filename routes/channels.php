<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

use App\Models\Group;

// 1. Otorisasi untuk Chat Grup
Broadcast::channel('group.{groupId}', function ($user, $groupId) {
    // User hanya boleh dengerin/gabung ke channel grup jika mereka terdaftar di tabel group_members
    return $user->groups()->where('group_id', $groupId)->exists();
});

// 2. Otorisasi untuk Tracking Status Online/Offline
Broadcast::channel('online-users', function ($user) {
    // Presence channel harus mengembalikan array data user (bukan sekadar true/false)
    return [
        'id' => $user->id,
        'name' => $user->name,
    ];
});