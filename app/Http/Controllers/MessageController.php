<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Models\Group; // <--- 1. WAJIB IMPORT MODEL GROUP DI SINI
use Illuminate\Support\Facades\Auth;
use App\Events\GroupMessageSent;
class MessageController extends Controller
{
    public function index()
    {
        // 1. Ambil semua data user lain untuk daftar teman
        $users = User::where('id', '!=', Auth::id())->get();

        // 2. Ambil semua data grup dari database
        $groups = Group::all();

        // 3. Melempar data ke view chat.blade.php
        return view('chat', compact('users', 'groups'));
    }

    public function store(Request $request)
    {
        $message = Message::create([
            'sender_id'   => Auth::id(), 
            'receiver_id' => $request->receiver_id,
            'group_id'    => $request->group_id,
            'message'     => $request->message
        ]);

        if ($message->group_id) {
            event(new GroupMessageSent($message));
        }

        return response()->json(['success' => true, 'message' => $message]);
    }

    public function sendGroupMessage(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'message'  => 'required|string',
        ]);

        $message = Message::create([
            'sender_id'   => Auth::id(),
            'group_id'    => $request->group_id,
            'receiver_id' => null, 
            'message'     => $request->message,
        ]);

        broadcast(new GroupMessageSent($message))->toOthers();

        return response()->json([
            'status' => 'Pesan grup terkirim!',
            'data'   => $message
        ]);
    }

    public function getGroupMessages($groupId)
    {
        // Ambil pesan berdasarkan group_id, urutkan dari yang tertua ke terbaru
        // Kita muat juga relasi 'sender' untuk tahu siapa yang mengirim pesan tersebut
        $messages = Message::with('sender')
                            ->where('group_id', $groupId)
                            ->orderBy('created_at', 'asc')
                            ->get();

        return response()->json($messages);
    }
}