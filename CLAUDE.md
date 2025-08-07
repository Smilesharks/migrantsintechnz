# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

**Frontend Development:**
- `npm run dev` - Start Vite development server with hot reload
- `npm run build` - Build production assets with Vite
- `npm run production` - Alias for production build

**Backend Development:**
- `php artisan serve` - Start Laravel development server
- `php please` - Statamic CLI tool for CMS operations
- `composer dev` - Runs concurrent development servers (Laravel, queue, pail logs, npm dev)

**Testing & Quality:**
- `composer test` - Run PHPUnit tests (clears config first)
- `php artisan test` - Alternative test command

**Content & Cache Management:**
- `php artisan statamic:stache:warm` - Warm Statamic cache
- `php artisan statamic:static:clear` - Clear static cache
- `php artisan statamic:static:warm --queue` - Warm static cache via queue
- `php artisan statamic:search:update --all` - Update search indexes

## Architecture Overview

This is a **Statamic CMS** application built on **Laravel Framework** with a focus on content management for "Migrants in Tech NZ" community.

### Tech Stack
- **CMS**: Statamic 5.x (flat-file CMS)
- **Framework**: Laravel 12.x
- **Frontend**: Alpine.js + Tailwind CSS 4.x
- **Build Tool**: Vite 6.x
- **Deployment**: Static site generation (SSG) for Netlify

### Key Architecture Patterns

**Content Structure:**
- `content/collections/` - Main content (migrants, events, pages)
- `content/globals/` - Site-wide settings (SEO, social media, browser appearance)
- `content/navigation/` - Navigation structures
- `resources/blueprints/` - Content field definitions
- `resources/fieldsets/` - Reusable field groups

**Template Architecture:**
- Uses **Antlers** templating engine (Statamic's template engine)
- `resources/views/` - All templates with `.antlers.html` extension
- `resources/views/page_builder/` - Page builder components
- `resources/views/components/` - Reusable components
- Component-based page building using fieldsets

**Frontend Build Process:**
- Vite compiles `resources/css/site.css` and `resources/js/site.js`
- Tailwind CSS for styling
- Alpine.js for interactivity
- Manual chunks disabled in Vite config for simpler builds

**Static Site Generation:**
- Configured for Netlify deployment
- `php please ssg:generate` generates static HTML
- Output to `storage/app/static/` directory
- Full static caching enabled in production

### Custom Peak Addons
The site uses Studio1902's Peak addon suite:
- `statamic-peak-browser-appearance` - Theme switching functionality
- `statamic-peak-seo` - SEO management
- `statamic-peak-tools` - Development utilities

### Content Collections
1. **Migrants** (`content/collections/migrants/`) - Community member profiles
2. **Events** (`content/collections/events/`) - Community events and meetups
3. **Pages** (`content/collections/pages/`) - Static pages

### Development Workflow
- Content is managed through Statamic CP (Control Panel)
- Git integration enabled for content versioning
- Hot reloading for both PHP and frontend assets
- Queue system for background processing