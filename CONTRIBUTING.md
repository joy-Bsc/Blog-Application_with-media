# Contributing to Blog Application

Thank you for your interest in contributing to this Vue.js + Inertia.js + Laravel blog application! This document provides guidelines and information for contributors.

## 🚀 Getting Started

### Prerequisites
- PHP >= 8.2 with required extensions
- Node.js >= 18.x and npm
- Composer (latest version)
- Git for version control

### Development Setup
1. Fork the repository
2. Clone your fork: `git clone <your-fork-url>`
3. Install dependencies: `composer install && npm install`
4. Set up environment: `cp .env.example .env && php artisan key:generate`
5. Run migrations: `php artisan migrate`
6. Start development servers: `php artisan serve` and `npm run dev`

## 📋 Development Guidelines

### Code Style
- **PHP**: Follow PSR-12 coding standards
- **JavaScript**: Use ES6+ features and Vue 3 Composition API
- **CSS**: Use Tailwind CSS utility classes, avoid custom CSS when possible
- **Vue Components**: Use `<script setup>` syntax with Composition API

### Project Structure
```
app/Http/Controllers/     # Laravel controllers
app/Models/              # Eloquent models
resources/js/Pages/      # Inertia.js pages (Vue components)
resources/js/Components/ # Reusable Vue components
resources/js/Layouts/    # Layout components
```

### Naming Conventions
- **Files**: PascalCase for Vue components (`PostCard.vue`)
- **Variables**: camelCase for JavaScript (`postData`)
- **Methods**: camelCase for functions (`toggleLike()`)
- **Routes**: kebab-case for URLs (`/posts/create`)

## 🛠 Development Workflow

### Branch Naming
- `feature/feature-name` - New features
- `bugfix/bug-description` - Bug fixes
- `improvement/description` - Code improvements
- `docs/description` - Documentation updates

### Commit Messages
Use descriptive commit messages:
```
feat: add real-time like system with optimistic updates
fix: resolve infinite scroll pagination issue
docs: update README with deployment instructions
style: improve hover animations on post cards
```

### Pull Request Process
1. Create a feature branch from `main`
2. Make your changes with appropriate tests
3. Update documentation if needed
4. Ensure all tests pass: `php artisan test`
5. Build assets: `npm run build`
6. Submit pull request with detailed description

## 🧪 Testing

### Running Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/PostTest.php

# Run with coverage
php artisan test --coverage
```

### Writing Tests
- Write feature tests for new endpoints
- Write unit tests for complex business logic
- Test both success and error scenarios
- Include edge cases and validation tests

### Manual Testing Checklist
- [ ] User registration and authentication flow
- [ ] Post CRUD operations with images
- [ ] Like/unlike functionality (instant feedback)
- [ ] Comment system with nested replies
- [ ] Search functionality with debouncing
- [ ] Responsive design on mobile devices
- [ ] File upload validation and storage
- [ ] Performance optimization (asset loading)

## 🎯 Areas for Contribution

### High Priority
- **Performance**: Database query optimization
- **Testing**: Increase test coverage
- **Accessibility**: ARIA labels and keyboard navigation
- **Mobile**: Touch gesture improvements
- **Security**: Additional validation and sanitization

### Medium Priority
- **Features**: Email notifications for comments/likes
- **UI/UX**: Dark mode toggle
- **Admin**: Admin panel for content moderation
- **API**: RESTful API for mobile app integration
- **SEO**: Meta tags and Open Graph implementation

### Low Priority
- **Integrations**: Social media sharing
- **Analytics**: User interaction tracking
- **Localization**: Multi-language support
- **Themes**: Customizable UI themes
- **Export**: Content export functionality

## 🐛 Bug Reports

### Before Reporting
1. Check existing issues
2. Reproduce the bug consistently
3. Test in different browsers/devices
4. Check browser console for errors

### Bug Report Template
```markdown
**Bug Description**
Clear description of the issue

**Steps to Reproduce**
1. Go to...
2. Click on...
3. See error

**Expected Behavior**
What should have happened

**Actual Behavior**
What actually happened

**Environment**
- OS: [e.g., Windows 11]
- Browser: [e.g., Chrome 120]
- PHP Version: [e.g., 8.2]
- Node Version: [e.g., 18.17]

**Screenshots**
If applicable, add screenshots
```

## 💡 Feature Requests

### Requesting Features
1. Check existing feature requests in issues
2. Describe the problem your feature solves
3. Provide detailed specifications
4. Consider implementation complexity
5. Include mockups/wireframes if helpful

### Feature Request Template
```markdown
**Feature Summary**
Brief description of the feature

**Problem Statement**
What problem does this solve?

**Proposed Solution**
How should this feature work?

**Alternative Solutions**
Other ways to solve this problem

**Additional Context**
Any other relevant information
```

## 🔧 Development Tips

### Performance Considerations
- Use Laravel's eager loading to prevent N+1 queries
- Implement caching for expensive operations
- Optimize images before upload
- Use Vite's build optimization for production
- Consider database indexing for search queries

### Security Best Practices
- Always validate user input
- Use CSRF protection for forms
- Sanitize data before database storage
- Implement proper file upload validation
- Use Laravel's built-in security features

### Vue.js Best Practices
- Use Composition API for complex logic
- Implement proper prop validation
- Use computed properties for derived data
- Handle loading and error states gracefully
- Optimize component re-renders

## 📞 Getting Help

### Resources
- **Laravel Documentation**: https://laravel.com/docs
- **Vue.js Documentation**: https://vuejs.org/guide/
- **Inertia.js Documentation**: https://inertiajs.com/
- **Tailwind CSS Documentation**: https://tailwindcss.com/docs

### Community Support
- Create an issue for bugs or questions
- Join discussions in existing issues
- Check the project's README for setup help
- Review existing code for implementation examples

## 📝 License

By contributing to this project, you agree that your contributions will be licensed under the MIT License.

---

**Thank you for contributing to making this blog application better!** 🚀
