import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';

const app = express();
app.use(express.json());

const server = createServer(app);
const io = new Server(server, {
    cors: { origin: '*' }
});

const online = new Map();

io.on('connection', (socket) => {
    socket.on('join', (user_id) => {
        socket.user_id = user_id;
        socket.join(`user_${user_id}`);
        if (!online.has(user_id)) {
            online.set(user_id, new Set());
        }
        online.get(user_id).add(socket.id);
        if (online.get(user_id).size === 1) {
            io.emit('online', { user_id });
        }

        socket.emit('online-users', Array.from(online.keys()));
    });

    socket.on('disconnect', () => {
        if (socket.user_id) {
            const userSockets = online.get(socket.user_id);
            if (userSockets) {
                userSockets.delete(socket.id);
                if (userSockets.size === 0) {
                    online.delete(socket.user_id);
                    io.emit('offline', { user_id: socket.user_id });
                }
            }
        }
    });

    socket.on('message-read', (data) => {
        console.log('=== SOCKET.IO: Message read event received ===');
        console.log('From socket:', socket.user_id);
        console.log('Data:', data);
        console.log('Broadcasting to user:', data.to);
        io.to(`user_${data.to}`).emit('message-read', {
            message_id: data.message_id,
            from: data.from,
            to: data.to
        });
        console.log('Message read event broadcasted');
    });

    socket.on('message-delivered', (data) => {
        console.log('=== SOCKET.IO: Message delivered event received ===');
        console.log('From socket:', socket.user_id);
        console.log('Data:', data);
        console.log('Broadcasting to user:', data.to);
        io.to(`user_${data.to}`).emit('message-delivered', {
            message_id: data.message_id,
            from: data.from,
            to: data.to
        });
        console.log('Message delivered event broadcasted');
    });

    socket.on('conversation-read', (data) => {
        console.log('=== SOCKET.IO: Conversation read event received ===');
        console.log('From socket:', socket.user_id);
        console.log('Data:', data);
        console.log('Broadcasting to user:', data.to);
        io.to(`user_${data.to}`).emit('conversation-read', {
            from: data.from,
            to: data.to,
            conversation_id: data.conversation_id
        });
        console.log('Conversation read event broadcasted');
    });

    socket.on('typing', (data) => {
        console.log('Typing event received:', data);
        if (data.to && data.to !== data.user_id) {
            io.to(`user_${data.to}`).emit('typing', {
                user_id: data.user_id,
                user_name: data.user_name
            });
        }
    });

    socket.on('stop-typing', (data) => {
        console.log('Stop typing event received:', data);
        if (data.to && data.to !== data.user_id) {
            io.to(`user_${data.to}`).emit('stop-typing', {
                user_id: data.user_id
            });
        }
    });

    socket.on('check-status', (user_id) => {
        console.log('Check status event received for user:', user_id);
        const isOnline = online.has(user_id);
        socket.emit('user-status', {
            user_id: user_id,
            online: isOnline
        });
    });

    socket.on('test-event', (data) => {
        console.log('=== TEST EVENT RECEIVED ===');
        console.log('From socket:', socket.user_id);
        console.log('Data:', data);
        socket.emit('test-response', { message: 'Hello from server', timestamp: new Date() });
    });
});

app.post('/push', (req, res) => {
    const { room, event, data } = req.body;
    console.log('Push endpoint called:', { room, event, data });
    io.to(room).emit(event, data);
    res.send('ok');
});

server.listen(3000, () => {
    console.log('Socket.IO server running on port 3000');
});