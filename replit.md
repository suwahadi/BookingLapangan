# Yomabar - Sport Booking Platform

## Overview
Yomabar is a sports venue booking platform built with Laravel 12, Livewire 4, and Tailwind CSS 4. It allows users to search and book sports courts (badminton, futsal, etc.) at various venues in Jakarta. Features include online payments via Midtrans, booking management, admin dashboard, and wallet system.

## Recent Changes
- **2026-02-12**: Integrated voucher system into checkout/review-order page - replaced dummy validation with real Voucher model lookup, VoucherCalculator for discount, session-based storage, VoucherRedemptionService applied after booking creation, available vouchers list in modal, discount shown in cost breakdown
- **2026-02-12**: Added voucher/discount system - migrations, models, enums, services (VoucherCalculator, VoucherEligibilityService, VoucherRedemptionService), Livewire BookingVoucherBox component, integration with PaymentService/MidtransNotificationService/BookingExpiryService/CancelBookingService, VoucherSeeder, and tests
- **2026-02-12**: Initial Replit setup - configured PostgreSQL, Vite, trust proxies, workflow

### Sample Voucher Codes
- DISKON10 - 10% off (max Rp 50,000, min order Rp 100,000)
- HEMAT25K - Rp 25,000 off (min order Rp 75,000)
- GRATIS50K - Rp 50,000 off (min order Rp 200,000)
- VENUE20 - 20% off for Elite Kuningan Arena venue

## Project Architecture
- **Framework**: Laravel 12 (PHP 8.2)
- **Frontend**: Livewire 4 + Tailwind CSS 4 (via Vite)
- **Database**: PostgreSQL (Replit built-in)
- **Package Manager**: Composer (PHP), npm (JS)
- **Build Tool**: Vite 6

### Directory Structure
- `app/` - Application code (Models, Livewire components, Services, etc.)
- `resources/views/` - Blade templates and Livewire views
- `database/migrations/` - Database migrations (39 total, including voucher system)
- `database/seeders/` - Database seeders with sample venue data
- `routes/web.php` - Web routes
- `config/` - Laravel configuration files
- `public/` - Publicly accessible assets

### Key Dependencies
- `livewire/livewire` - Reactive UI components
- `spatie/laravel-permission` - Role-based access control
- `@tailwindcss/vite` - Tailwind CSS Vite plugin

### Database
- Uses Replit's built-in PostgreSQL
- Connection via environment variables (DB_CONNECTION=pgsql)
- Session, cache, and queue all use database driver

### Development Workflow
- Single workflow runs PHP artisan server (port 5000) + Vite dev server concurrently
- Vite HMR configured for Replit domain
- Trust proxies configured for Replit's reverse proxy

### Admin Access
- Super Admin: admin@booking.com / admin123
- Finance Admin: finance@booking.com / admin123
- Operator: operator@booking.com / admin123
- Back Office: office@booking.com / admin123

### Regular User Access
- user1@gmail.com through user10@gmail.com / password

## User Preferences
- (none recorded yet)
