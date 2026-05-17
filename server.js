import { Server } from "socket.io";

const io = new Server(3000, {
    cors: {
        origin: "*",
    }
});

console.log("Server Chat Real-time AKTIF di port 3000...");

// Tempat menyimpan daftar user yang sedang online
// Formatnya nanti: { "ID_USER_LARAVEL": "ID_SOCKET" }
let usersOnline = {};

// CUKUP SATU GERBANG UTAMA DI SINI
io.on('connection', (socket) => {
    console.log('User baru terhubung dengan Socket ID: ' + socket.id);

    // ==========================================
    // 1. FITUR PRESENCE (TRACKING ONLINE/OFFLINE)
    // ==========================================
    socket.on('user-online', (data) => {
        // Simpan userId dari Laravel ke dalam properti socket agar mudah dibaca saat disconnect
        socket.userId = data.userId;
        
        // Catat di object penampung
        usersOnline[data.userId] = socket.id;
        
        console.log(`User ${data.name} (ID: ${data.userId}) sekarang ONLINE.`);
        
        // Kirim daftar user online terbaru ke SEMUA browser yang sedang aktif
        io.emit('update-user-status', usersOnline);
    });


    // ==========================================
    // 2. FITUR CHAT PRIVAT / GLOBAL (FITUR AWAL KAMU)
    // ==========================================
   socket.on('kirim-pesan', (data) => {
    console.log('Pesan masuk:', data);
    
    if (data.groupId) {
        // Kondisi 1: Kirim ke Room Grup
        io.to(`group-${data.groupId}`).emit('terima-pesan', data);
    } else if (data.receiverId) {
        // Kondisi 2: Chat Privat (Kirim HANYA ke pengirim dan penerima saja)
        const targetSocketId = usersOnline[data.receiverId]; // Ambil socket ID teman
        const mySocketId = usersOnline[data.userId];       // Ambil socket ID saya sendiri

        if (targetSocketId) {
            // Jika teman sedang online, kirim ke dia
            io.to(targetSocketId).emit('terima-pesan', data);
        }
        // Kirim juga ke diri saya sendiri agar chat yang saya ketik muncul di layar saya
        if (mySocketId) {
            io.to(mySocketId).emit('terima-pesan', data);
        }
    } else {
        // Kondisi 3: Chat Global lama (jika tidak ada grup dan tidak ada target user)
        io.emit('terima-pesan', data);
    }
});


    // ==========================================
    // 3. FITUR GROUP CHAT (GABUNG ROOM GRUP)
    // ==========================================
    socket.on('join-group', (groupId) => {
        // Keluar dari room grup sebelumnya jika user berpindah grup (biar gak dobel chat)
        if (socket.currentRoom) {
            socket.leave(socket.currentRoom);
        }

        // Daftarkan socket user ke room khusus grup ini
        socket.join(`group-${groupId}`);
        socket.currentRoom = `group-${groupId}`;
        
        console.log(`Socket ${socket.id} masuk ke kamar: group-${groupId}`);
    });


    // ==========================================
    // 4. FITUR DISCONNECT (OTOMATIS OFFLINE)
    // ==========================================
    socket.on('disconnect', () => {
        console.log(`User dengan Socket ID ${socket.id} terputus/keluar.`);
        
        // Jika user tersebut punya userId (sudah kirim event user-online sebelumnya)
        if (socket.userId) {
            // Hapus dari daftar user online
            delete usersOnline[socket.userId];
            
            // Beritahu semua orang kalau user ini sudah OFFLINE
            io.emit('update-user-status', usersOnline);
        }
    });
});