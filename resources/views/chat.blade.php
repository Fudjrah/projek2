<!DOCTYPE html>
<html>
<head>
    <title>Chat Projek 2</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100 p-6">
     
    <div class="max-w-4xl mx-auto flex gap-4">
        
        <div class="w-1/3 bg-white shadow-lg rounded-lg p-4 flex flex-col gap-4">
            
            <div>
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">Teman Anda</h3>
                <div id="daftarTeman" class="flex flex-col gap-2">
                    @isset($users)
                        @foreach($users as $user)
                            @if($user->id !== Auth::id())
                            <div onclick="pilihTeman('{{ $user->id }}', '{{ $user->name }}')" class="flex items-center justify-between p-2 border-b text-sm cursor-pointer hover:bg-gray-100 rounded btn-nav-chat">
                                <span>{{ $user->name }}</span>
                                <span id="status-dot-user-{{ $user->id }}" class="status-dot w-3 h-3 rounded-full bg-gray-400"></span>
                            </div>
                            @endif
                        @endforeach
                    @endisset
                </div>
            </div>

            <div>
                <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">Grup Anda</h3>
                <div class="flex flex-col gap-1">
                    @isset($groups)
                        @if(count($groups) > 0)
                            @foreach($groups as $g)
                                <button type="button" 
                                        id="btn-group-{{ $g->id }}"
                                        onclick="pilihGrup('{{ $g->id }}', '{{ $g->name }}')" 
                                        class="group-btn btn-nav-chat w-full text-left p-2 bg-blue-50 hover:bg-blue-100 rounded text-blue-700 font-medium">
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
            </div>

        </div>

        <div class="w-2/3 bg-white shadow-lg rounded-lg p-5 flex flex-col">
            <h2 id="judulChatRoom" class="text-xl font-bold mb-4 text-gray-800">Selamat Datang</h2>
            
            <div id="chatBox" class="h-80 overflow-y-auto border p-3 mb-4 flex flex-col gap-2 bg-gray-50 rounded justify-center">
                </div>

            <div id="areaInput" class="flex gap-2 hidden">
                <input type="text" id="inputPesan" onkeypress="handleEnter(event)" class="border p-2 flex-1 rounded focus:outline-none focus:border-blue-500" placeholder="Tulis pesan...">
                <button onclick="kirimChat()" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2 rounded font-medium">Kirim</button>
            </div>
        </div>

    </div>

    <script src="https://cdn.socket.io/4.8.3/socket.io.min.js"></script>

    <script>
        // Hubungkan ke server Node.js kamu
        const socket = io('http://192.168.100.8:3000');

        // Ambil data user login dari Laravel
        const currentUserId = "{{ Auth::id() }}";
        const namaUser = "{{ Auth::user()->name ?? 'User' }}";

        // Variabel penanda room aktif
        let currentGroupId = null; 
        let currentReceiverId = null; 

        // KONDISI UTAMA: Saat halaman pertama kali di-load browser
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById('judulChatRoom').innerText = "Selamat Datang di Chat Aplikasi";
            
            // Set tampilan selamat datang di tengah chatbox
            document.getElementById('chatBox').innerHTML = `
                <div class="text-center text-gray-400 italic my-auto">
                    <p class="font-medium text-base text-gray-500">Belum ada obrolan aktif</p>
                    <p class="text-xs">Silakan klik nama teman atau grup di sidebar untuk mulai mengobrol.</p>
                </div>
            `;
            
            // Sembunyikan area input pesan agar tidak bisa dipakai chat global siluman
            document.getElementById('areaInput').classList.add('hidden');
        });

        // FUNGSI: Ketika mengklik nama teman untuk Chat Privat
        function pilihTeman(userId, userName) {
            currentReceiverId = userId;
            currentGroupId = null; // Matikan jalur grup

            // Buka kunci input pesan (Hapus class hidden)
            document.getElementById('areaInput').classList.remove('hidden');

            // Ubah judul room chat di UI
            document.getElementById('judulChatRoom').innerText = "Chat Privat dengan: " + userName;

            // Efek visual pembersih bg aktif (opsional)
            document.querySelectorAll('.btn-nav-chat').forEach(el => el.classList.remove('bg-blue-100', 'font-bold'));
            
            // Bersihkan kotak chat lama, kembalikan posisi flex dari center ke start
            const box = document.getElementById('chatBox');
            box.classList.remove('justify-center');
            box.innerHTML = '';

            // Beritahu Node.js kalau kita masuk ke jalur privat
            socket.emit('join-private', {
                user1: currentUserId,
                user2: currentReceiverId
            });
        }

        // FUNGSI: Ketika mengklik nama grup
        function pilihGrup(groupId, groupName) {
            currentGroupId = groupId;
            currentReceiverId = null; // Matikan jalur privat
            
            // Buka kunci input pesan (Hapus class hidden)
            document.getElementById('areaInput').classList.remove('hidden');

            document.getElementById('judulChatRoom').innerText = "Grup: " + groupName;
            
            const box = document.getElementById('chatBox');
            box.classList.remove('justify-center');
            box.innerHTML = '';

            // Beritahu Node.js untuk gabung ke kamar grup
            socket.emit('join-group', groupId);
        }

        // FUNGSI: Kirim pesan
        function kirimChat() {
            const input = document.getElementById('inputPesan');
            const pesan = input.value;

            if (pesan.trim() !== "") {
                const payload = {
                    nama: namaUser,
                    userId: currentUserId,
                    teks: pesan,
                    groupId: currentGroupId,
                    receiverId: currentReceiverId 
                };

                // Lempar ke server Node.js
                socket.emit('kirim-pesan', payload);
                input.value = '';
            }
        }

        // FUNGSI: Handle ketukan Enter di Keyboard
        function handleEnter(e) {
            if (e.key === 'Enter') {
                kirimChat();
            }
        }

        // =======================================================
        // LOGIKA MENERIMA PESAN (DIPERKETAT TOTAL)
        // =======================================================
        socket.on('terima-pesan', (data) => {
            
            // KONDISI 1: JIKA YANG MASUK ADALAH PESAN GRUP
            if (data.groupId) {
                // Blokir/Abaikan jika id grup pesan tidak cocok dengan grup yang sedang saya buka
                if (data.groupId !== currentGroupId) {
                    return; 
                }
            } 
            
            // KONDISI 2: JIKA YANG MASUK ADALAH PESAN PRIVAT
            else if (data.receiverId) {
                // Cek kecocokan aktor obrolan privat secara dua arah (Pengirim & Penerima harus sinkron)
                const apakahDariTemanSaya = (data.userId == currentReceiverId && data.receiverId == currentUserId);
                const apakahDariSayaSendiri = (data.userId == currentUserId && data.receiverId == currentReceiverId);

                // KUNCI: Jika saya tidak sedang membuka chat privat dengan orang itu, langsung BLOKIR pesan agar tidak nyasar ke room global!
                if (!apakahDariTemanSaya && !apakahDariSayaSendiri) {
                    return; 
                }
            }

            // Tampilkan balon chat jika berhasil lolos dari filter super ketat di atas
            const box = document.getElementById('chatBox');
            const div = document.createElement('div');
            
            if (data.userId == currentUserId) {
                div.className = "bg-blue-500 text-white p-2 rounded max-w-xs self-end mb-2 text-sm shadow-sm";
            } else {
                div.className = "bg-gray-200 text-gray-800 p-2 rounded max-w-xs self-start mb-2 text-sm shadow-sm";
            }

            div.innerHTML = `<strong>${data.nama}:</strong> ${data.teks}`;
            box.appendChild(div);
            box.scrollTop = box.scrollHeight;
        });

        // TRACKING STATUS ONLINE/OFFLINE
        socket.on('connect', () => {
            socket.emit('user-online', {
                userId: currentUserId,
                name: namaUser
            });
        });

        socket.on('update-user-status', (usersOnline) => {
            console.log("User yang sedang online saat ini:", usersOnline);

            // 1. Kembalikan semua bulatan ke abu-abu (offline)
            document.querySelectorAll('.status-dot').forEach(dot => {
                dot.classList.remove('bg-green-500');
                dot.classList.add('bg-gray-400');
            });

            // 2. Nyalakan hijau jika ID user terdaftar di objek online Node.js
            if (typeof usersOnline === 'object' && usersOnline !== null) {
                Object.keys(usersOnline).forEach(userId => {
                    let dot = document.getElementById(`status-dot-user-${userId}`);
                    if (dot) {
                        dot.classList.remove('bg-gray-400');
                        dot.classList.add('bg-green-500'); 
                    }
                });
            }
        });
    </script>
</body>
</html>