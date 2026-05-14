<!DOCTYPE html>
<html>
<head>
    <title>Chat Projek 2</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body class="bg-gray-100 p-10">
    <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-5">
        <h2 class="text-xl font-bold mb-4">Chat Room</h2>
        
        <div id="chat-window" class="h-64 overflow-y-auto border p-3 mb-4 flex flex-col gap-2">
           
        </div>

        <div class="flex gap-2">
            <input type="text" id="message-input" class="border p-2 flex-1" placeholder="Tulis pesan...">
            <button id="send-button" class="bg-blue-500 text-white px-4 py-2">Kirim</button>
        </div>
    </div>

    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            const userId = "{{ auth()->id() }}";
            
            // Mendengarkan pesan (Real-time)
            window.Echo.private(`chat.${userId}`)
                .listen('MessageSent', (e) => {
                    const div = document.createElement('div');
                    div.className = "bg-gray-200 p-2 rounded self-start";
                    div.innerText = e.message.message;
                    document.getElementById('chat-window').appendChild(div);
                });

            // Kirim Pesan
            document.getElementById('send-button').onclick = function() {
                const text = document.getElementById('message-input').value;
                axios.post('/messages', {
                    receiver_id: 1, // Sementara kirim ke ID 1
                    message: text
                }).then(() => {
                    const div = document.createElement('div');
                    div.className = "bg-blue-500 text-white p-2 rounded self-end";
                    div.innerText = text;
                    document.getElementById('chat-window').appendChild(div);
                    document.getElementById('message-input').value = '';
                });
            };
        });
    </script>
</body>
</html>