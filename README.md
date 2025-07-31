# Blog Application - Vue.js + Inertia.js + Laravel

A modern, full-stack blog application built with Vue.js 3, Inertia.js, and Laravel 11, featuring real-time interactions, responsive design, and advanced user experience optimizations.

## 🚀 Features

### Core Functionality
- **User Authentication** - Registration, login, password reset with email verification
- **Post Management** - Create, read, update, delete blog posts with rich text content
- **Image Support** - Single and multiple image uploads per post
- **Comments System** - Nested comments with replies support
- **Like System** - Real-time post likes with instant feedback
- **Search** - Full-text search across posts, content, and authors
- **User Profiles** - Public and private profile management

### Advanced Features
- **Infinite Scroll** - Automatic loading of more posts as you scroll
- **Optimistic UI Updates** - Instant like/unlike feedback before server confirmation
- **Hover Animations** - Smooth padding animations on post hover
- **Responsive Design** - Mobile-first approach with Tailwind CSS
- **Image Gallery** - Multi-image support with preview grid
- **Real-time Updates** - Like counts sync when returning to pages
- **Performance Optimization** - Selective delays for UX testing, instant asset loading

### Technical Highlights
- **Vue 3 Composition API** - Modern reactive components
- **Inertia.js SPA** - Server-side routing with SPA user experience
- **Laravel 11** - Latest PHP framework with modern architecture
- **Tailwind CSS** - Utility-first styling with custom animations
- **Vite** - Fast build tool and hot module replacement
- **SQLite Database** - Lightweight development database

## 🛠 Tech Stack

### Frontend
- **Vue.js 3** - Progressive JavaScript framework
- **Inertia.js** - Modern SPA without API complexity
- **Tailwind CSS** - Utility-first CSS framework
- **Vite** - Next-generation frontend tooling

### Backend
- **Laravel 11** - PHP web application framework
- **SQLite** - File-based database for simplicity
- **Laravel Breeze** - Authentication scaffolding

### Development Tools
- **Composer** - PHP dependency management
- **npm** - Node.js package management
- **Laravel Artisan** - Command-line interface

## 📋 Prerequisites

Before running this application, make sure you have:

- **PHP >= 8.2** with required extensions
- **Composer** (latest version)
- **Node.js >= 18.x** and npm
- **Git** for version control

### Required PHP Extensions
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- BCMath PHP Extension
- Fileinfo PHP Extension
- GD PHP Extension (for image processing)

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd blog-app
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node.js Dependencies
```bash
npm install
```

### 4. Environment Setup
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 5. Database Setup
```bash
# Run database migrations
php artisan migrate

# (Optional) Seed sample data
php artisan db:seed
```

### 6. Storage Setup
```bash
# Create symbolic link for public storage
php artisan storage:link
```

### 7. Build Assets
```bash
# Development build
npm run dev

# Production build
npm run build
```

## 🏃‍♂️ Running the Application

### Development Mode
```bash
# Start Laravel development server
php artisan serve

# In another terminal, start Vite dev server (for hot reloading)
npm run dev
```

The application will be available at `http://localhost:8000`

### Production Mode
```bash
# Build assets for production
npm run build

# Start server (use a proper web server like Apache/Nginx in production)
php artisan serve
```

## 📁 Project Structure

```
blog-app/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Application controllers
│   │   └── Middleware/           # Custom middleware (SleepMiddleware)
│   └── Models/                   # Eloquent models (User, Post, Like, Comment)
├── bootstrap/
│   └── app.php                   # Application bootstrap configuration
├── database/
│   ├── migrations/               # Database schema migrations
│   └── seeders/                  # Database seeders
├── public/
│   ├── build/                    # Compiled assets (auto-generated)
│   └── storage/                  # Public storage link
├── resources/
│   ├── css/
│   │   └── app.css              # Main stylesheet
│   ├── js/
│   │   ├── Components/          # Vue.js components
│   │   ├── Layouts/             # Page layouts (App, Guest, Authenticated)
│   │   ├── Pages/               # Inertia.js pages
│   │   │   ├── Auth/            # Authentication pages
│   │   │   ├── Posts/           # Blog post pages
│   │   │   └── Profile/         # User profile pages
│   │   └── app.js               # Main JavaScript entry point
│   └── views/
│       └── app.blade.php        # Main HTML template
├── routes/
│   ├── web.php                  # Web routes
│   └── auth.php                 # Authentication routes
├── storage/
│   └── app/public/              # File storage (images, etc.)
├── .env                         # Environment configuration
├── composer.json                # PHP dependencies
├── package.json                 # Node.js dependencies
├── tailwind.config.js           # Tailwind CSS configuration
└── vite.config.js              # Vite build configuration
```

## 🎯 Key Features Explained

### Real-time Like System
- Instant UI feedback using optimistic updates
- Server synchronization with error handling and rollback
- Like counts persist across page navigation

### Infinite Scroll
- Automatic loading of posts as user scrolls
- Debounced search with real-time filtering
- Pagination with "Load More" fallback

### Image Management
- Single and multiple image uploads
- Automatic storage in `storage/app/public/posts/`
- Image preview grid with overflow indicators

### Performance Optimizations
- **SleepMiddleware**: Selective 2-second delays for UX testing
- **Static Asset Optimization**: Instant loading of CSS/JS/images
- **Caching**: Post caching with Redis/file cache support
- **Database Optimization**: Eager loading and query optimization

### Responsive Design
- Mobile-first approach
- Hover animations and transitions
- Adaptive image grids
- Touch-friendly interactions

## 🔧 Configuration

### Environment Variables (.env)
```env
APP_NAME="Blog Application"
APP_ENV=local
APP_KEY=base64:... # Generated by artisan key:generate
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# Mail configuration (for password reset)
MAIL_MAILER=log
```

### Tailwind CSS Custom Animations
The application includes custom animations defined in `tailwind.config.js`:
- `fadeInSlideLeft` - Page entrance animation
- `fadeOutSlideRight` - Page exit animation
- Hover transitions and padding animations

## 🚦 API Endpoints

### Authentication Routes
- `GET /login` - Login page
- `POST /login` - Process login
- `GET /register` - Registration page
- `POST /register` - Process registration
- `POST /logout` - Logout user

### Post Routes
- `GET /` - Home page (posts listing)
- `GET /posts/{id}` - View single post
- `GET /posts/create` - Create post form (auth required)
- `POST /posts` - Store new post (auth required)
- `GET /posts/{id}/edit` - Edit post form (auth required)
- `PUT /posts/{id}` - Update post (auth required)
- `DELETE /posts/{id}` - Delete post (auth required)

### Interaction Routes
- `POST /posts/{id}/like` - Toggle like (auth required)
- `POST /posts/{id}/comments` - Add comment (auth required)
- `PUT /comments/{id}` - Update comment (auth required)
- `DELETE /comments/{id}` - Delete comment (auth required)

### API Routes
- `GET /api/posts` - JSON API for pagination

## 🎨 UI Components

### Layout Components
- **AppLayout** - Main authenticated layout with navigation
- **GuestLayout** - Clean layout for auth pages (no Laravel logo)
- **AuthenticatedLayout** - Dashboard and profile layout

### Reusable Components
- **PrimaryButton** - Styled action buttons
- **TextInput** - Form input fields with validation
- **InputLabel** - Consistent form labels
- **Dropdown** - Navigation dropdown menus

### Page Components
- **Posts/Index** - Main blog listing with search and infinite scroll
- **Posts/Show** - Individual post view with comments
- **Posts/Create** - Post creation form with image upload
- **Profile/Show** - User profile management

## 🧪 Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
```

### Manual Testing Checklist
- [ ] User registration and login
- [ ] Post creation with images
- [ ] Like functionality (instant feedback)
- [ ] Comment system with replies
- [ ] Search functionality
- [ ] Responsive design on mobile
- [ ] Image uploads and display
- [ ] Performance (asset loading times)

## 🔒 Security Features

- **CSRF Protection** - All forms protected with CSRF tokens
- **Authentication** - Laravel Breeze authentication system
- **File Upload Security** - Image validation and secure storage
- **SQL Injection Protection** - Eloquent ORM with parameter binding
- **XSS Protection** - Vue.js automatic escaping

## 🚀 Deployment

### Production Deployment Steps
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Configure proper database connection
4. Set up proper web server (Apache/Nginx)
5. Configure file permissions
6. Set up SSL certificate
7. Configure mail service for password reset

### Recommended Production Setup
- **Web Server**: Nginx or Apache
- **Database**: MySQL or PostgreSQL
- **Cache**: Redis
- **Queue**: Redis or database
- **Storage**: Local or cloud storage (S3)
- **Mail**: SMTP service (SendGrid, Mailgun)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Guidelines
- Follow Laravel and Vue.js best practices
- Write descriptive commit messages
- Add tests for new features
- Update documentation as needed
- Ensure responsive design compatibility

## 📝 License

This project is open-source and available under the [MIT License](LICENSE).

## 🙏 Acknowledgments

- **Laravel Team** - Amazing PHP framework
- **Vue.js Team** - Progressive JavaScript framework
- **Inertia.js** - Modern SPA experience
- **Tailwind CSS** - Utility-first CSS framework
- **Community Contributors** - For inspiration and solutions

## 📞 Support

For support and questions:
- Create an issue in the repository
- Check existing documentation
- Review Laravel and Vue.js official documentation

---

**Made with ❤️ using Vue.js, Inertia.js, and Laravel**
