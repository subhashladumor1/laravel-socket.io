@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2 text-dark fw-bold">Welcome back, {{ Auth::user()->name }}!</h2>
                            <p class="text-muted mb-0">Ready to start a conversation? Select a user from your contacts to begin chatting.</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="d-flex align-items-center justify-content-md-end">
                                <div class="user-avatar me-3" style="width: 60px; height: 60px; font-size: 24px;">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-semibold">{{ Auth::user()->name }}</h6>
                                    <small class="text-success">
                                        <i class="bi bi-circle-fill me-1"></i>Online
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-chat-dots text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="card-title fw-semibold">Start Chatting</h5>
                    <p class="card-text text-muted">Connect with your friends and colleagues in real-time.</p>
                    <a href="{{ route('chat') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-right me-2"></i>Open Chat
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-people text-success" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="card-title fw-semibold">Active Users</h5>
                    <p class="card-text text-muted">See who's currently online and available to chat.</p>
                    <span class="badge bg-success fs-6" id="onlineCount">Loading...</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <i class="bi bi-shield-check text-info" style="font-size: 2.5rem;"></i>
                    </div>
                    <h5 class="card-title fw-semibold">Secure Messaging</h5>
                    <p class="card-text text-muted">Your conversations are encrypted and private.</p>
                    <span class="badge bg-info fs-6">Protected</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-2"></i>Recent Activity
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3" style="width: 40px; height: 40px; font-size: 16px;">
                                    <i class="bi bi-person"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold">Welcome to your dashboard</h6>
                                    <p class="mb-0 text-muted">You've successfully logged into the chat application.</p>
                                </div>
                                <small class="text-muted">Just now</small>
                            </div>
                        </div>
                        
                        @if (session('status'))
                        <div class="list-group-item border-0 py-3">
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3" style="width: 40px; height: 40px; font-size: 16px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                                    <i class="bi bi-check-circle"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-semibold">Status Update</h6>
                                    <p class="mb-0 text-muted">{{ session('status') }}</p>
                                </div>
                                <small class="text-muted">Just now</small>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.socket.io/4.8.1/socket.io.min.js"></script>
<script>
const socket = io('http://127.0.0.1:3000');
let onlineUsers = [];

socket.on('connect', () => {
    console.log('Connected to server');
    socket.emit('join', {{ Auth::id() }});
});

socket.on('online-users', (users) => {
    onlineUsers = users;
    updateOnlineCount();
});

socket.on('online', (data) => {
    if (!onlineUsers.includes(data.user_id)) {
        onlineUsers.push(data.user_id);
    }
    updateOnlineCount();
});

socket.on('offline', (data) => {
    onlineUsers = onlineUsers.filter(id => id !== data.user_id);
    updateOnlineCount();
});

function updateOnlineCount() {
    const count = onlineUsers.length;
    const countElement = document.getElementById('onlineCount');
    if (countElement) {
        countElement.textContent = `${count} user${count !== 1 ? 's' : ''} online`;
    }
}
</script>
@endpush
