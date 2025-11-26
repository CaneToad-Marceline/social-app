# 🌐 Social App - Twitter & Reddit Inspired Social Media Platform

A full-featured social media web application combining the best features of Twitter (X) and Reddit, built with vanilla PHP, MySQL, HTML, CSS, and JavaScript.

![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-blue)
![MySQL Version](https://img.shields.io/badge/MySQL-5.7%2B-orange)
![License](https://img.shields.io/badge/license-MIT-green)

## ✨ Features

### 🔐 Authentication & Security
- User registration with validation
- Secure login system with session management
- Password encryption using bcrypt (`password_hash()`)
- Protected routes and authorization checks
- Admin role management

### 📝 Core Functionality (CRUD)
- **Create**: Post text updates with optional images
- **Read**: Browse all posts in a dynamic feed
- **Update**: Edit profile information, bio, profile picture, and password
- **Delete**: Remove your own posts (admins can delete any post)

### 💬 Social Features
- Like/Unlike posts with real-time AJAX updates
- Comment system with instant feedback
- User profiles with post history
- Image uploads for posts and profile pictures
- Post statistics (likes count, comments count)

### 👨‍💼 Admin Dashboard
- User management (view all users, delete users)
- Post moderation (view all posts, delete any post)
- Statistics dashboard (total users, posts, comments)
- **Export posts to CSV** for data analysis

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **XAMPP** / **WAMP** / **MAMP** / **LAMP** (Apache + MySQL + PHP)
  - PHP 7.4 or higher
  - MySQL 5.7 or higher
  - Apache 2.4 or higher
- A web browser (Chrome, Firefox, Edge, etc.)
- A text editor (VS Code, Notepad++, Sublime Text, etc.)

## 🚀 Installation Guide

### Step 1: Download the Project

```bash
# Clone this repository
git clone https://github.com/yourusername/social-app.git

# Or download as ZIP and extract
```

### Step 2: Move to Web Server Directory

Move the `social_app` folder to your web server's root directory:

**For XAMPP (Windows):**
```
C:\xampp\htdocs\social_app\
```

**For WAMP (Windows):**
```
C:\wamp64\www\social_app\
```

**For MAMP (Mac):**
```
/Applications/MAMP/htdocs/social_app/
```

**For Linux:**
```
/var/www/html/social_app/
```

### Step 3: Create the Database

1. Start your Apache and MySQL services from XAMPP/WAMP/MAMP control panel
2. Open your browser and go to: `http://localhost/phpmyadmin/`
3. Click on "**New**" in the left sidebar
4. Create a database named: `social_app`
5. Click on the `social_app` database
6. Go to the "**SQL**" tab
7. Copy and paste the following SQL code:

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_picture VARCHAR(255) DEFAULT 'default-avatar.png',
    bio TEXT,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    image VARCHAR(255),
    likes_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE comments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE likes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    post_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (post_id, user_id),
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO users (username, email, password, is_admin) 
VALUES ('admin', 'admin@social.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);
```

8. Click "**Go**" to execute

### Step 4: Configure Database Connection

1. Open `config.php` in your text editor
2. Update the database credentials if needed (default settings work for most XAMPP installations):

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'social_app');
```

3. Update the BASE_URL to match your setup:

```php
define('BASE_URL', 'http://localhost/social_app/');
```

### Step 5: Set Up Upload Directories

The application should automatically create the upload directories, but if needed, manually create:

```
social_app/
└── uploads/
    ├── profiles/
    │   └── default-avatar.png
    └── posts/
```

**Add a default avatar:**
- Find or create a simple profile picture (PNG format recommended)
- Rename it to `default-avatar.png`
- Place it in `uploads/profiles/`

### Step 6: Set Permissions (Linux/Mac only)

```bash
chmod -R 755 social_app/
chmod -R 777 social_app/uploads/
```

### Step 7: Access the Application

Open your web browser and navigate to:

```
http://localhost/social_app/
```

You'll be redirected to the login page. Use these credentials to access the admin account:

```
Username: admin
Password: admin123
```

## 📁 Project Structure

```
social_app/
├── config.php              # Database configuration and helper functions
├── index.php               # Main feed page
├── login.php               # User login page
├── register.php            # User registration page
├── profile.php             # User profile and settings
├── admin.php               # Admin dashboard
├── navbar.php              # Navigation bar component
├── create_post.php         # Create post handler
├── delete_post.php         # Delete post handler
├── delete_user.php         # Delete user handler (admin only)
├── ajax.php                # AJAX handlers (likes, comments)
├── logout.php              # Logout handler
├── style.css               # Main stylesheet
├── script.js               # JavaScript functionality
└── uploads/                # Upload directory
    ├── profiles/           # User profile pictures
    └── posts/              # Post images
```

## 🔧 How It Works

### Authentication Flow

1. **Registration** (`register.php`):
   - User submits registration form
   - Server validates input (username length, email format, password strength)
   - Password is hashed using `password_hash()` with bcrypt
   - User data is stored in the database
   - User is redirected to login page

2. **Login** (`login.php`):
   - User submits credentials
   - Server queries database for matching username/email
   - Password is verified using `password_verify()`
   - Session variables are set (`user_id`, `username`, `is_admin`)
   - User is redirected to main feed

3. **Session Management** (`config.php`):
   - Every protected page checks if user is logged in using `isLoggedIn()`
   - Admin pages additionally check `isAdmin()`
   - Unauthorized users are redirected appropriately

### Post Creation Flow

1. User fills out post form with text and optional image
2. Form submits to `create_post.php` via POST
3. Server validates and sanitizes input
4. If image is uploaded:
   - File extension is validated
   - Unique filename is generated using `uniqid()`
   - Image is moved to `uploads/posts/`
5. Post data is inserted into database
6. User is redirected to feed

### Like System (AJAX)

1. User clicks like button
2. JavaScript sends AJAX request to `ajax.php` with action `toggle_like`
3. Server checks if user already liked the post:
   - If liked: Remove like from database, decrement count
   - If not liked: Add like to database, increment count
4. Server returns JSON response with new like status and count
5. JavaScript updates UI without page reload

### Comment System (AJAX)

1. User submits comment form
2. JavaScript sends AJAX request to `ajax.php` with action `add_comment`
3. Server validates and inserts comment into database
4. Server returns JSON with new comment data
5. JavaScript appends new comment to the comments list
6. Comment count is updated in real-time

### Admin Features

**User Management:**
- Admin can view all users with statistics (post count, comment count)
- Admin can delete any user (except themselves)
- Deleting a user automatically removes all their posts, comments, and likes (CASCADE)

**Post Export:**
- Admin clicks "Export to CSV" button
- Server queries all posts with user information
- Data is formatted as CSV
- Browser downloads the file automatically

## 🛡️ Security Features

- **Password Hashing**: All passwords are hashed using bcrypt via `password_hash()`
- **SQL Injection Prevention**: Uses `mysqli_real_escape_string()` for input sanitization
- **Session Management**: Secure session handling with logout functionality
- **XSS Prevention**: User input is escaped using `htmlspecialchars()` before display
- **File Upload Validation**: Checks file extensions and uses unique filenames
- **Authorization Checks**: Protected routes verify user authentication and admin status
- **CSRF Protection**: Consider adding CSRF tokens for production use

## 🎨 Customization

### Change Theme Colors

Edit `style.css` and modify these CSS variables:

```css
/* Primary color (buttons, links) */
background: #1877f2;

/* Background colors */
background: #f0f2f5;

/* Text colors */
color: #050505;
```

### Modify Database Settings

Edit `config.php` to change database connection settings or add new helper functions.

### Add New Features

The modular structure makes it easy to add new features:
- Create new PHP files for new pages
- Add routes in navigation (`navbar.php`)
- Extend database schema as needed
- Add AJAX handlers in `ajax.php`

## 📊 Database Schema

### Users Table
- `id`: Primary key
- `username`: Unique username
- `email`: Unique email address
- `password`: Hashed password
- `profile_picture`: Filename of profile picture
- `bio`: User biography
- `is_admin`: Admin flag (0 or 1)
- `created_at`: Registration timestamp

### Posts Table
- `id`: Primary key
- `user_id`: Foreign key to users table
- `content`: Post text content
- `image`: Optional image filename
- `likes_count`: Number of likes
- `created_at`: Post timestamp

### Comments Table
- `id`: Primary key
- `post_id`: Foreign key to posts table
- `user_id`: Foreign key to users table
- `content`: Comment text
- `created_at`: Comment timestamp

### Likes Table
- `id`: Primary key
- `post_id`: Foreign key to posts table
- `user_id`: Foreign key to users table
- `created_at`: Like timestamp
- Unique constraint on (post_id, user_id)

## 🐛 Troubleshooting

### "Not Found" Error
- Ensure folder name matches the URL (e.g., `social_app` not `social-app`)
- Check that Apache is running in XAMPP/WAMP/MAMP control panel
- Verify files are in the correct htdocs/www directory

### Database Connection Failed
- Verify MySQL is running
- Check database credentials in `config.php`
- Ensure database `social_app` exists in phpMyAdmin

### Upload Errors
- Check that `uploads/` directory exists
- Verify folder permissions (777 on Linux/Mac)
- Ensure `php.ini` allows file uploads

### Images Not Displaying
- Check file paths in `config.php` (BASE_URL and UPLOAD_DIR)
- Verify images are in correct folders
- Check browser console for 404 errors

### Session Errors
- Ensure `session_start()` is called in `config.php`
- Check PHP session settings in `php.ini`
- Clear browser cookies and try again

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

## 👤 Author

Created with ❤️ by CaneToad

## 🙏 Acknowledgments

- Inspired by Twitter (X) and Reddit
- Built with vanilla PHP, MySQL, HTML, CSS, and JavaScript
- Icons and emojis from system defaults
- Thanks to the open-source community

## 📧 Contact

For questions or support, please open an issue on GitHub.

---

**⭐ If you find this project useful, please consider giving it a star on GitHub!**