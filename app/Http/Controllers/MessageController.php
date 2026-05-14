<?php

namespace App\Http\Controllers;

use App\Models\Message; // PENTING: Untuk memanggil tabel pesan
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
{
    // Ambil semua user kecuali diri sendiri
    $users = \App\Models\User::where('id', '!=', Auth::user()->id)->get();
    
    return view('chat', compact('users'));
}
    public function store(Request $request)
{

    $request->validate([
            'receiver_id' => 'required',
            'message' => 'required',
    ]);

    $message = Message::create([
        'user_id' => Auth::id(),
        'receiver_id' => $request->receiver_id,
        'message' => $request->message,
    ]);

    broadcast(new \App\Events\MessageSent($message))->toOthers();

    return response()->json($message);
}
}

