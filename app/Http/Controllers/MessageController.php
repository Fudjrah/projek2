<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Jika belum login, tendang ke halaman login
        if (!$user) {
            return redirect()->route('login');
        }

        // Ambil user lain untuk daftar teman
        $users = User::where('id', '!=', $user->id)->get();
        
        // Kita isi array kosong dulu untuk grup agar chat.blade tidak error variabel
        $groups = []; 

        return view('chat', compact('users', 'groups'));
    }

    public function store(Request $request)
    {
        $message = Message::create([
            'user_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message
        ]);

        return response()->json(['success' => true, 'message' => $message]);
    }
}