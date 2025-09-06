import express from 'express';
import { createServer } from 'http';
import { Server } from 'socket.io';

const app = express();
app.use(express.json());

const server = createServer(app);
const io = new Server(server, {
    cors: { 
        origin: ['http://127.0.0.1:8001', 'http://localhost:8001', 'http://127.0.0.1:8080', 'http://localhost:8080'],
        methods: ['GET', 'POST'],
        credentials: true
    },
    allowEIO3: true,
    transports: ['websocket', 'polling']
});

const online = new Map();
const typingUsers = new Map();
const userSockets = new Map(); // Track user socket connections

io.on('connection', (socket) => {
    console.log('New connection:', socket.id);
    console.log('Socket transport:', socket.conn.transport.name);

    socket.on('join', (user_id) => {
        console.log('User joining:', user_id);
        socket.user_id = user_id;
        socket.join(`user_${user_id}`);
        
        // Track user socket
        if (!userSockets.has(user_id)) {
            userSockets.set(user_id, new Set());
        }
        userSockets.get(user_id).add(socket.id);
        
        // Update online status
        if (!online.has(user_id)) {
            online.set(user_id, new Set());
        }
        online.get(user_id).add(socket.id);
        
        // Notify others that user came online (only if this is their first connection)
        if (online.get(user_id).size === 1) {
            console.log(`User ${user_id} is now online`);
            io.emit('user-online', { user_id, timestamp: new Date().toISOString() });
        }

        // Send current online users to the new user
        socket.emit('online-users', Array.from(online.keys()));
        
        // Notify all users about current online status
        io.emit('online-status-update', {
            online_users: Array.from(online.keys()),
            timestamp: new Date().toISOString()
        });
    });

    socket.on('typing', (data) => {
        const { user_id, user_name, to_user_id } = data;
        console.log('Typing:', user_id, 'to', to_user_id);
        
        if (to_user_id && online.has(to_user_id)) {
            socket.to(`user_${to_user_id}`).emit('typing', { 
                user_id, 
                user_name,
                timestamp: new Date().toISOString()
            });
        }
    });

    socket.on('stop-typing', (data) => {
        const { user_id, to_user_id } = data;
        console.log('Stop typing:', user_id, 'to', to_user_id);
        
        if (to_user_id && online.has(to_user_id)) {
            socket.to(`user_${to_user_id}`).emit('stop-typing', { 
                user_id,
                timestamp: new Date().toISOString()
            });
        }
    });

    socket.on('message-read', (data) => {
        const { message_id, from_user_id, read_at } = data;
        console.log('Message read:', message_id, 'by', from_user_id);
        
        if (from_user_id && online.has(from_user_id)) {
            socket.to(`user_${from_user_id}`).emit('message-read', { 
                message_id,
                read_at: read_at || new Date().toISOString(),
                timestamp: new Date().toISOString()
            });
        }
    });

    socket.on('message-delivered', (data) => {
        const { message_id, from_user_id, delivered_at } = data;
        console.log('Message delivered:', message_id, 'to', from_user_id);
        
        if (from_user_id && online.has(from_user_id)) {
            socket.to(`user_${from_user_id}`).emit('message-delivered', { 
                message_id,
                delivered_at: delivered_at || new Date().toISOString(),
                timestamp: new Date().toISOString()
            });
        }
    });

    socket.on('get-online-status', (data) => {
        const { user_id } = data;
        if (user_id) {
            const isOnline = online.has(user_id) && online.get(user_id).size > 0;
            socket.emit('user-status', { 
                user_id, 
                is_online: isOnline,
                timestamp: new Date().toISOString()
            });
        }
    });

    socket.on('disconnect', () => {
        console.log('Disconnect:', socket.id);
        
        if (socket.user_id) {
            const userSockets = online.get(socket.user_id);
            if (userSockets) {
                userSockets.delete(socket.id);
                if (userSockets.size === 0) {
                    online.delete(socket.user_id);
                    console.log(`User ${socket.user_id} is now offline`);
                    io.emit('user-offline', { 
                        user_id: socket.user_id,
                        timestamp: new Date().toISOString()
                    });
                    
                    // Update online status for all users
                    io.emit('online-status-update', {
                        online_users: Array.from(online.keys()),
                        timestamp: new Date().toISOString()
                    });
                }
            }
        }
    });
});

app.post('/push', (req, res) => {
    const { room, event, data } = req.body;
    io.to(room).emit(event, data);
    res.send('ok');
});

server.listen(3000, () => {
    console.log('Socket.IO server running on port 3000');
    console.log('CORS enabled for:', ['http://127.0.0.1:8001', 'http://localhost:8001', 'http://127.0.0.1:8080', 'http://localhost:8080']);
});

server.on('error', (err) => {
    console.error('Server error:', err);
});

io.engine.on('connection_error', (err) => {
    console.log('Connection error:', err.req, err.code, err.message, err.context);
});