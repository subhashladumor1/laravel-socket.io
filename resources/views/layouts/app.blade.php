<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #111b21;
            color: #e9edef;
        }
        .navbar-brand {
            font-weight: 600;
            color: #2c3e50 !important;
        }
        .chat-container {
            height: calc(100vh - 76px);
            background: #0b141a;
            border-radius: 0;
            box-shadow: none;
            overflow: hidden;
            border: 1px solid #2a3942;
        }
        .user-list {
            background: #111b21;
            border-right: 1px solid #2a3942;
            height: 100%;
            overflow-y: auto;
        }
        .room-item {
            transition: all 0.2s ease;
            border-bottom: 1px solid #2a3942;
            color: #e9edef;
        }
        .room-item:hover {
            background-color: #2a3942;
        }
        .room-item.active {
            background-color: #2a3942;
            color: #e9edef;
        }
        .user-avatar {
            width: 49px;
            height: 49px;
            border-radius: 50%;
            background: #6b7c85;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 500;
            font-size: 18px;
            margin-right: 13px;
        }
        .message-container {
            height: calc(100% - 120px);
            overflow-y: auto;
            background: #0b141a;
            padding: 20px;
            background-image: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.02"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.02"/><circle cx="50" cy="10" r="0.5" fill="%23ffffff" opacity="0.02"/><circle cx="10" cy="60" r="0.5" fill="%23ffffff" opacity="0.02"/><circle cx="90" cy="40" r="0.5" fill="%23ffffff" opacity="0.02"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }
        .message {
            margin-bottom: 15px;
            display: flex;
            align-items: flex-end;
        }
        .message.sent {
            justify-content: flex-end;
        }
        .message.received {
            justify-content: flex-start;
        }
        .message-bubble {
            max-width: 70%;
            padding: 12px 16px;
            border-radius: 18px;
            position: relative;
            word-wrap: break-word;
        }
        .message.sent .message-bubble {
            background: #005c4b;
            color: #e9edef;
            border-bottom-right-radius: 4px;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
        }
        .message.received .message-bubble {
            background: #202c33;
            color: #e9edef;
            border-bottom-left-radius: 4px;
            box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
        }
        .message-time {
            font-size: 11px;
            opacity: 0.6;
            margin-top: 4px;
            color: #8696a0;
        }
        .message-status {
            display: flex;
            align-items: center;
            margin-top: 4px;
            font-size: 11px;
        }
        .message-status i {
            margin-left: 4px;
        }
        .message-status .single-tick {
            color: #8696a0;
        }
        .message-status .double-tick {
            color: #53bdeb;
        }
        .message-status .double-tick.read {
            color: #53bdeb;
        }
        .typing-indicator {
            display: flex;
            align-items: center;
            padding: 8px 16px;
            color: #8696a0;
            font-style: italic;
            font-size: 14px;
        }
        .typing-dots {
            display: inline-flex;
            margin-left: 8px;
        }
        .typing-dots span {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: #8696a0;
            margin: 0 1px;
            animation: typing 1.4s infinite ease-in-out;
        }
        .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .typing-dots span:nth-child(2) { animation-delay: -0.16s; }
        @keyframes typing {
            0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
            40% { transform: scale(1); opacity: 1; }
        }
        .last-seen {
            font-size: 12px;
            color: #8696a0;
            margin-top: 2px;
        }
        .room-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .room-item:hover {
            background-color: #2a3942;
        }
        .room-item.active {
            background-color: #2a3942;
            border-left-color: #00a884;
        }
        .room-preview {
            font-size: 13px;
            color: #8696a0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .room-time {
            font-size: 11px;
            color: #8696a0;
        }
        .unread-badge {
            background: #00a884;
            color: white;
            border-radius: 10px;
            padding: 2px 6px;
            font-size: 11px;
            font-weight: 600;
        }
        .online-indicator {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 12px;
            height: 12px;
            background: #00a884;
            border-radius: 50%;
            border: 2px solid #111b21;
        }
        .message-input-container {
            background: #202c33;
            border-top: 1px solid #2a3942;
            padding: 20px;
        }
        .status-online {
            color: #00a884;
        }
        .status-offline {
            color: #8696a0;
        }
        .chat-header {
            background: #202c33;
            color: #e9edef;
            padding: 20px;
            border-bottom: 1px solid #2a3942;
        }
        .typing-indicator {
            font-style: italic;
            color: #8696a0;
            font-size: 14px;
        }
        .no-chat-selected {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #8696a0;
            text-align: center;
        }
        .no-chat-selected i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        .form-control {
            background-color: #2a3942;
            border: 1px solid #2a3942;
            color: #e9edef;
            border-radius: 21px;
        }
        .form-control:focus {
            background-color: #2a3942;
            border-color: #00a884;
            color: #e9edef;
            box-shadow: 0 0 0 0.2rem rgba(0, 168, 132, 0.25);
        }
        .form-control::placeholder {
            color: #8696a0;
        }
        .btn-primary {
            background-color: #00a884;
            border-color: #00a884;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-primary:hover {
            background-color: #008f73;
            border-color: #008f73;
        }
        .btn-primary:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 168, 132, 0.25);
        }
        @media (max-width: 768px) {
            .chat-container {
                height: calc(100vh - 56px);
            }
            .user-list {
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                z-index: 1000;
                transition: left 0.3s ease;
            }
            .user-list.show {
                left: 0;
            }
            .message-bubble {
                max-width: 85%;
            }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
