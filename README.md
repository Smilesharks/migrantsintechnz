# Migrants in Tech NZ

A community platform built with Statamic CMS to connect and support migrants working in New Zealand's technology sector.

## 🎯 About

Migrants in Tech NZ is a platform that brings together migrants working in the tech industry across New Zealand. Our community provides networking opportunities, career support, and a directory of talented professionals looking to make their mark in Aotearoa's thriving tech ecosystem.

### Features

-   **Community Directory**: Browse profiles of migrants in tech across NZ
-   **Event Management**: Discover and register for networking events and meetups
-   **Location-based Networking**: Connect with professionals in your region
-   **Professional Links**: Direct access to portfolios, LinkedIn, and GitHub profiles

## 🏗️ Tech Stack

-   **CMS**: Statamic 5.x (Flat-file CMS)
-   **Framework**: Laravel 12.x
-   **Frontend**: Alpine.js + Tailwind CSS 4.x
-   **Build Tool**: Vite 6.x
-   **Deployment**: Static Site Generation (SSG) for Netlify
-   **Forms**: Netlify Forms integration

## 🚀 Quick Start

### Prerequisites

-   PHP 8.2+
-   Composer
-   Node.js 18+
-   NPM

### Installation

1. **Clone and install dependencies**

    ```bash
    git clone [repository-url]
    cd migrantsintechnz
    composer install
    npm install
    ```

2. **Create admin user**

    ```bash
    php please make:user
    ```

3. **Start development servers**

    ```bash
    # Option 1: Individual servers
    php artisan serve
    npm run dev

    # Option 2: All-in-one (recommended)
    composer dev
    ```

4. **Access the site**
    - Frontend: `http://localhost:8000`
    - Control Panel: `http://localhost:8000/cp`

## 🛠️ Development Commands

### Frontend Development

-   `npm run dev` - Start Vite development server with hot reload
-   `npm run build` - Build production assets
-   `npm run production` - Alias for production build

### Backend Development

-   `php artisan serve` - Start Laravel development server
-   `php please` - Statamic CLI tool for CMS operations
-   `composer dev` - Run concurrent development servers (Laravel, queue, logs, npm)

### Content & Cache Management

-   `php artisan statamic:stache:warm` - Warm Statamic cache
-   `php artisan statamic:static:clear` - Clear static cache
-   `php artisan statamic:static:warm --queue` - Warm static cache via queue
-   `php artisan statamic:search:update --all` - Update search indexes

### Testing & Quality

-   `composer test` - Run PHPUnit tests
-   `php artisan test` - Alternative test command

## 📁 Project Structure

```
├── content/
│   ├── collections/
│   │   ├── events/          # Community events and meetups
│   │   ├── migrants/        # Community member profiles
│   │   └── pages/           # Static pages
│   ├── globals/             # Site-wide settings
│   └── navigation/          # Navigation structures
├── resources/
│   ├── blueprints/          # Content field definitions
│   ├── fieldsets/           # Reusable field groups
│   ├── views/
│   │   ├── components/      # Reusable components
│   │   ├── page_builder/    # Page builder blocks
│   │   └── layout/          # Layout templates
│   ├── css/
│   └── js/
└── public/                  # Static assets
```

## 🎨 Content Management

### Adding Community Members

1. Access Control Panel (`/cp`)
2. Navigate to **Collections > Migrants**
3. Create new entry with:
    - Full name and position
    - Email and professional links
    - Skills/expertise tags (max 2)
    - Location within NZ
    - Profile image (optional)

### Managing Events

1. Go to **Collections > Events**
2. Create event with:
    - Date and time
    - Location details
    - Event description
    - Ticket/registration URLs
    - Featured image

Events are automatically sorted chronologically and show appropriate "Get Tickets" buttons for future events.

### Page Building

The site uses a flexible page builder system:

-   **Section Title**: Hero sections with descriptions
-   **Netlify Form**: Community registration form
-   **Past Events**: Event listing component

## 🌐 Forms & Netlify Integration

The site includes a custom Netlify form for community registrations at `/join`:

-   **Honeypot protection**: Spam prevention
-   **Field validation**: Required fields and format checking
-   **Skills limiting**: Maximum 2 expertise selections
-   **Responsive design**: Works on all devices
-   **Dark mode support**: Consistent theming

Form submissions are processed by Netlify and can be configured to send email notifications.

## 📱 Responsive Design

-   **Mobile-first approach** with Tailwind CSS
-   **Dark/light theme switching** with Peak Browser Appearance
-   **Accessible components** following web standards
-   **Performance optimized** with static generation

## 🚢 Deployment

### Netlify (Recommended)

1. **Generate static site**

    ```bash
    php please ssg:generate
    ```

2. **Deploy generated files** from `storage/app/static/`

### Traditional Hosting (Ploi)

```bash
cd {SITE_DIRECTORY}
git pull origin {BRANCH}
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
npm ci && npm run build
php artisan cache:clear
php artisan config:cache
php artisan statamic:stache:warm
php artisan statamic:static:warm --queue
```

### Forge Deployment

Similar to Ploi but with Forge-specific variables and FPM reload handling.

## ⚙️ Environment Configuration

### Development

```env
APP_NAME="Migrants In Tech"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE="Pacific/Auckland"
```

### Production

```env
APP_NAME="Migrants In Tech"
APP_ENV=production
APP_DEBUG=false
STATAMIC_STATIC_CACHING_STRATEGY=full
STATAMIC_CACHE_TAGS_ENABLED=true
STATAMIC_GIT_ENABLED=true
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

### Content Guidelines

-   Profile images should be professional headshots
-   Event descriptions should be engaging and informative
-   All content should be inclusive and welcoming

## 📞 Support

For technical issues or community questions:

-   Create an issue in the repository
-   Contact the community administrators
-   Check Statamic documentation for CMS-related questions

---

**Built with ❤️ for the migrant tech community in Aotearoa New Zealand**
