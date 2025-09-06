# WhatsApp-like Chat Application

A real-time chat application built with Laravel, Socket.IO, and Bootstrap that mimics WhatsApp's interface and functionality.

## Features

### 🚀 Real-time Communication
- **Instant messaging** with Socket.IO for real-time message delivery
- **Typing indicators** to show when someone is typing
- **Online/Offline status** tracking for all users
- **Message delivery receipts** (single tick, double tick, read receipts)

### 💬 WhatsApp-like Interface
- **Clean, modern design** inspired by WhatsApp
- **Message bubbles** with proper styling for sent/received messages
- **User list sidebar** with online status indicators
- **Responsive design** that works on desktop and mobile
- **Message timestamps** and status indicators

### 🔐 Authentication & Security
- **Laravel Sanctum** for API authentication
- **Session-based** and **token-based** authentication
- **Secure message storage** with proper validation

### 📱 Advanced Features
- **Read receipts** - see when messages are read
- **Delivery status** - track message delivery
- **Unread message counts** in user list
- **Last message preview** in conversation list
- **Real-time online status** updates

## Technology Stack

- **Backend**: Laravel 12.x with PHP 8.2+
- **Real-time**: Socket.IO with Node.js
- **Frontend**: Bootstrap 5, JavaScript (ES6+)
- **Database**: MySQL with proper indexing
- **Authentication**: Laravel Sanctum

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL database

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd laravel-socket.io
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database configuration**
   - Update your `.env` file with database credentials
   - Run migrations:
   ```bash
   php artisan migrate
   ```

6. **Start the servers**
   
   **Terminal 1 - Laravel Server:**
   ```bash
   php artisan serve --host=127.0.0.1 --port=8080
   ```
   
   **Terminal 2 - Socket.IO Server:**
   ```bash
   node socket.js
   ```

7. **Access the application**
   - Open your browser and go to `http://127.0.0.1:8080`
   - Register/Login to start chatting

## Database Schema

### Messages Table
- `id` - Primary key
- `from_id` - Foreign key to users table
- `to_id` - Foreign key to users table  
- `message` - Text content
- `delivered` - Boolean delivery status
- `read_at` - Timestamp when message was read
- `created_at` - Message creation timestamp
- `updated_at` - Last update timestamp

### User Online Status Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `socket_id` - Unique socket connection ID
- `last_seen` - Last activity timestamp
- `is_online` - Current online status
- `created_at` - Record creation timestamp
- `updated_at` - Last update timestamp

## API Endpoints

### Authentication Required
All API endpoints require authentication via Laravel Sanctum token.

- `POST /api/send` - Send a message
- `GET /api/pending` - Get pending messages
- `GET /api/messages?with={user_id}` - Get conversation with specific user
- `POST /api/mark-delivered/{id}` - Mark message as delivered
- `POST /api/mark-read` - Mark messages as read
- `GET /api/online-users` - Get list of online users

## Socket.IO Events

### Client to Server
- `join` - Join user to their personal room
- `typing` - Send typing indicator
- `stop-typing` - Stop typing indicator
- `message-read` - Send read receipt
- `message-delivered` - Send delivery receipt

### Server to Client
- `online` - User came online
- `offline` - User went offline
- `online-users` - List of online users
- `new-message` - New message received
- `typing` - Someone is typing
- `stop-typing` - Someone stopped typing
- `message-read` - Message was read
- `message-delivered` - Message was delivered

## Features in Detail

### Real-time Messaging
- Messages are sent instantly using Socket.IO
- Automatic reconnection on connection loss
- Message queuing for offline users

### Online Status
- Real-time online/offline status updates
- Visual indicators in user list
- Last seen timestamps

### Message Status
- **Single tick (✓)**: Message sent
- **Double tick (✓✓)**: Message delivered
- **Blue double tick (✓✓)**: Message read

### Typing Indicators
- Shows when someone is typing
- Auto-hide after 1 second of inactivity
- Real-time updates via Socket.IO

## Customization

### Styling
The application uses Bootstrap 5 with custom CSS for WhatsApp-like appearance. Main styles are in `resources/views/layouts/app.blade.php`.

### Socket.IO Configuration
Socket.IO server runs on port 3000 by default. Configuration can be modified in `socket.js`.

### Database
Uses MySQL with proper indexing for optimal performance. Migrations are located in `database/migrations/`.

## Troubleshooting

### Common Issues

1. **Port conflicts**
   - Laravel server: Change port in `php artisan serve --port=XXXX`
   - Socket.IO server: Modify port in `socket.js`

2. **Database connection**
   - Check `.env` file for correct database credentials
   - Ensure MySQL service is running

3. **Socket.IO connection**
   - Verify Socket.IO server is running on port 3000
   - Check browser console for connection errors

4. **Authentication issues**
   - Clear browser cache and cookies
   - Check if API token is being generated correctly

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For support and questions, please open an issue in the repository.