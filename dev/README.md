# 📚 Developer Documentation - Lumi POS System

## Welcome Developer! 👋

This folder contains comprehensive technical documentation to help you understand, maintain, and extend the Lumi POS & Inventory Management System.

---

## 📖 Documentation Index

### Getting Started
- **[01-PROJECT-OVERVIEW.md](./01-PROJECT-OVERVIEW.md)** - High-level project architecture and tech stack
- **[02-SETUP-GUIDE.md](./02-SETUP-GUIDE.md)** - Installation and configuration instructions
- **[03-DATABASE-SCHEMA.md](./03-DATABASE-SCHEMA.md)** - Complete database structure and relationships

### Architecture & Design
- **[04-ARCHITECTURE.md](./04-ARCHITECTURE.md)** - Application architecture, SOLID principles, design patterns
- **[05-REPOSITORY-PATTERN.md](./05-REPOSITORY-PATTERN.md)** - Repository layer implementation
- **[06-SERVICE-LAYER.md](./06-SERVICE-LAYER.md)** - Business logic services

### Core Features
- **[07-AUTHENTICATION.md](./07-AUTHENTICATION.md)** - Auth system and middleware
- **[08-INVENTORY-MANAGEMENT.md](./08-INVENTORY-MANAGEMENT.md)** - Stock tracking and item management
- **[09-ORDER-SYSTEM.md](./09-ORDER-SYSTEM.md)** - POS and order processing
- **[10-DAY-SESSIONS.md](./10-DAY-SESSIONS.md)** - Business day management

### Advanced Features
- **[11-DISCOUNT-TAX-SYSTEM.md](./11-DISCOUNT-TAX-SYSTEM.md)** - Pricing, discounts, and tax calculations
- **[12-PDF-INVOICES.md](./12-PDF-INVOICES.md)** - Invoice generation and thermal receipts
- **[13-REPORTS-ANALYTICS.md](./13-REPORTS-ANALYTICS.md)** - Reporting and charts
- **[14-BILINGUAL-SYSTEM.md](./14-BILINGUAL-SYSTEM.md)** - Multi-language support (EN/AR)

### Development Guide
- **[15-API-ENDPOINTS.md](./15-API-ENDPOINTS.md)** - All routes and endpoints
- **[16-FRONTEND-GUIDE.md](./16-FRONTEND-GUIDE.md)** - Blade templates and JavaScript
- **[17-TESTING.md](./17-TESTING.md)** - Testing strategy and examples
- **[18-DEPLOYMENT.md](./18-DEPLOYMENT.md)** - Desktop deployment with PHP Desktop

### Extension & Customization
- **[19-ADDING-FEATURES.md](./19-ADDING-FEATURES.md)** - Step-by-step guide to add new features
- **[20-TROUBLESHOOTING.md](./20-TROUBLESHOOTING.md)** - Common issues and solutions

---

## 🚀 Quick Start

### For New Developers:
1. Read **[01-PROJECT-OVERVIEW.md](./01-PROJECT-OVERVIEW.md)** to understand the system
2. Follow **[02-SETUP-GUIDE.md](./02-SETUP-GUIDE.md)** to get the project running
3. Review **[04-ARCHITECTURE.md](./04-ARCHITECTURE.md)** to understand the code structure
4. Check **[19-ADDING-FEATURES.md](./19-ADDING-FEATURES.md)** before implementing new features

### For Feature Development:
1. Understand the feature domain (Inventory, Orders, Reports, etc.)
2. Review the relevant architecture documentation
3. Follow the repository pattern for data access
4. Use service layer for business logic
5. Implement proper validation with FormRequests
6. Add tests for your feature

---

## 🏗️ Project Structure Quick Reference

```
www/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # Thin controllers
│   │   ├── Middleware/         # AdminAuth, SetLocale
│   │   └── Requests/           # FormRequest validation
│   ├── Models/                 # Eloquent models
│   ├── Repositories/           # Data access layer
│   │   └── Contracts/          # Repository interfaces
│   ├── Services/               # Business logic
│   └── Providers/              # Service providers
├── config/
│   ├── cashier.php            # POS-specific config
│   └── ...                    # Laravel configs
├── database/
│   ├── migrations/            # Database schema
│   └── seeders/               # Test data
├── resources/
│   ├── views/                 # Blade templates
│   └── lang/                  # Translations (en, ar)
├── routes/
│   └── web.php               # All routes
└── dev/                      # This documentation folder
```

---

## 🔑 Key Concepts

### SOLID Principles
The application follows SOLID principles:
- **Single Responsibility**: Each class has one job
- **Open/Closed**: Open for extension, closed for modification
- **Liskov Substitution**: Interfaces are properly implemented
- **Interface Segregation**: Small, focused interfaces
- **Dependency Inversion**: Depend on abstractions, not concretions

### Design Patterns Used
- **Repository Pattern**: Data access abstraction
- **Service Layer Pattern**: Business logic separation
- **Dependency Injection**: Constructor-based DI
- **Form Request Pattern**: Validation separation
- **Observer Pattern**: Eloquent model events

### Technology Stack
- **Backend**: Laravel 12, PHP 8.2+
- **Database**: SQLite (default), MySQL/PostgreSQL supported
- **Frontend**: Blade, Tailwind CSS 4, Alpine.js
- **PDF**: DomPDF with Arabic support
- **Desktop**: PHP Desktop Chromium wrapper

---

## 💡 Development Philosophy

### Code Quality Standards
- **Clean Code**: Readable, self-documenting code
- **Type Safety**: Use type hints for all parameters and returns
- **Validation**: Always validate input using FormRequests
- **Error Handling**: Proper exception handling with meaningful messages
- **Security**: CSRF protection, input sanitization, session validation

### Best Practices
1. **Follow Laravel conventions** (naming, structure)
2. **Keep controllers thin** (delegate to services)
3. **Use repositories** for database operations
4. **Write expressive tests** for critical features
5. **Document complex logic** with clear comments
6. **Use meaningful variable names** (no abbreviations)

---

## 🛠️ Development Tools

### Required Software
- PHP 8.2+ with SQLite extension
- Composer (dependency management)
- Node.js & NPM (frontend assets)
- Git (version control)

### Recommended IDE Extensions
- **PHP Intelephense** - PHP intelligence
- **Laravel Blade Snippets** - Blade syntax
- **Tailwind CSS IntelliSense** - CSS utilities
- **SQLite Viewer** - Database inspection

---

## 📞 Support & Contribution

### Getting Help
- Check **[20-TROUBLESHOOTING.md](./20-TROUBLESHOOTING.md)** for common issues
- Review existing documentation for similar features
- Examine test files for usage examples

### Contributing Guidelines
1. Read relevant documentation first
2. Follow existing code patterns
3. Write/update tests for your changes
4. Update documentation if needed
5. Use meaningful commit messages

---

## 📝 Documentation Maintenance

This documentation should be updated when:
- New features are added
- Architecture patterns change
- New dependencies are introduced
- Breaking changes are made
- Security protocols are updated

**Last Updated**: November 2025  
**Laravel Version**: 12.x  
**PHP Version**: 8.2+
