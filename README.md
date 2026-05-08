<h1 align="center">ShopNow</h1>

<p align="center">
  A modern, open-source ecommerce platform built with Laravel, Vue 3, Inertia.js, and Tailwind CSS.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-13.x-red?logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.4-blue?logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/Vue-3.x-green?logo=vue.js" alt="Vue">
  <img src="https://img.shields.io/badge/Tailwind-3.x-38bdf8?logo=tailwindcss" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/License-MIT-yellow" alt="License">
</p>

---

## Features

- **Product Catalog** — Categories, featured products, gallery images, sale pricing
- **Shopping Cart** — Session-based cart with shipping calculation and voucher support
- **Checkout** — Full delivery details form with payment method selection
- **Order Management** — Order confirmation, order history, and admin order tracking
- **Customer Auth** — Registration, login, password reset
- **Admin Panel** — Product, order, user, blog, and settings management
- **Blog** — Posts with tags, archive filtering, author profiles, and rich text content
- **Static Pages** — About, Privacy Policy, Terms of Service, Refund Policy via CMS
- **Contact Form** — With configurable contact info from settings
- **Role & Permission** — ACL system via Spatie
- **Media Library** — Image uploads via Spatie Media Library
- **SEO Ready** — Meta titles, descriptions, canonical URLs, structured data
- **Modular Architecture** — Feature modules for clean separation of concerns

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13, PHP 8.4 |
| Frontend | Vue 3, Inertia.js v2, Tailwind CSS v3 |
| Bundler | Vite |
| Testing | Pest v4, PHPUnit |
| Database | MySQL / SQLite |
| Media | Spatie Laravel Media Library |
| Permissions | Spatie Laravel Permission |
| Code Style | Laravel Pint, ESLint, Prettier |

---

## Project Structure

```
modules/
├── Acl/            # Roles & permissions
├── AdminAuth/      # Admin authentication
├── Blog/           # Blog posts, tags, authors
├── Cart/           # Shopping cart & checkout
├── ContactMessage/ # Contact form
├── Customer/       # Customer profiles
├── CustomerAuth/   # Customer authentication
├── Dashboard/      # Admin dashboard
├── Index/          # Homepage & static pages
├── Order/          # Orders & confirmation
├── Page/           # CMS pages
├── Product/        # Products & categories
├── Settings/       # Site-wide settings
├── Support/        # Support utilities
└── User/           # Admin user management

resources-site/     # Customer-facing frontend (Blade + Vue)
resources/          # Admin frontend (Inertia/Vue)
```

---

## Getting Started

### Requirements

- PHP 8.4+
- Node.js 20+
- Composer
- MySQL or SQLite

### Installation

```bash
# Clone the repository
git clone https://github.com/your-username/shopnow.git
cd shopnow

# Install PHP dependencies
composer install

# Install Node dependencies
npm install

# Set up environment
cp .env.example .env
php artisan key:generate

# Configure your database in .env, then run migrations
php artisan migrate --seed

# Build frontend assets
npm run build

# Start the development server
php artisan serve
```

### Development

```bash
# Run dev server with hot reload
npm run dev

# Run all processes (server + queue + logs)
composer run dev

# Run tests
php artisan test --compact

# Format PHP code
vendor/bin/pint
```

---

## Configuration

Key settings are managed through the **Admin → Settings** panel and stored in the database:

- **Branding** — Site name, logo, favicon
- **Contact** — Address, phone, email
- **Shipping** — Flat rate, free shipping threshold
- **Payment** — Cash on delivery, online payment methods
- **SEO** — Default meta tags

---

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes (`git commit -m 'Add my feature'`)
4. Push to the branch (`git push origin feature/my-feature`)
5. Open a Pull Request

Please ensure your code follows the existing conventions and passes all tests.

---

## Security

If you discover a security vulnerability, please open a private security advisory on GitHub rather than a public issue.

---

## License

ShopNow is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
