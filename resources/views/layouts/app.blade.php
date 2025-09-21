<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

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
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            overflow: hidden;
            height: 100vh;
        }
        .navbar-brand {
            font-weight: 600;
            color: #2c3e50 !important;
        }
        #app {
            height: 100vh;
            overflow: hidden;
        }
        .container-fluid {
            height: 100vh;
            overflow: hidden;
        }
        .chat-container {
            height: calc(100vh - 76px);
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: relative;
            min-height: 0;
            justify-content: space-between;
        }
        .user-list {
            background: #f8f9fa;
            border-right: 1px solid #e9ecef;
            height: calc(100vh - 76px);
            overflow-y: auto;
            position: sticky;
            top: 0;
            /* Custom scrollbar */
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }
        .user-list::-webkit-scrollbar {
            width: 6px;
        }
        .user-list::-webkit-scrollbar-track {
            background: #f7fafc;
        }
        .user-list::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }
        .user-list::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
        .user-item {
            transition: all 0.2s ease;
            border-bottom: 1px solid #e9ecef;
        }
        .user-item:hover {
            background-color: #e9ecef;
        }
        .user-item.active {
            background-color: #007bff;
            color: white;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 16px;
        }
        .message-container {
            height: calc(100vh - 240px);
            max-height: calc(100vh - 240px);
            overflow-y: auto;
            background: #ffffff;
            padding: 20px 20px 100px 20px;
            flex: 1;
            position: relative;
            min-height: 0;
            margin-bottom: 0;
            /* Custom scrollbar */
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
            scroll-behavior: smooth;
        }
        .message-container::-webkit-scrollbar {
            width: 8px;
        }
        .message-container::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 4px;
        }
        .message-container::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
            border: 1px solid #f7fafc;
        }
        .message-container::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
        .message-container::-webkit-scrollbar-thumb:active {
            background: #718096;
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
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-bottom-right-radius: 4px;
        }
        .message.received .message-bubble {
            background: #f1f3f4;
            color: #333;
            border-bottom-left-radius: 4px;
        }
        .message-time {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 4px;
        }
        .message-status {
            display: flex;
            align-items: center;
            margin-top: 4px;
            font-size: 11px;
            justify-content: flex-end;
        }
        .message-status i {
            margin-left: 4px;
        }
        .message-status .single-tick {
            color: #999;
        }
        .message-status .double-tick {
            color: #4fc3f7;
        }
        .message-status .double-tick.read {
            color: #4caf50;
        }
        .message-status .bi-clock {
            color: #ccc;
        }
        .typing-indicator {
            display: flex;
            align-items: center;
            padding: 8px 16px;
            color: #666;
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
            background-color: #666;
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
            color: #666;
            margin-top: 2px;
        }
        .room-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }
        .room-item:hover {
            background-color: #f8f9fa;
        }
        .room-item.active {
            background-color: #e3f2fd;
            border-left-color: #2196f3;
        }
        .room-preview {
            font-size: 13px;
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .room-time {
            font-size: 11px;
            color: #999;
        }
        .unread-badge {
            background: #4caf50;
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
            background: #4caf50;
            border-radius: 50%;
            border: 2px solid white;
        }
        .message-input-container {
            background: #ffffff;
            border-top: 1px solid #e9ecef;
            padding: 8px;
            position: sticky;
            bottom: 0;
            z-index: 100;
            margin-bottom: 200px;
            flex-shrink: 0;
            height: auto;
            min-height: 80px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }
        .status-online {
            color: #28a745;
        }
        .status-offline {
            color: #6c757d;
        }
        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
            position: sticky;
            top: 0;
            z-index: 100;
            flex-shrink: 0;
        }
        .typing-indicator {
            font-style: italic;
            color: #6c757d;
            font-size: 14px;
        }
        .no-chat-selected {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #6c757d;
            text-align: center;
        }
        .no-chat-selected i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }
        @media (max-width: 768px) {
            body {
                overflow: hidden;
                height: 100vh;
            }
            .container-fluid {
                padding: 0;
                height: 100vh;
                overflow: hidden;
            }
            .row {
                margin: 0;
                height: 100vh;
            }
            .col-md-4, .col-lg-3 {
                padding: 0;
            }
            .col-md-8, .col-lg-9 {
                padding: 0;
            }
            .chat-container {
                height: 100vh;
                border-radius: 0;
                box-shadow: none;
                overflow: hidden;
            }
            .user-list {
                position: fixed;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100vh;
                z-index: 1000;
                transition: left 0.3s ease;
                background: #f8f9fa;
                overflow-y: auto;
            }
            .user-list.show {
                left: 0;
            }
            .message-bubble {
                max-width: 85%;
            }
            .chat-header {
                padding: 15px 20px;
                position: sticky;
                top: 0;
                z-index: 1001;
                flex-shrink: 0;
            }
            .message-container {
                padding: 15px 15px 80px 15px;
                height: calc(100vh - 180px);
                max-height: calc(100vh - 180px);
                overflow-y: auto;
                flex: 1;
                min-height: 0;
                margin-bottom: 0;
            }
            .message-input-container {
                padding: 15px;
                position: sticky;
                bottom: 0;
                flex-shrink: 0;
                height: auto;
                min-height: 70px;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            }
            .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 14px;
            }
            .room-item {
                padding: 15px !important;
            }
            .room-item h6 {
                font-size: 14px;
            }
            .room-preview {
                font-size: 12px;
            }
            .room-time {
                font-size: 10px;
            }
            .message-bubble {
                font-size: 14px;
                padding: 10px 14px;
            }
            .message-time {
                font-size: 10px;
            }
            .btn-lg {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }
            .form-control-lg {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }
        }
        
        @media (max-width: 480px) {
            .chat-header {
                padding: 10px 15px;
            }
            .message-container {
                padding: 10px 10px 70px 10px;
                height: calc(100vh - 160px);
                max-height: calc(100vh - 160px);
                overflow-y: auto;
                min-height: 0;
                margin-bottom: 0;
            }
            .message-input-container {
                padding: 10px;
                height: auto;
                min-height: 60px;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            }
            .room-item {
                padding: 12px !important;
            }
            .user-avatar {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }
            .message-bubble {
                max-width: 90%;
                font-size: 13px;
                padding: 8px 12px;
            }
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <link rel="stylesheet"  as="style" href="{{ asset('css/style.css') }}">
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
                            <li class="nav-item dropdown me-3">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('home') }}">
                                        <i class="bi bi-house me-2"></i>Home
                                    </a>
                                    <a class="dropdown-item" href="{{ route('chat') }}">
                                        <i class="bi bi-chat-dots me-2"></i>Chat
                                    </a>
                                    <hr class="dropdown-divider">
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        <i class="bi bi-box-arrow-right me-2"></i>{{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                            
                            <!-- Direct Logout Button -->
                            <li class="nav-item">
                                <a class="btn btn-outline-danger btn-sm" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                             if(confirm('Are you sure you want to logout?')) {
                                                 document.getElementById('logout-form').submit();
                                             }">
                                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                                </a>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4" style="height: calc(100vh - 76px); overflow: hidden;">
            @yield('content')
        </main>
    </div>
    @stack('scripts')
    
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
