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
const baseUrl = '{{ env('APP_URL') }}';

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

// State management
let typingTimer = null;
let isTyping = false;
let lastMessageTimes = {};
let unreadCounts = {};
let lastMessages = {};
let onlineUsers = [];

console.log('API Token:', apiToken);
console.log('Base URL:', baseUrl);

if (!apiToken) {
    alert('Authentication token missing. Please log in again.');
    window.location.href = '{{ route('login') }}';
}

const socket = io('http://127.0.0.1:3000', { transports: ['websocket'] });

socket.on('connect', () => {
    console.log('=== SOCKET CONNECTED ===');
    console.log('Socket ID:', socket.id);
    console.log('User ID:', userId);
    socket.emit('join', userId);

    // Load initial message previews for all users
    loadMessagePreviews();
    
    // Fetch pending messages
    fetchPendingMessages();
});

socket.on('disconnect', () => {
    console.log('=== SOCKET DISCONNECTED ===');
});

socket.on('connect_error', (error) => {
    console.log('=== SOCKET CONNECTION ERROR ===');
    console.log('Error:', error);
});

socket.on('test-response', (data) => {
    console.log('=== TEST RESPONSE RECEIVED ===');
    console.log('Data:', data);
});

socket.on('new-message', (data) => {
    console.log('New message received via socket:', data);
    
    // Always update the sidebar with the new message
    const targetUserId = data.from === userId ? data.to : data.from;
    updateLastMessage(targetUserId, data.message, new Date());
    
    // Only show in chat if it's the current conversation
    if (data.from === otherId || data.from === userId) {
        const messageClass = data.from === userId ? 'sent' : 'received';
        const senderName = data.from === userId ? myName : data.from_name;
        const timestamp = new Date();
        const status = data.from === userId ? 'delivered' : 'received';
        
        appendMessage(messageClass, data.message, senderName, timestamp, data.id, status);
        
        // Mark as delivered if it's our message
        if (data.from === userId) {
            fetch(`${baseUrl}/api/mark-delivered/${data.id}`, {
                method: 'POST',
                headers: { 'Authorization': `Bearer ${apiToken}` }
            }).catch(err => console.error('Mark delivered error:', err));
        } else {
            // Mark as read if it's from the current conversation
            if (data.from === otherId) {
                markMessageAsRead(data.id);
            } else {
                // Increment unread count for other conversations
                incrementUnreadCount(data.from);
            }
        }
    } else {
        // This is a message from another conversation, just update unread count
        incrementUnreadCount(data.from);
    }
});

socket.on('online', (data) => {
    console.log("socket.on('online'): ", data);
    updateUserStatus(data.user_id, true);
});

socket.on('offline', (data) => {
    console.log("socket.on('offline'): ", data);
    updateUserStatus(data.user_id, false);
});

socket.on('online-users', (users) => {
    console.log("online-users: ", users);
    onlineUsers = users;
    updateAllStatuses(users);
});

socket.on('user-status', (data) => {
    console.log("user-status: ", data);
    updateUserStatus(data.user_id, data.online);
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
    console.log('=== MESSAGE READ EVENT RECEIVED ===');
    console.log('Data:', data);
    console.log('Updating message status to read for message ID:', data.message_id);
    updateMessageStatus(data.message_id, 'read');
});

socket.on('message-delivered', (data) => {
    console.log('=== MESSAGE DELIVERED EVENT RECEIVED ===');
    console.log('Data:', data);
    console.log('Updating message status to delivered for message ID:', data.message_id);
    updateMessageStatus(data.message_id, 'delivered');
});

socket.on('conversation-read', (data) => {
    console.log('=== CONVERSATION READ EVENT RECEIVED ===');
    console.log('Data:', data);
    console.log('Updating all sent messages to read status for conversation:', data.conversation_id);
    if (data.conversation_id === otherId) {
        const sentMessages = messagesElement.querySelectorAll('.message.sent');
        console.log('Found sent messages to update:', sentMessages.length);
        sentMessages.forEach((msg, index) => {
            const messageId = msg.getAttribute('data-message-id');
            if (messageId) {
                console.log(`Updating sent message ${index + 1} to read:`, messageId);
                updateMessageStatus(messageId, 'read');
            }
        });
    }
});

function fetchPendingMessages() {
    fetch(`${baseUrl}/api/pending`, {
        headers: { 'Authorization': `Bearer ${apiToken}` }
    }).then(res => {
        if (!res.ok) throw new Error(`Pending failed: ${res.status} ${res.statusText}`);
        return res.json();
    }).then(msgs => {
        console.log('Pending messages:', msgs);
        msgs.forEach(msg => {
            const messageClass = msg.from === userId ? 'sent' : 'received';
            appendMessage(messageClass, msg.message, msg.from_name, new Date(msg.sent_at), msg.id, 'delivered');
            // Mark as read if it's from the current conversation
            if (msg.from === otherId) {
                markMessageAsRead(msg.id);
            } else {
                incrementUnreadCount(msg.from);
            }
        });
    }).catch(err => console.error('Pending fetch error:', err));
}

// Handle user selection
userList.querySelectorAll('.room-item').forEach(item => {
    item.addEventListener('click', () => {
        otherId = parseInt(item.dataset.userId);
        if (!otherId) return;

        // Update UI
        userList.querySelectorAll('.room-item').forEach(i => i.classList.remove('active'));
        item.classList.add('active');
        
        // Clear unread count for selected user
        clearUnreadCount(otherId);
        
        // Mark all messages as read when opening conversation
        markAllMessagesAsRead();
        
        // Show chat interface
        chatHeader.classList.remove('d-none');
        noChatSelected.classList.add('d-none');
        messageInputContainer.classList.remove('d-none');
        
        // Update header
        const userName = item.querySelector('h6').textContent;
        selectedUserName.textContent = userName;
        selectedUserInitial.textContent = userName.charAt(0).toUpperCase();
        
        // Update header status based on current user status
        const statusElement = item.querySelector('.user-status');
        if (statusElement) {
            selectedUserStatus.className = statusElement.className;
            selectedUserStatus.innerHTML = statusElement.innerHTML;
        }
        
        // Clear messages and fetch new ones
        messagesElement.innerHTML = '';
        
        // Fetch messages
        fetch(`${baseUrl}/api/messages?with=${otherId}`, {
            headers: { 'Authorization': `Bearer ${apiToken}` }
        }).then(res => {
            if (!res.ok) throw new Error(`Messages failed: ${res.status} ${res.statusText}`);
            return res.json();
        }).then(msgs => {
            console.log('Loaded messages:', msgs.length);
            msgs.forEach(msg => {
                const messageClass = msg.is_me ? 'sent' : 'received';
                const senderName = msg.is_me ? myName : msg.from_name;
                const timestamp = msg.sent_at ? new Date(msg.sent_at) : new Date();
                const status = msg.is_me ? (msg.read_at ? 'read' : 'delivered') : 'received';
                appendMessage(messageClass, msg.message, senderName, timestamp, msg.id, status);
            });
            
            // Update all sent messages to read status after loading
            setTimeout(() => {
                markAllMessagesAsRead();
                scrollToBottom();
            }, 200);
            
            socket.emit('check-status', otherId);
        }).catch(err => console.error('Messages fetch error:', err));
        
        // Hide user list on mobile
        if (isMobile()) {
            document.querySelector('.user-list').classList.remove('show');
            setTimeout(() => {
                messageInput.focus();
            }, 300);
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
    }
});

// Typing functionality
messageInput.addEventListener('input', () => {
    if (!otherId) return;
    
    if (!isTyping) {
        isTyping = true;
        socket.emit('typing', { user_id: userId, user_name: myName, to: otherId });
    }
    
    clearTimeout(typingTimer);
    typingTimer = setTimeout(() => {
        if (isTyping) {
            isTyping = false;
            socket.emit('stop-typing', { user_id: userId, to: otherId });
        }
    }, 1000);
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
    if (isTyping) {
        isTyping = false;
        socket.emit('stop-typing', { user_id: userId, to: otherId });
    }

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
        // Update sidebar immediately
        updateLastMessage(otherId, message, new Date());
        
        // Add message with sending status first
        appendMessage('sent', message, myName, new Date(), data.id, 'sending');
        messageInput.value = '';
        
        // Update status to delivered
        setTimeout(() => {
            updateMessageStatus(data.id, 'delivered');
            console.log('Message delivered, ID:', data.id);
        }, 1000);
    }).catch(err => console.error('Send error:', err));
}

function appendMessage(type, message, senderName, timestamp, messageId = null, status = 'sending') {
    const messageDiv = document.createElement('div');
    messageDiv.classList.add('message', type);
    if (messageId) {
        messageDiv.setAttribute('data-message-id', messageId);
    }
    
    const bubbleDiv = document.createElement('div');
    bubbleDiv.classList.add('message-bubble');
    bubbleDiv.textContent = message;
    
    const timeDiv = document.createElement('div');
    timeDiv.classList.add('message-time');
    timeDiv.textContent = formatTime(timestamp);
    
    // Add status indicators for sent messages
    if (type === 'sent') {
        const statusDiv = document.createElement('div');
        statusDiv.classList.add('message-status');
        statusDiv.innerHTML = getStatusIcon(status);
        statusDiv.setAttribute('data-status', status);
        messageDiv.appendChild(statusDiv);
    }
    
    messageDiv.appendChild(bubbleDiv);
    messageDiv.appendChild(timeDiv);
    
    messagesElement.appendChild(messageDiv);
    scrollToBottom();
}

function getStatusIcon(status) {
    switch (status) {
        case 'sending':
            return '<i class="bi bi-clock"></i>';
        case 'delivered':
            return '<i class="bi bi-check2 single-tick"></i>';
        case 'read':
            return '<i class="bi bi-check2-all double-tick read"></i>';
        default:
            return '<i class="bi bi-clock"></i>';
    }
}

function formatTime(date) {
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
}

function updateUserStatus(userId, isOnline) {
    const statusElements = document.querySelectorAll(`.user-status[data-user-id="${userId}"]`);
    statusElements.forEach(el => {
        const icon = el.querySelector('i');
        const text = el.querySelector('span') || el;
        console.log('updateUserStatus: ', userId, isOnline);
        if (isOnline) {
            el.className = 'user-status status-online';
            text.innerHTML = '<i class="bi bi-circle-fill me-1"></i>Online';
        } else {
            el.className = 'user-status status-offline';
            text.innerHTML = '<i class="bi bi-circle-fill me-1"></i>Offline';
        }
    });
    
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
    document.querySelectorAll('.user-status').forEach(el => {
        const userId = parseInt(el.dataset.userId);
        const isOnline = users.includes(userId);
        updateUserStatus(userId, isOnline);
    });
}

function updateLastMessage(userId, message, timestamp) {
    const lastMessageEl = document.getElementById(`last-message-${userId}`);
    const lastTimeEl = document.getElementById(`last-message-time-${userId}`);
    
    if (lastMessageEl) {
        lastMessageEl.textContent = message;
    }
    if (lastTimeEl) {
        lastTimeEl.textContent = formatTime(timestamp);
    }
    
    lastMessages[userId] = message;
    lastMessageTimes[userId] = timestamp;
}

function incrementUnreadCount(userId) {
    if (userId === otherId) return; // Don't increment for current conversation
    
    unreadCounts[userId] = (unreadCounts[userId] || 0) + 1;
    const unreadEl = document.getElementById(`unread-count-${userId}`);
    if (unreadEl) {
        unreadEl.textContent = unreadCounts[userId];
        unreadEl.style.display = unreadCounts[userId] > 0 ? 'inline' : 'none';
    }
}

function markMessageAsRead(messageId) {
    fetch(`${baseUrl}/api/mark-read/${messageId}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${apiToken}` }
    }).then(() => {
        console.log('Message marked as read:', messageId);
        socket.emit('message-read', {
            message_id: messageId,
            from: otherId,
            to: userId
        });
    }).catch(err => console.error('Mark read error:', err));
}

function markAllMessagesAsRead() {
    if (!otherId) return;
    
    console.log('=== Marking All Messages as Read ===');
    console.log('Current user ID:', userId);
    console.log('Other user ID:', otherId);
    
    fetch(`${baseUrl}/api/mark-conversation-read/${otherId}`, {
        method: 'POST',
        headers: { 'Authorization': `Bearer ${apiToken}` }
    }).then(() => {
        console.log('API call successful, emitting Socket.IO events');
        
        const receivedMessages = messagesElement.querySelectorAll('.message.received');
        console.log('Found received messages:', receivedMessages.length);
        
        receivedMessages.forEach((msg, index) => {
            const messageId = msg.getAttribute('data-message-id');
            if (messageId) {
                console.log(`Emitting message-read for message ${index + 1}:`, messageId);
                socket.emit('message-read', {
                    message_id: messageId,
                    from: otherId,
                    to: userId
                });
            }
        });
        
        console.log('Emitting conversation-read event');
        socket.emit('conversation-read', {
            from: otherId,
            to: userId,
            conversation_id: otherId
        });
    }).catch(err => console.error('Mark conversation read error:', err));
}

function updateMessageStatus(messageId, status) {
    console.log('Updating message status:', messageId, status);
    const messageEl = document.querySelector(`[data-message-id="${messageId}"]`);
    let isRead = false;
    if(status == 'read') {
        isRead = true;
    }
    if (messageEl) {
        const statusEl = messageEl.querySelector('.message-status');
        if (statusEl) {
            if(!isRead) {
                console.log('Found status element, updating to:', status);
                statusEl.innerHTML = getStatusIcon(status);
                statusEl.setAttribute('data-status', status);
                console.log('Status updated. New HTML:', statusEl.innerHTML);
            } else {
                console.log('is all ready is Read:', isRead);
                statusEl.innerHTML = getStatusIcon('read');
                statusEl.setAttribute('data-status', 'read');
                console.log('Status updated. New HTML:', statusEl.innerHTML);
            }
        } else {
            console.log('No status element found for message:', messageId);
        }
    } else {
        console.log('Message element not found for ID:', messageId);
    }
}

function showTypingIndicator(userName) {
    typingUser.textContent = userName;
    typingIndicator.style.display = 'block';
}

function hideTypingIndicator() {
    typingIndicator.style.display = 'none';
}

function clearUnreadCount(userId) {
    unreadCounts[userId] = 0;
    const unreadEl = document.getElementById(`unread-count-${userId}`);
    if (unreadEl) {
        unreadEl.style.display = 'none';
    }
}

function isUserOnline(userId) {
    return onlineUsers.includes(userId);
}

function loadMessagePreviews() {
    const userItems = document.querySelectorAll('.room-item');
    userItems.forEach(item => {
        const userId = parseInt(item.dataset.userId);
        if (userId) {
            fetch(`${baseUrl}/api/last-message/${userId}`, {
                headers: { 'Authorization': `Bearer ${apiToken}` }
            }).then(res => {
                if (res.ok) {
                    return res.json();
                }
                throw new Error('Failed to fetch last message');
            }).then(data => {
                if (data.message) {
                    updateLastMessage(userId, data.message, new Date(data.created_at));
                }
            }).catch(err => {
                console.log(`No messages with user ${userId}`);
            });
        }
    });
}

// Mobile responsiveness
toggleUserList.addEventListener('click', () => {
    document.querySelector('.user-list').classList.toggle('show');
});

backToUsers.addEventListener('click', () => {
    document.querySelector('.user-list').classList.add('show');
    if (isMobile()) {
        chatHeader.classList.add('d-none');
        noChatSelected.classList.remove('d-none');
        messageInputContainer.classList.add('d-none');
        otherId = null;
    }
});

document.addEventListener('click', (e) => {
    if (window.innerWidth < 768) {
        const userList = document.querySelector('.user-list');
        const toggleBtn = document.getElementById('toggleUserList');
        
        if (!userList.contains(e.target) && !toggleBtn.contains(e.target)) {
            userList.classList.remove('show');
        }
    }
});

window.addEventListener('resize', () => {
    if (window.innerWidth >= 768) {
        document.querySelector('.user-list').classList.remove('show');
    }
});

function isMobile() {
    return window.innerWidth < 768;
}

function scrollToBottom() {
    if (messagesElement) {
        console.log('Scrolling to bottom. ScrollHeight:', messagesElement.scrollHeight, 'ClientHeight:', messagesElement.clientHeight);
        setTimeout(() => {
            messagesElement.scrollTo({
                top: messagesElement.scrollHeight,
                behavior: 'smooth'
            });
        }, 50);
    }
}

// Test functions for debugging
window.testMessageStatus = function() {
    console.log('=== Testing Message Status ===');
    const sentMessages = messagesElement.querySelectorAll('.message.sent');
    console.log('Found sent messages:', sentMessages.length);
    sentMessages.forEach((msg, index) => {
        const messageId = msg.getAttribute('data-message-id');
        const statusEl = msg.querySelector('.message-status');
        console.log(`Message ${index + 1}: ID=${messageId}, Status element exists=${!!statusEl}`);
        if (statusEl) {
            console.log(`Current status HTML:`, statusEl.innerHTML);
            console.log(`Current status classes:`, statusEl.className);
        }
    });
};

window.testRealtimeRead = function() {
    console.log('=== Testing Real-time Read Events ===');
    if (!otherId) {
        console.log('No conversation selected');
        return;
    }
    
    console.log('Simulating conversation read for user:', otherId);
    socket.emit('conversation-read', {
        from: otherId,
        to: userId,
        conversation_id: otherId
    });
    
    console.log('Simulating individual message read');
    const sentMessages = messagesElement.querySelectorAll('.message.sent');
    if (sentMessages.length > 0) {
        const firstMessage = sentMessages[0];
        const messageId = firstMessage.getAttribute('data-message-id');
        if (messageId) {
            socket.emit('message-read', {
                message_id: messageId,
                from: otherId,
                to: userId
            });
        }
    }
};
</script>
@endpush
