<!DOCTYPE html>
<html>
<head>
    <title>Chat Projek 2</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100 p-10">
     
</div>
    <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-5">
        <h2 class="text-xl font-bold mb-4">Chat Room (Socket.io)</h2>
        
        <!-- ID diubah jadi 'chatBox' agar sesuai dengan script di bawah -->
        <div id="chatBox" class="h-64 overflow-y-auto border p-3 mb-4 flex flex-col gap-2">
           <!-- Pesan muncul di sini -->
        </div>

        <div class="flex gap-2">
            <!-- ID diubah jadi 'inputPesan' agar sesuai dengan script di bawah -->
            <input type="text" id="inputPesan" class="border p-2 flex-1" placeholder="Tulis pesan...">
            <!-- Tambahkan onclick="kirimChat()" -->
            <button onclick="kirimChat()" class="bg-blue-500 text-white px-4 py-2">Kirim</button>
        </div>
    </div>

    <!-- Bagian Grup (Group Chat) -->
<div class="mt-4">
    <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">Grup Anda</h3>
    
    @isset($groups)
        @if(count($groups) > 0)
            @foreach($groups as $g)
                {{-- Gunakan alias $g agar tidak bentrok dengan variabel lain --}}
                <button type="button" 
                        onclick="pilihGrup('{{ $g->id }}', '{{ $g->name }}')" 
                        class="w-full text-left p-2 bg-blue-50 hover:bg-blue-100 rounded mb-1 text-blue-700">
                    # {{ $g->name }}
                </button>
            @endforeach
        @else
            <p class="text-gray-400 text-xs italic">Belum ada grup.</p>
        @endif
    @else
        <p class="text-red-400 text-xs italic">Variabel grup tidak terkirim.</p>
    @endisset
</div>

    <!-- Panggil library socket.io -->
    <script src="https://cdn.socket.io/4.8.3/socket.io.min.js"></script>

    <script>
        // Hubungkan ke server.js kamu
        const socket = io("http://localhost:3000");

        function kirimChat() {
            const input = document.getElementById('inputPesan');
            const pesan = input.value;
            const namaUser = "{{ Auth::user()->name ?? 'User' }}";

            if (pesan.trim() !== "") {
                // Kirim ke server.js
                socket.emit('kirim-pesan', {
                    nama: namaUser,
                    teks: pesan
                });
                input.value = '';
            }
        }

        // Terima dari server.js
        socket.on('terima-pesan', (data) => {
            const box = document.getElementById('chatBox');
            // Tambahkan pesan ke kotak chat
            const div = document.createElement('div');
            div.className = "bg-gray-200 p-2 rounded self-start mb-2";
            div.innerHTML = `<strong>${data.nama}:</strong> ${data.teks}`;
            box.appendChild(div);
            
            // Auto scroll ke bawah
            box.scrollTop = box.scrollHeight;
        });

        // Di dalam tag <script> chat.blade.php
        socket.on('connect', () => {
        socket.emit('user-online', {
        userId: "{{ Auth::id() }}",
        name: "{{ Auth::user()->name }}"
    });
});

// Terima status online dari orang lain
        socket.on('update-user-status', (users) => {
    // Logika untuk mengubah warna bulatan di sidebar menjadi hijau
         console.log("User yang sedang online:", users);
});
    </script>
</body>
</body>
</html>