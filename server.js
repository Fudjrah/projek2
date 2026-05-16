import { Server } from "socket.io";

const io = new Server(3000, {
    cors: {
        origin: "*",
    }
});

console.log("Server Chat Real-time AKTIF di port 3000...");

io.on('connection', (socket) => {
    console.log('User baru terhubung: ' + socket.id);

    socket.on('kirim-pesan', (data) => {
        console.log('Pesan masuk:', data);
        io.emit('terima-pesan', data);
    });

    socket.on('disconnect', () => {
        console.log('User keluar');
    });
});

io.on('connection', (socket) => {
    // Fitur Presence: Deteksi saat user masuk
    console.log('User terhubung: ' + socket.id);

    // Bergabung ke kamar grup
    socket.on('join-group', (groupId) => {
        socket.join(`group-${groupId}`);
        console.log(`User masuk ke grup: group-${groupId}`);
    });

    // Kirim pesan ke grup tertentu saja
    socket.on('kirim-pesan-grup', (data) => {
        // Mengirim ke semua orang di room 'group-ID'
        io.to(`group-${data.groupId}`).emit('terima-pesan-grup', data);
    });

    socket.on('disconnect', () => {
        console.log('User terputus');
    });
});