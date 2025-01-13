## About Laravel-Filament-Saas-Starter-Kit

This a starter kit for your next Saas-application. It saves you many hours of setting up the basics for your Saas:

1) Access control for your Saas-frontend
2) An Admin-panel for your saas-clients/tenants to manage their users and settings
3) A SuperAdmin-panel for you to manage your clients/tenants and users 

It is based upon the Laravel 11 Breeze and LiveWire starter kit, Filament 3.0 for the admin-panels and other common libraries like Spatie Media Library, Sentry, Mailgun, Laravel-debugbar and more.

The Laravel-Filament-Saas-Setup is a pre-configured setup for starting your own Saas-application.
It handles access-restriction based on the Tenant, and the admin for both the tenant and you as a SuperAdmin. 
You can start directly with implementing your business logic without spending time on setting up the basic Saas-structure.

The Laravel-Filament-Saas-Setup contains database-setup only. The Premium-version is a private repository with full setup for starting your project.

You can contact Lambert [lambert@jovisst.nl](maillto:lambert@jovisst.nl) for access to the Premium-version.

The setup includes the following features:
- A Tenant/tenant can have multiple users
- A user can have access to multiple Tenants
- SecureTenant-middleware for tenant-access restriction
- A user has a profile with basic information, including a profile picture
- A tenantAdmin for tenants to manage their users etc
- A superAdmin for you to manage your tenants and users

## Used components
The Laravel-Filament-Saas-Setup is based upon Laravel Breeze wihh the following components:
- Laravel 11
- Breeze (Volt Class API with AlpineJS)
- Filament 3.0 using the Tenant-structure for the tenantAdmin and superAdmin
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary/v11/introduction) (e.g. profile picture)
- Sentry-setup for error tracking
- Mailgun-setup for sending emails
- Laravel-debugbar for debugging
- more to come

## Installation of development environment
- Clone the repository
- Run `composer install`
- Run `npm install`
- Run `cp .env.example .env` and fill in your application details 
- Run `npm run dev`
- Run `php artisan migrate`
- Run `php artisan serve`
- Make your first (superAdmin) user by running `php artisan app:make-user your@mail yourName yourPassword yourTenantName --tenantAdmin --superAdmin`
- Open your browser and go to `http://127.0.0.1:8000`

## Contributing

## Code of Conduct

## Security Vulnerabilities

If you discover a security vulnerability please send an e-mail to Lambert via [lambert@jovisst.nl](maillto:lambert@jovisst.nl). All security vulnerabilities will be promptly addressed.

## License


