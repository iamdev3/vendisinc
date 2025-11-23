# Vendisync - Technical Documentation

## Project Overview

Vendisync is a Laravel 12-based e-commerce management system with a Filament 4 admin panel. The application provides comprehensive order management, product catalog, retailer database, and financial tracking capabilities.

## Technology Stack

### Backend

-   **Laravel 12** - Core PHP framework
-   **PHP 8.2+** - Language version
-   **Filament 4** - Admin panel and CRUD interface
-   **MySQL** - Primary database
-   **Redis** - Cache and queue management

### Frontend

-   **Tailwind CSS 4** - Styling framework
-   **Vite 6** - Asset bundling
-   **Livewire** - Dynamic UI components (via Filament)

### Development & Testing

-   **Pest PHP** - Testing framework
-   **Composer** - PHP dependency management
-   **NPM** - Frontend dependency management

## Project Structure

```
app/
├── Enums/                 # PHP enums (OrderStatus)
├── Filament/              # Admin panel resources and components
│   ├── Pages/             # Custom admin pages
│   ├── Resources/         # CRUD resources for entities
│   └── Widgets/           # Dashboard widgets
├── Helpers/               # Utility functions
├── Http/                  # Controllers and middleware
├── Mail/                  # Email templates and mailables
├── Models/                # Eloquent models
└── Providers/             # Service providers
config/                    # Configuration files
database/                  # Migrations, seeders, factories
lang/                      # Localization files
public/                    # Public assets
resources/                 # Views, CSS, JavaScript
routes/                    # Route definitions
storage/                   # File storage
tests/                     # Test files
```

## Core Components

### Models

#### User

-   Standard Laravel authentication model
-   Uses Spatie Permissions for role management
-   Relationships with Orders

#### Brand

-   Product manufacturers or own brands
-   Relationships with Products and Orders

#### Category

-   Product categorization
-   Relationships with Products

#### Product

-   Inventory items with multilingual support
-   Uses Spatie Translatable for localization
-   Relationships with Brand, Category, and OrderItems

#### Retailor

-   Customer entities with contact information
-   Relationships with Orders

#### Order

-   Core transaction entity
-   Financial calculations and status tracking
-   Relationships with User, Brand, Retailor, and OrderItems

#### OrderItem

-   Line items in orders
-   Relationships with Order and Product

### Enums

#### OrderStatus

-   Defines order lifecycle statuses
-   Includes CONFIRMED, SHIPPED, DELIVERED, CANCELLED, REFUNDED, ON_HOLD

### Filament Resources

#### OrderResource

-   Comprehensive order management interface
-   Form with sections for order information, customer details, and order items
-   Advanced table with filtering, sorting, and financial columns
-   Automatic profit calculation in repeater fields

#### ProductResource

-   Multilingual product management
-   Sections for product information and management flags
-   Translatable fields for name, description, and additional info

#### RetailorResource

-   Customer database management
-   Sections for contact information and status management
-   Logo upload capability

#### BrandResource & CategoryResource

-   Simple CRUD interfaces for taxonomy management

## Key Features Implementation

### Multilingual Support

-   Uses `lara-zeus/spatie-translatable` package
-   JSON translation files in [lang/](file:///c%3A/Users/Dev/Herd/vendisync/lang) directory
-   Translatable fields in Product model
-   Language switcher in admin panel

### Profit Calculation

-   Automatic calculation in OrderResource form
-   Unit profit = sell_price - base_price
-   Total profit = unit_profit \* quantity
-   Overall order profit margin calculation

### Email System

-   Uses `filament-mail-templates` package
-   Markdown-based email templates
-   WelcomeEmail mailable example
-   Template and history management in admin

### Role-Based Access Control

-   Uses `filament-shield` package
-   Permission management through admin interface
-   Role assignment to users
-   Resource-level access control

## Configuration Files

### Key Configuration Files

-   `config/filament.php` - Filament admin settings
-   `config/filament-mail-templates.php` - Email template settings
-   `config/permission.php` - Role and permission settings
-   `config/services.php` - External service configurations

### Environment Configuration

-   Database connection settings
-   Cache and queue drivers
-   Mail configuration
-   Application URL and timezone

## Database Schema

### Migration Structure

-   Users and authentication tables
-   Spatie permission tables
-   Brand, Category, Product tables
-   Retailor table with contact information
-   Orders table with financial fields
-   OrderItems table for line items
-   Mail templates and history tables

### Key Relationships

-   Foreign key constraints for data integrity
-   Indexes on frequently queried columns
-   JSON fields for flexible data storage

## Frontend Architecture

### Asset Pipeline

-   Vite configuration in `vite.config.js`
-   Tailwind CSS for styling
-   JavaScript modules for dynamic functionality
-   Asset compilation commands via NPM scripts

### Views

-   Blade templates for email and testing
-   Filament automatically generates admin views
-   Localization support in views

## API and Routes

### Web Routes

-   Admin authentication redirect
-   Test translation route
-   Filament automatically generates CRUD routes

### Console Commands

-   Standard Laravel commands
-   Custom commands for application maintenance

## Development Workflow

### Local Development

-   `composer dev` command for concurrent server, queue, and Vite
-   Database migrations and seeding
-   Environment configuration via `.env` file

### Testing

-   Pest PHP for unit and feature tests
-   Test database configuration
-   Factory-based test data generation

### Deployment

-   Standard Laravel deployment process
-   Asset compilation for production
-   Database migration execution
-   Cache clearing procedures

## Package Dependencies

### Core Packages

-   `filament/filament` - Admin panel
-   `lara-zeus/spatie-translatable` - Multilingual support
-   `bezhansalleh/filament-shield` - Permissions
-   `bezhansalleh/filament-language-switch` - Language switching

### Development Packages

-   `pestphp/pest` - Testing framework
-   `fakerphp/faker` - Test data generation

## Customizations

### Helper Functions

-   FormFieldBuilder.php for form component utilities

### Service Providers

-   Custom Filament service provider configuration
-   AppServiceProvider for application bootstrapping

## Extending the Application

### Adding New Entities

1. Create model with required relationships
2. Generate migration with proper foreign keys
3. Create Filament Resource with form and table
4. Add localization support if needed
5. Implement proper validation rules

### Customizing Existing Features

1. Extend Filament Resources for UI changes
2. Modify models for business logic changes
3. Update migrations for schema changes
4. Adjust localization files for text changes

## Performance Considerations

### Database Optimization

-   Proper indexing on foreign keys and frequently queried columns
-   Eager loading of relationships to prevent N+1 queries
-   Use of database caching where appropriate

### Caching Strategy

-   Laravel cache configuration
-   Redis for session and cache storage
-   Query result caching for expensive operations

### Queue Management

-   Background job processing for email sending
-   Queue configuration in `config/queue.php`
-   Failed job handling and monitoring

This documentation provides a comprehensive overview of the Vendisync system architecture, enabling developers to understand, maintain, and extend the application effectively.
