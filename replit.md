# Yomabar - Sport Booking Platform

## Overview
Yomabar is a sports venue booking platform built with Laravel 12, Livewire 4, and Tailwind CSS 4. It allows users to search and book sports courts (badminton, futsal, etc.) at various venues in Jakarta. Features include online payments via Midtrans, booking management, admin dashboard, and wallet system.

## Recent Changes
- **2026-02-12**: Initial Replit setup - configured PostgreSQL, Vite, trust proxies, workflow

## Project Architecture
- **Framework**: Laravel 12 (PHP 8.2)
- **Frontend**: Livewire 4 + Tailwind CSS 4 (via Vite)
- **Database**: PostgreSQL (Replit built-in)
- **Package Manager**: Composer (PHP), npm (JS)
- **Build Tool**: Vite 6

### Directory Structure
- `app/` - Application code (Models, Livewire components, Services, etc.)
- `resources/views/` - Blade templates and Livewire views
- `database/migrations/` - Database migrations (36 total)
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
