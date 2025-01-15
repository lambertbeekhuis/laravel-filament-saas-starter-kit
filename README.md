## About Laravel-Filament-Saas-Starter-Kit

This a starter kit for your next Saas-application. It saves you many hours for setting up the basics for your Saas:

1) Access control for your Saas-frontend
2) An Admin-panel for your saas-clients/tenants to manage their users and settings
3) A SuperAdmin-panel for you to manage your clients/tenants and users 

It is based upon Laravel 11 Breeze with LiveWire starter kit, Filament 3.0 for the admin-panels and other common libraries like Spatie Media Library, Sentry, Mailgun, Laravel-debugbar and more.

The Laravel Breeze starter kit is expanded with 'Tenants', and User can be part of 1 or more Tenants.
In the base setup, the setup is based on a single database: all data is stored in the same database.

After installing this starter kit, you can start directly with implementing your business logic without spending time on setting up the basic Saas-structure.

An example of the application can be found at [https://starterkit.jovisst.nl](https://starterkit.jovisst.nl). 

You can login as a SuperAdmin by starterkit@jovisst.nl with password 'starterkit'. You can create your own tenant and users from there.

The laravel-filament-saas-starter-kit includes the following features:
- A Tenant/tenant can have multiple users
- A user can have access to one or multiple Tenants
- AuthTenantMiddleware for securing application access
- Registration for the new users and new tenants
- A user profile with basic information, including a profile picture
- A (tenant)Admin for tenants to manage their users etc
- A superAdmin for you to manage your tenants and users

## Used components
The Laravel-Filament-Saas-Setup is based upon Laravel Breeze with the following components (see also the composer.json for a complete list):
- Laravel 11
- Breeze (Volt Class API with AlpineJS)
- Filament 3.0
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary/v11/introduction) (used for e.g. profile picture)
- Sentry-setup for error tracking
- Mailgun-setup for sending emails
- Laravel-debug bar for debugging


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
- From here on you can start implementing your business logic


## Contributing

## Code of Conduct

## Security Vulnerabilities

If you discover a security vulnerability please send an e-mail to Lambert via [lambert@jovisst.nl](maillto:lambert@jovisst.nl). All security vulnerabilities will be promptly addressed.

## License


