# 💬 Laravel Socket.IO Real-time Chat Application

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-12.x-red?style=for-the-badge&logo=laravel)
![Socket.IO](https://img.shields.io/badge/Socket.IO-4.8.1-black?style=for-the-badge&logo=socket.io)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.2.3-purple?style=for-the-badge&logo=bootstrap)
![PHP](https://img.shields.io/badge/PHP-8.2+-blue?style=for-the-badge&logo=php)
![MySQL](https://img.shields.io/badge/MySQL-5.7+-orange?style=for-the-badge&logo=mysql)

**A stunning WhatsApp-like real-time chat application built with Laravel and Socket.IO**

[🚀 Quick Start](#-quick-start) • [📱 Features](#-features) • [🛠 Installation](#-installation) • [💻 Usage](#-usage) • [🔧 Troubleshooting](#-troubleshooting)

## 📸 Screenshots

<div align="center">

### 🏠 Dashboard Home Screen
![Dashboard Home Screen](https://i.prnt.sc/g9CeT4zWLIzU)

### 💬 Chat Room Interface
![Chat Room Interface](https://i.prnt.sc/5nr8gKYsR31x)

### 🔐 Login Page
![Login Page](https://i.prnt.sc/QZzWfiKkJvyL)

### 📝 Registration Page
![Registration Page](https://i.prnt.sc/7h2Q5FB9nQDA)

</div>

</div>

---

## 🌟 What is this?

This is a **real-time chat application** that brings the power of modern messaging to your Laravel application! Think WhatsApp, but built with cutting-edge web technologies. It features instant messaging, online status tracking, typing indicators, and message delivery receipts - all wrapped in a beautiful, responsive interface.

<div align="center">
<img src="https://i.prnt.sc/5nr8gKYsR31x" alt="Chat Application Preview" width="80%" style="border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
</div>

### 🎯 Perfect for:
- **Learning** real-time web development
- **Building** chat features for your apps
- **Understanding** WebSocket communication
- **Creating** modern messaging interfaces

---

## ✨ Features

### 🚀 **Real-time Magic**
- ⚡ **Instant messaging** - Messages appear instantly
- 👀 **Typing indicators** - See when someone is typing
- 🟢 **Online status** - Know who's online/offline
- ✅ **Message receipts** - Track delivery and read status
- 🔄 **Auto-reconnection** - Never lose connection

### 💬 **WhatsApp-like Experience**
- 🎨 **Beautiful UI** - Clean, modern design
- 💭 **Message bubbles** - Sent/received message styling
- 📱 **Mobile responsive** - Works on all devices
- ⏰ **Timestamps** - See when messages were sent
- 🔢 **Unread counts** - Badge notifications

### 🔐 **Security & Performance**
- 🛡️ **Laravel Sanctum** - Secure API authentication
- 🔒 **Token-based auth** - Modern authentication
- 🗄️ **MySQL database** - Reliable data storage
- ⚡ **Optimized queries** - Fast performance

## 🎨 **Visual Features Showcase**

<div align="center">

### 💬 **Real-time Messaging**
![Chat Interface](https://i.prnt.sc/5nr8gKYsR31x)
*WhatsApp-like interface with message bubbles, online status, and typing indicators*

### 🏠 **Clean Dashboard**
![Dashboard](https://i.prnt.sc/g9CeT4zWLIzU)
*Modern, responsive dashboard with easy navigation*

### 🔐 **Secure Authentication**
![Login](https://i.prnt.sc/QZzWfiKkJvyL)
*Clean login interface with Laravel authentication*

### 📝 **User Registration**
![Registration](https://i.prnt.sc/7h2Q5FB9nQDA)
*Simple registration process for new users*

</div>

## 📱 **Page Overview**

| Page | Description | Key Features |
|------|-------------|--------------|
| 🏠 **Dashboard** | Main landing page after login | Navigation menu, user info, quick access to chat |
| 💬 **Chat Room** | Real-time messaging interface | Message bubbles, online status, typing indicators, user list |
| 🔐 **Login** | User authentication | Email/password login, remember me, secure validation |
| 📝 **Register** | New user registration | User creation, form validation, automatic login |

<div align="center">

### 🖼️ **Complete Application Flow**
<img src="https://i.prnt.sc/g9CeT4zWLIzU" alt="Dashboard" width="45%" style="margin: 5px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
<img src="https://i.prnt.sc/5nr8gKYsR31x" alt="Chat Interface" width="45%" style="margin: 5px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

<img src="https://i.prnt.sc/QZzWfiKkJvyL" alt="Login Page" width="45%" style="margin: 5px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
<img src="https://i.prnt.sc/7h2Q5FB9nQDA" alt="Registration Page" width="45%" style="margin: 5px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

</div>

---

## 🛠 Technology Stack

<table>
<tr>
<td align="center"><strong>Backend</strong></td>
<td align="center"><strong>Real-time</strong></td>
<td align="center"><strong>Frontend</strong></td>
<td align="center"><strong>Database</strong></td>
</tr>
<tr>
<td>

- 🐘 **Laravel 12.x**
- 🔧 **PHP 8.2+**
- 🔐 **Laravel Sanctum**

</td>
<td>

- ⚡ **Socket.IO 4.8.1**
- 🟢 **Node.js**
- 🌐 **WebSockets**

</td>
<td>

- 🎨 **Bootstrap 5**
- ⚡ **JavaScript ES6+**
- 📱 **jQuery 3.7.1**

</td>
<td>

- 🗄️ **MySQL 5.7+**
- 📊 **Optimized indexes**
- 🔄 **Real-time sync**

</td>
</tr>
</table>

---

## 🚀 Quick Start

### ⚡ **Super Quick Setup** (5 minutes!)

   ```bash
# 1️⃣ Clone and setup
git clone <your-repo-url>
   cd laravel-socket.io

# 2️⃣ Install everything
composer install && npm install

# 3️⃣ Configure environment
cp .env.example .env
php artisan key:generate

# 4️⃣ Setup database (create MySQL database first)
php artisan migrate

# 5️⃣ Start both servers (2 terminals)
# Terminal 1:
php artisan serve --host=127.0.0.1 --port=8080

# Terminal 2:
node socket.js

# 6️⃣ Open browser
# Go to: http://127.0.0.1:8080
```

**🎉 That's it! You're ready to chat!**

---

## 📋 Prerequisites

Before we begin, make sure you have these installed:

### ✅ **Required Software**

| Software | Version | Download Link |
|----------|---------|---------------|
| 🐘 **PHP** | 8.2+ | [php.net](https://www.php.net/downloads.php) |
| 📦 **Composer** | Latest | [getcomposer.org](https://getcomposer.org/download/) |
| 🟢 **Node.js** | 16+ | [nodejs.org](https://nodejs.org/) |
| 🗄️ **MySQL** | 5.7+ | [mysql.com](https://dev.mysql.com/downloads/) |
| 🔧 **Git** | Latest | [git-scm.com](https://git-scm.com/downloads) |

### 🖥️ **System Requirements**
- **RAM**: 2GB minimum (4GB recommended)
- **Storage**: 1GB free space
- **OS**: Windows 10+, macOS 10.14+, or Linux

---

## 🛠 Installation

### Step 1: 📥 **Clone the Repository**

   ```bash
# Clone the project
git clone <your-repository-url>
cd laravel-socket.io

# Or if you already have it locally
cd C:\Users\Subhash\Documents\GitHub\laravel-socket.io
```

### Step 2: 📦 **Install Dependencies**

   ```bash
# Install PHP packages
composer install

# Install Node.js packages
   npm install
   ```

**What this installs:**
- 🐘 Laravel Framework & Sanctum
- ⚡ Socket.IO & Express.js
- 🎨 Bootstrap & jQuery
- 🔧 Build tools (Vite)

### Step 3: ⚙️ **Environment Configuration**

   ```bash
# Copy environment file
   cp .env.example .env

# Generate application key
   php artisan key:generate
   ```

**Edit your `.env` file:**
```env
APP_NAME="Laravel Socket Chat"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8080

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_socket_chat
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### Step 4: 🗄️ **Database Setup**

**Create MySQL Database:**
```sql
CREATE DATABASE laravel_socket_chat;
```

**Run Migrations:**
   ```bash
   php artisan migrate
   ```

**This creates:**
- 👥 `users` table (user accounts)
- 💬 `messages` table (chat messages)
- 🔐 `personal_access_tokens` table (API auth)
- 📊 `sessions` table (user sessions)

### Step 5: 🚀 **Start the Servers**

**You need TWO terminals running:**

#### 🖥️ **Terminal 1 - Laravel Server**
   ```bash
   php artisan serve --host=127.0.0.1 --port=8080
```
**Expected output:**
```
Starting Laravel development server: http://127.0.0.1:8080
   ```
   
#### ⚡ **Terminal 2 - Socket.IO Server**
   ```bash
   node socket.js
   ```
**Expected output:**
```
Socket.IO server running on port 3000
```

### Step 6: 🌐 **Access the Application**

1. **Open your browser**
2. **Go to:** `http://127.0.0.1:8080`
3. **Register** a new account or **Login**
4. **Click "Chat"** to start messaging!

---

## 💻 Usage Guide

### 🎯 **Getting Started**

#### 1. **First Time Setup**
- 🏠 Visit the home page
- 📝 Click **"Register"** to create account
- 🔐 Or **"Login"** if you have an account

#### 2. **Accessing Chat**
- 💬 Click **"Chat"** in navigation
- 👥 See list of all users
- 🖱️ Click any user to start conversation

#### 3. **Sending Messages**
- ✍️ Type your message
- ⏎ Press **Enter** or click **Send**
- ⚡ Message appears instantly!

### 💬 **Chat Features**

#### 📱 **Message Status Indicators**
| Icon | Status | Meaning |
|------|--------|---------|
| ⏰ | Sending | Message being sent to server |
| ✅ | Delivered | Message reached recipient's device |
| ✅✅ | Read | Message has been read by recipient |

#### 🟢 **Online Status**
- **Green dot** = User is online
- **Gray dot** = User is offline
- **Real-time updates** = Status changes instantly

#### ⌨️ **Typing Indicators**
- See when someone is typing
- Auto-hide after 1 second of inactivity
- Real-time updates via Socket.IO

#### 📱 **Mobile Experience**
- **Responsive design** works on all devices
- **Touch-friendly** interface
- **Collapsible sidebar** for mobile

### 🔧 **Advanced Features**

#### 📊 **Message History**
- All messages stored in database
- Previous conversations load automatically
- Messages persist between sessions

#### 🔔 **Unread Notifications**
- Red badges show unread count
- Counts reset when opening conversation
- Real-time updates for new messages

#### 👀 **Last Message Preview**
- See last message in each conversation
- Timestamps show when sent
- Updates in real-time

---

## 🔌 API Documentation

### 🔐 **Authentication**
All API endpoints require authentication via Laravel Sanctum token.

**Headers Required:**
```http
Authorization: Bearer {your_api_token}
Content-Type: application/json
```

### 📡 **API Endpoints**

#### 💬 **Send Message**
```http
POST /api/send
```
**Request:**
```json
{
    "to": 2,
    "message": "Hello, how are you?"
}
```
**Response:**
```json
{
    "success": true,
    "id": 123
}
```

#### 📥 **Get Pending Messages**
```http
GET /api/pending
```
**Response:**
```json
[
    {
        "id": 123,
        "from": 2,
        "from_name": "John Doe",
        "message": "Hello there!",
        "sent_at": "2024-01-15T10:30:00.000000Z"
    }
]
```

#### 💭 **Get Conversation**
```http
GET /api/messages?with=2
```
**Response:**
```json
[
    {
        "id": 123,
        "from": 1,
        "from_name": "Jane Smith",
        "message": "Hello!",
        "sent_at": "2024-01-15T10:30:00.000000Z",
        "is_me": true
    }
]
```

#### ✅ **Mark as Delivered**
```http
POST /api/mark-delivered/123
```

#### 👀 **Mark as Read**
```http
POST /api/mark-read/123
```

#### 📋 **Get Last Message**
```http
GET /api/last-message/2
```

---

## ⚡ Socket.IO Events

### 📤 **Client to Server Events**

```javascript
// Join user room
socket.emit('join', userId);

// Typing indicator
socket.emit('typing', {
    user_id: userId,
    user_name: userName,
    to: recipientId
});

// Stop typing
socket.emit('stop-typing', {
    user_id: userId,
    to: recipientId
});

// Message read
socket.emit('message-read', {
    message_id: messageId,
    from: senderId,
    to: recipientId
});
```

### 📥 **Server to Client Events**

```javascript
// User came online
socket.on('online', (data) => {
    console.log('User online:', data.user_id);
});

// New message received
socket.on('new-message', (data) => {
    console.log('New message:', data);
});

// Someone is typing
socket.on('typing', (data) => {
    console.log('User typing:', data.user_name);
});

// Message was read
socket.on('message-read', (data) => {
    console.log('Message read:', data.message_id);
});
```

---

## 🗄️ Database Schema

### 👥 **Users Table**
```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    api_token VARCHAR(80) UNIQUE NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### 💬 **Messages Table**
```sql
CREATE TABLE messages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    from_id BIGINT NOT NULL,
    to_id BIGINT NOT NULL,
    message TEXT NOT NULL,
    delivered BOOLEAN DEFAULT 0,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (from_id) REFERENCES users(id),
    FOREIGN KEY (to_id) REFERENCES users(id)
);
```

---

## 🔧 Troubleshooting

### 🚨 **Common Issues & Solutions**

#### 1. **Socket.IO Connection Failed**
**Problem:** Browser shows connection errors
**Solutions:**
- ✅ Ensure Socket.IO server is running on port 3000
- ✅ Check firewall settings
- ✅ Verify CORS configuration
- ✅ Check browser console for errors

#### 2. **Laravel Server Won't Start**
**Problem:** `php artisan serve` fails
**Solutions:**
- ✅ Check if port 8080 is in use
- ✅ Try different port: `php artisan serve --port=8081`
- ✅ Verify PHP version: `php --version`
- ✅ Check Laravel: `php artisan --version`

#### 3. **Database Connection Error**
**Problem:** Migration fails
**Solutions:**
- ✅ Verify database credentials in `.env`
- ✅ Ensure MySQL service is running
- ✅ Check database exists
- ✅ Verify user permissions

#### 4. **Messages Not Sending**
**Problem:** Messages don't reach recipients
**Solutions:**
- ✅ Check Socket.IO server logs
- ✅ Verify API token is valid
- ✅ Check network connectivity
- ✅ Review browser console

#### 5. **Authentication Issues**
**Problem:** Can't login or API calls fail
**Solutions:**
- ✅ Clear browser cache and cookies
- ✅ Check session configuration
- ✅ Verify Sanctum setup
- ✅ Regenerate API tokens

### 🐛 **Debug Mode**

Enable detailed error information:
```env
# In .env file
APP_DEBUG=true
LOG_LEVEL=debug
```

Check logs:
```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Socket.IO logs (in terminal where node socket.js is running)
```

---

## 🚀 Deployment

### 🌐 **Production Setup**

#### 1. **Environment Configuration**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Use Redis for sessions in production
SESSION_DRIVER=redis
CACHE_DRIVER=redis
```

#### 2. **Server Requirements**
- 🐘 **PHP 8.2+** with required extensions
- 🗄️ **MySQL 5.7+** or **MariaDB 10.3+**
- 🟢 **Node.js 16+** and **NPM**
- 🌐 **Web Server**: Nginx or Apache
- ⚙️ **Process Manager**: PM2 or Supervisor

#### 3. **Deployment Steps**
```bash
# Install production dependencies
composer install --optimize-autoloader --no-dev
npm install --production

# Generate optimized assets
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Start services with PM2
pm2 start socket.js --name "socket-server"
pm2 start "php artisan serve --host=0.0.0.0 --port=8000" --name "laravel-server"
```

---

## 🎨 Customization

### 🎨 **Styling**
- Modify Bootstrap classes in templates
- Add custom CSS in `resources/css`
- Update JavaScript for UI interactions
- Test on different devices

### ⚡ **Adding Features**
1. Create new API endpoints in `MessageController`
2. Add new Socket.IO events in `socket.js`
3. Update frontend JavaScript in `chat.blade.php`
4. Add database migrations for new tables/columns

### 🔧 **Configuration**
- **Socket.IO port**: Modify `socket.js`
- **Laravel port**: Change in `php artisan serve`
- **Database**: Update `.env` file
- **CORS**: Configure in `socket.js`

---

## 🤝 Contributing

### 🚀 **How to Contribute**
1. 🍴 **Fork** the repository
2. 🌿 **Create** feature branch: `git checkout -b feature/amazing-feature`
3. 💾 **Commit** changes: `git commit -m 'Add amazing feature'`
4. 📤 **Push** to branch: `git push origin feature/amazing-feature`
5. 🔄 **Open** Pull Request

### 📋 **Code Standards**
- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add tests for new features
- Update documentation

---

## 📚 Additional Resources

### 📖 **Documentation**
- [Laravel Documentation](https://laravel.com/docs)
- [Socket.IO Documentation](https://socket.io/docs/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)
- [Laravel Sanctum Documentation](https://laravel.com/docs/sanctum)

### 🎓 **Learning Resources**
- [Laracasts](https://laracasts.com) - Video tutorials
- [Laravel Daily](https://laraveldaily.com) - Tips and tutorials
- [Socket.IO Examples](https://github.com/socketio/socket.io/tree/master/examples)

### 💬 **Community Support**
- [Laravel Discord](https://discord.gg/laravel)
- [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel)
- [Laravel Forums](https://laracasts.com/discuss)

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🆘 Support

Having issues? We're here to help!

1. 🔍 **Check** the troubleshooting section above
2. 🔎 **Search** existing issues in the repository
3. 🆕 **Create** a new issue with detailed information
4. 💬 **Join** the community for help and discussions

---

## 🎉 Conclusion

Congratulations! You now have a complete real-time chat application! 🎊

**What you've built:**
- ✅ Real-time messaging with Socket.IO
- ✅ Beautiful WhatsApp-like interface
- ✅ Message status tracking
- ✅ Online/offline status
- ✅ Typing indicators
- ✅ Mobile-responsive design
- ✅ Secure authentication

**Next steps:**
- 🚀 Deploy to production
- 🎨 Customize the design
- ⚡ Add new features
- 📱 Test on different devices

**Happy coding!** 🚀💻

---

<div align="center">

**Made with ❤️ using Laravel & Socket.IO**

[⭐ Star this repo](https://github.com/yourusername/laravel-socket.io) • [🐛 Report Bug](https://github.com/yourusername/laravel-socket.io/issues) • [💡 Request Feature](https://github.com/yourusername/laravel-socket.io/issues)

</div>