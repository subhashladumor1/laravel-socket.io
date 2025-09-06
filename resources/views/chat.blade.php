@extends('layouts.app')
@section('content')
<div class="container-fluid p-0">
    <div class="row g-0">
        <!-- Sidebar: User Chat List -->
        <div class="col-md-4 col-lg-3">
            <div class="user-list">
                <!-- Header -->
                <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="bi bi-chat-dots me-2"></i>Chats
                        <span id="connectionStatus" class="badge bg-success ms-2">Connected</span>
                    </h5>
                    <button class="btn btn-outline-secondary btn-sm d-md-none" id="toggleUserList">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
                
                <!-- User List -->
                <div class="list-group list-group-flush" id="userList">
                    @foreach(App\Models\User::where('id', '!=', Auth::id())->orderBy('name')?->get() as $user)
                        <div class="list-group-item list-group-item-action room-item p-3" data-user-id="{{ $user->id }}">
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3 position-relative">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                    <div class="online-indicator" id="online-indicator-{{ $user->id }}" style="display: none;"></div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1 fw-semibold">{{ $user->name }}</h6>
                                        <div class="d-flex align-items-center">
                                            <small class="room-time me-2" id="last-message-time-{{ $user->id }}">--:--</small>
                                            <span class="unread-badge" id="unread-count-{{ $user->id }}" style="display: none;">0</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="room-preview" id="last-message-{{ $user->id }}">No messages yet</small>
                                        <small class="user-status status-offline" data-user-id="{{ $user->id }}">
                                            <i class="bi bi-circle-fill me-1"></i>Offline
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Chat Room -->
        <div class="col-md-8 col-lg-9">
            <div class="chat-container h-100 d-flex flex-column">
                <!-- Chat Header -->
                <div id="chatHeader" class="chat-header d-none">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-light btn-sm me-3 d-md-none" id="backToUsers">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <div class="user-avatar me-3">
                            <span id="selectedUserInitial"></span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold" id="selectedUserName"></h6>
                            <small id="selectedUserStatus" class="status-offline">
                                <i class="bi bi-circle-fill me-1"></i>Offline
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Messages Container -->
                <div id="messages" class="message-container flex-grow-1">
                    <div id="noChatSelected" class="no-chat-selected">
                        <div>
                            <i class="bi bi-chat-square-text"></i>
                            <h5 class="mt-3">Select a conversation</h5>
                            <p class="text-muted">Choose a user from the sidebar to start chatting</p>
                        </div>
                    </div>
                    <div id="typingIndicator" class="typing-indicator" style="display: none;">
                        <span id="typingUser"></span> is typing
                        <div class="typing-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>

                <!-- Message Input -->
                <div id="messageInputContainer" class="message-input-container d-none">
                    <div class="input-group">
                        <input type="text" id="messageInput" class="form-control form-control-lg" 
                               placeholder="Type a message..." autocomplete="off">
                        <button class="btn btn-primary btn-lg" type="button" id="sendButton">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

  @endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.socket.io/4.8.1/socket.io.min.js"></script>

<script>
const userId = {{ Auth::id() }};
const apiToken = '{{ session('api_token') }}';
const myName = "{{ Auth::user()?->name }}";
let otherId = null;
const baseUrl = 'http://127.0.0.1:8001';

// DOM Elements
const messagesElement = document.getElementById('messages');
const messageInput = document.getElementById('messageInput');
const sendButton = document.getElementById('sendButton');
const chatHeader = document.getElementById('chatHeader');
const selectedUserName = document.getElementById('selectedUserName');
const selectedUserInitial = document.getElementById('selectedUserInitial');
const selectedUserStatus = document.getElementById('selectedUserStatus');
const userList = document.getElementById('userList');
const noChatSelected = document.getElementById('noChatSelected');
const messageInputContainer = document.getElementById('messageInputContainer');
const toggleUserList = document.getElementById('toggleUserList');
const backToUsers = document.getElementById('backToUsers');
const typingIndicator = document.getElementById('typingIndicator');
const typingUser = document.getElementById('typingUser');
const connectionStatus = document.getElementById('connectionStatus');

// State management
let typingTimer = null;
let isTyping = false;
let lastMessageTimes = {};
let unreadCounts = {};
let lastMessages = {};

console.log('API Token:', apiToken);
console.log('Base URL:', baseUrl);

if (!apiToken) {
    alert('Authentication token missing. Please log in again.');
    window.location.href = '{{ route('login') }}';
}

const socket = io('http://127.0.0.1:3000', { 
    transports: ['websocket', 'polling'],
    autoConnect: true,
    reconnection: true,
    reconnectionDelay: 1000,
    reconnectionAttempts: 10,
    timeout: 20000,
    forceNew: true
});

socket.on('connect', () => {
    console.log('Socket connected');
    if (connectionStatus) {
        connectionStatus.textContent = 'Connected';
        connectionStatus.className = 'badge bg-success ms-2';
    }
    socket.emit('join', userId);

    // Fetch pending messages
    fetch(`${baseUrl}/api/pending`, {
        headers: { 'Authorization': `Bearer ${apiToken}` }
    }).then(res => {
        if (!res.ok) throw new Error(`Pending failed: ${res.status} ${res.statusText}`);
        return res.json();
    }).then(msgs => {
        msgs.forEach(msg => appendMessage(msg.from === userId ? 'sent' : 'received', msg.message, msg.from_name, new Date()));
    }).catch(err => console.error('Pending fetch error:', err));

    // Initialize all users as offline initially
    // Real-time status will be updated via Socket.IO events
    const allUserElements = document.querySelectorAll('.room-item');
    allUserElements.forEach(element => {
        const userId = parseInt(element.dataset.userId);
        updateUserStatus(userId, false);
    });
});

socket.on('disconnect', (reason) => {
    console.log('Socket disconnected:', reason);
    if (connectionStatus) {
        connectionStatus.textContent = 'Disconnected';
        connectionStatus.className = 'badge bg-danger ms-2';
    }
    showNotification('Connection lost. Attempting to reconnect...');
});

socket.on('reconnect', (attemptNumber) => {
    console.log('Socket reconnected after', attemptNumber, 'attempts');
    if (connectionStatus) {
        connectionStatus.textContent = 'Connected';
        connectionStatus.className = 'badge bg-success ms-2';
    }
    showNotification('Reconnected successfully!');
    socket.emit('join', userId);
});

socket.on('reconnect_error', (error) => {
    console.log('Reconnection error:', error);
});

socket.on('reconnect_failed', () => {
    console.log('Reconnection failed');
    showNotification('Failed to reconnect. Please refresh the page.');
});

socket.on('new-message', (data) => {
    console.log('New message received:', data);
    
    if (data.from === otherId || data.from === userId) {
        const messageClass = data.from === userId ? 'sent' : 'received';
        const senderName = data.from === userId ? myName : data.from_name;
        const timestamp = new Date(data.timestamp || Date.now());
        
        appendMessage(messageClass, data.message, senderName, timestamp, data.id, false, null);
        
        // Update last message for the room
        updateLastMessage(data.from === userId ? data.to : data.from, data.message, timestamp);
        
        // Mark as delivered if it's our message
        if (data.from === userId) {
            // Emit delivery receipt
            socket.emit('message-delivered', {
                message_id: data.id,
                from_user_id: data.to,
                delivered_at: new Date().toISOString()
            });
            
            // Update message status to delivered
            setTimeout(() => {
                updateMessageStatus(data.id, 'delivered');
            }, 1000);
        } else {
            // Mark as read if it's from the current conversation
            if (data.from === otherId) {
                markMessageAsRead(data.id);
            } else {
                // Increment unread count for other conversations
                incrementUnreadCount(data.from);
            }
        }
    }
});

socket.on('user-online', (data) => {
    console.log("User came online:", data);
    updateUserStatus(data.user_id, true);
    showNotification(`${getUserName(data.user_id)} is now online`);
});

socket.on('user-offline', (data) => {
    console.log("User went offline:", data);
    updateUserStatus(data.user_id, false);
    showNotification(`${getUserName(data.user_id)} is now offline`);
});

// Debug: Log all socket events
socket.onAny((eventName, ...args) => {
    console.log(`Socket event: ${eventName}`, args);
});

socket.on('online-users', (users) => {
    console.log("Online users:", users);
    updateAllStatuses(users);
});

socket.on('online-status-update', (data) => {
    console.log("Online status update:", data);
    updateAllStatuses(data.online_users);
});

socket.on('user-status', (data) => {
    console.log("User status:", data);
    updateUserStatus(data.user_id, data.is_online);
});

socket.on('typing', (data) => {
    if (data.user_id === otherId) {
        showTypingIndicator(data.user_name);
    }
});

socket.on('stop-typing', (data) => {
    if (data.user_id === otherId) {
        hideTypingIndicator();
    }
});

socket.on('message-read', (data) => {
    console.log('Message read:', data);
    updateMessageStatus(data.message_id, 'read');
});

socket.on('message-delivered', (data) => {
    console.log('Message delivered:', data);
    updateMessageStatus(data.message_id, 'delivered');
});

// Handle user selection
userList.querySelectorAll('.room-item').forEach(item => {
    item.addEventListener('click', () => {
        otherId = parseInt(item.dataset.userId);
        if (!otherId) return;

        // Update UI
        userList.querySelectorAll('.room-item').forEach(i => i.classList.remove('active'));
        item.classList.add('active');
        
        // Show chat interface
        chatHeader.classList.remove('d-none');
        noChatSelected.classList.add('d-none');
        messageInputContainer.classList.remove('d-none');
        
        // Update header
        const userName = item.querySelector('h6').textContent;
        selectedUserName.textContent = userName;
        selectedUserInitial.textContent = userName.charAt(0).toUpperCase();
        
        // Clear messages and fetch new ones
        messagesElement.innerHTML = '';
        
        // Online status will be updated via real-time Socket.IO events
        // No need to manually check status when selecting a user
        
        // Fetch messages
        fetch(`${baseUrl}/api/messages?with=${otherId}`, {
            headers: { 'Authorization': `Bearer ${apiToken}` }
        }).then(res => {
            if (!res.ok) throw new Error(`Messages failed: ${res.status} ${res.statusText}`);
            return res.json();
        }).then(msgs => {
            msgs.forEach(msg => {
                const messageClass = msg.is_me ? 'sent' : 'received';
                const senderName = msg.is_me ? myName : msg.from_name;
                appendMessage(messageClass, msg.message, senderName, new Date(msg.created_at), msg.id, msg.delivered, msg.read_at);
            });
            // Mark messages as read
            markMessagesAsRead(msgs.filter(msg => !msg.is_me && !msg.read_at).map(msg => msg.id));
        }).catch(err => console.error('Messages fetch error:', err));
        
        // Hide user list on mobile
        if (window.innerWidth < 768) {
            document.querySelector('.user-list').classList.remove('show');
        }
    });
});

// Send message
sendButton.addEventListener('click', () => {
    sendMessage();
});

messageInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        sendMessage();
    } else {
        handleTyping();
    }
});

messageInput.addEventListener('input', () => {
    handleTyping();
});

function sendMessage() {
    const message = messageInput.value.trim();
    if (!message || !otherId) {
        if (!otherId) {
            alert('Please select a recipient first');
        }
        return;
    }

    // Stop typing indicator
    socket.emit('stop-typing', { user_id: userId, to_user_id: otherId });
    isTyping = false;

    fetch(`${baseUrl}/api/send`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${apiToken}`
        },
        body: JSON.stringify({ to: otherId, message })
    }).then(res => {
        if (!res.ok) throw new Error(`Send failed: ${res.status} ${res.statusText}`);
        return res.json();
    }).then(data => {
        appendMessage('sent', message, myName, new Date(), data.message_id || Date.now(), false, null);
        messageInput.value = '';
    }).catch(err => console.error('Send error:', err));
}

function appendMessage(type, message, senderName, timestamp, messageId = null, delivered = false, readAt = null) {
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('message', type);
    if (messageId) messageDiv.dataset.messageId = messageId;
    
    const bubbleDiv = document.createElement('div');
    bubbleDiv.classList.add('message-bubble');
    bubbleDiv.textContent = message;
    
    const timeDiv = document.createElement('div');
    timeDiv.classList.add('message-time');
    timeDiv.textContent = formatTime(timestamp);
    
    // Add message status for sent messages
    if (type === 'sent') {
        const statusDiv = document.createElement('div');
        statusDiv.classList.add('message-status');
        
        if (readAt) {
            statusDiv.innerHTML = '<i class="bi bi-check2-all double-tick read"></i>';
        } else if (delivered) {
            statusDiv.innerHTML = '<i class="bi bi-check2-all double-tick"></i>';
        } else {
            statusDiv.innerHTML = '<i class="bi bi-check2 single-tick"></i>';
        }
        
        messageDiv.appendChild(bubbleDiv);
        messageDiv.appendChild(timeDiv);
        messageDiv.appendChild(statusDiv);
    } else {
        messageDiv.appendChild(bubbleDiv);
        messageDiv.appendChild(timeDiv);
    }
    
    messagesElement.appendChild(messageDiv);
    messagesElement.scrollTop = messagesElement.scrollHeight;
}

function formatTime(date) {
    if (!date || isNaN(date.getTime())) {
        return '--:--';
    }
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function handleTyping() {
    if (!otherId) return;
    
    if (!isTyping) {
        isTyping = true;
        socket.emit('typing', { 
            user_id: userId, 
            user_name: myName, 
            to_user_id: otherId 
        });
    }
    
    clearTimeout(typingTimer);
    typingTimer = setTimeout(() => {
        if (isTyping) {
            isTyping = false;
            socket.emit('stop-typing', { 
                user_id: userId, 
                to_user_id: otherId 
            });
        }
    }, 1000);
}

function showTypingIndicator(userName) {
    typingUser.textContent = userName;
    typingIndicator.style.display = 'block';
    messagesElement.scrollTop = messagesElement.scrollHeight;
}

function hideTypingIndicator() {
    typingIndicator.style.display = 'none';
}

function updateMessageStatus(messageId, status) {
    const messageElement = document.querySelector(`[data-message-id="${messageId}"]`);
    if (messageElement) {
        const statusElement = messageElement.querySelector('.message-status');
        if (statusElement) {
            if (status === 'delivered') {
                statusElement.innerHTML = '<i class="bi bi-check2-all double-tick"></i>';
            } else if (status === 'read') {
                statusElement.innerHTML = '<i class="bi bi-check2-all double-tick read"></i>';
            }
        }
    }
}

function markMessagesAsRead(messageIds) {
    if (messageIds.length > 0) {
        fetch(`${baseUrl}/api/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${apiToken}`
            },
            body: JSON.stringify({ message_ids: messageIds })
        }).then(res => {
            if (res.ok) {
                // Emit read receipt to sender
                messageIds.forEach(id => {
                    socket.emit('message-read', { 
                        message_id: id, 
                        from_user_id: otherId,
                        read_at: new Date().toISOString()
                    });
                });
            }
        }).catch(err => console.error('Mark as read error:', err));
    }
}

function markMessageAsRead(messageId) {
    fetch(`${baseUrl}/api/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${apiToken}`
        },
        body: JSON.stringify({ message_ids: [messageId] })
    }).then(res => {
        if (res.ok) {
            socket.emit('message-read', { 
                message_id: messageId, 
                from_user_id: otherId,
                read_at: new Date().toISOString()
            });
        }
    }).catch(err => console.error('Mark as read error:', err));
}

function incrementUnreadCount(userId) {
    const unreadElement = document.getElementById(`unread-count-${userId}`);
    if (unreadElement) {
        const currentCount = parseInt(unreadElement.textContent) || 0;
        unreadElement.textContent = currentCount + 1;
        unreadElement.style.display = 'block';
    }
}

function updateLastMessage(userId, message, timestamp) {
    const lastMessageElement = document.getElementById(`last-message-${userId}`);
    const lastTimeElement = document.getElementById(`last-message-time-${userId}`);
    
    if (lastMessageElement) {
        lastMessageElement.textContent = message;
    }
    if (lastTimeElement) {
        lastTimeElement.textContent = formatTime(timestamp);
    }
}

function getUserName(userId) {
    const userElement = document.querySelector(`[data-user-id="${userId}"]`);
    if (userElement) {
        const nameElement = userElement.querySelector('h6');
        return nameElement ? nameElement.textContent : 'User';
    }
    return 'User';
}

function showNotification(message) {
    // Create a simple notification
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #25d366;
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        font-size: 14px;
        max-width: 300px;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// Add CSS for notification animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Real-time status updates are handled via Socket.IO events
// No need for periodic checks that can cause confusion

function updateUserStatus(userId, isOnline) {
    console.log(`Updating status for user ${userId}: ${isOnline ? 'online' : 'offline'}`);
    
    // Update status in user list
    const statusElements = document.querySelectorAll(`.user-status[data-user-id="${userId}"]`);
    statusElements.forEach(el => {
        if (isOnline) {
            el.className = 'user-status status-online';
            el.innerHTML = '<i class="bi bi-circle-fill me-1"></i>Online';
        } else {
            el.className = 'user-status status-offline';
            el.innerHTML = '<i class="bi bi-circle-fill me-1"></i>Offline';
        }
    });
    
    // Update online indicator
    const onlineIndicator = document.getElementById(`online-indicator-${userId}`);
    if (onlineIndicator) {
        onlineIndicator.style.display = isOnline ? 'block' : 'none';
    }
    
    // Update selected user status in chat header
    if (userId === otherId) {
        if (isOnline) {
            selectedUserStatus.className = 'status-online';
            selectedUserStatus.innerHTML = '<i class="bi bi-circle-fill me-1"></i>Online';
        } else {
            selectedUserStatus.className = 'status-offline';
            selectedUserStatus.innerHTML = '<i class="bi bi-circle-fill me-1"></i>Offline';
        }
    }
}

function updateAllStatuses(users) {
    console.log('Updating all statuses with users:', users);
    
    // Get all user elements
    const userElements = document.querySelectorAll('.room-item');
    userElements.forEach(element => {
        const userId = parseInt(element.dataset.userId);
        const isOnline = users.includes(userId);
        updateUserStatus(userId, isOnline);
    });
}

// Mobile responsiveness
toggleUserList.addEventListener('click', () => {
    document.querySelector('.user-list').classList.toggle('show');
});

backToUsers.addEventListener('click', () => {
    document.querySelector('.user-list').classList.add('show');
});

// Close user list when clicking outside on mobile
document.addEventListener('click', (e) => {
    if (window.innerWidth < 768) {
        const userList = document.querySelector('.user-list');
        const toggleBtn = document.getElementById('toggleUserList');
        
        if (!userList.contains(e.target) && !toggleBtn.contains(e.target)) {
            userList.classList.remove('show');
        }
    }
});

// Handle window resize
window.addEventListener('resize', () => {
    if (window.innerWidth >= 768) {
        document.querySelector('.user-list').classList.remove('show');
    }
});
</script>
@endpush
