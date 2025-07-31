# Quick Setup Guide

This is a simplified setup guide for getting the blog application running quickly. For detailed documentation, see [README.md](README.md).

## ⚡ Quick Start (5 minutes)

### Prerequisites
- PHP 8.2+ installed
- Composer installed
- Node.js 18+ and npm installed

### Installation Commands
```bash
# 1. Clone and navigate
git clone <repository-url>
cd blog-app

# 2. Install dependencies
composer install
npm install

# 3. Environment setup
cp .env.example .env
php artisan key:generate

# 4. Database setup
php artisan migrate
php artisan storage:link

# 5. Build assets
npm run build

# 6. Start server
php artisan serve
```

### Access the Application
- **URL**: http://localhost:8000
- **Register**: Create a new account to start posting
- **Features**: Posts, comments, likes, search, image uploads

## 🚀 Development Mode

For development with hot reloading:
```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

## 📝 Key Features to Test

1. **User Registration** - Create account at `/register`
2. **Create Post** - Add new blog post with images
3. **Like System** - Instant like/unlike feedback  
4. **Comments** - Add and reply to comments
5. **Search** - Search posts and authors
6. **Responsive** - Test on mobile devices

## 🔧 Troubleshooting

### Common Issues
- **Database errors**: Run `php artisan migrate`
- **Asset errors**: Run `npm run build`
- **Storage errors**: Run `php artisan storage:link`
- **Permission errors**: Check file permissions for `storage/` and `bootstrap/cache/`

### Performance Notes
- Assets load instantly (optimized middleware)
- Like actions provide immediate feedback
- Page navigation has intentional 2-second delay for UX testing
- Search includes 500ms debouncing

## 📚 Next Steps

- Read the full [README.md](README.md) for detailed documentation
- Check [CONTRIBUTING.md](CONTRIBUTING.md) for development guidelines
- Review [CHANGELOG.md](CHANGELOG.md) for feature history

---

**🎉 You're ready to start blogging!**
