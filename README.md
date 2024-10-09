## About Laravel-Filament-Saas-Setup (Premium)

The Laravel-Filament-Saas-Setup is a pre-configured setup for starting your Saas-application. You can start directly with implementing your business logic and don't have to worry about the Saas-setup.
This setup saves you many hours of work and is a great starting point for your next Saas-project.

The Laravel-Filament-Saas-Setup contains database-setup only. The Premium-version is a private repository the full scope setup for starting your project.

You can contact Lambert Beekhuis via [lambert@jovisst.nl](maillto:lambert@jovisst.nl) for access to the Premium-version.

The setup includes the following features:
- A Tenant/Client can have multiple users
- A user's can have access to multiple Tenants/Clients
- SecureTenant middleware for client-access restriction
- A user has a profile with basic information, including a profile picture
- A clientAdmin for clients to manage their users etc
- A superAdmin for you to manage your clients and users

## Used components
The Laravel-Filament-Saas-Setup is based upon the following components:
- Laravel 11
- Breeze (Volt Class API with AlpineJS)
- Filament 3.0 using the Tenant-structure for the clientAdmin and superAdmin
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary/v11/introduction) (e.g. profile picture)
- Sentry-setup for error tracking
- Mailgun-setup for sending emails
- Laravel-debugbar for debugging

## Installation
- Clone the repository
- Run `composer install`
- Run `npm install`
- Run `npm run dev`
- Run `php artisan migrate`
- Run `php artisan serve`
- Make your first (superAdmin) user by running `php artisan app:make-user your@mail yourName yourPassword yourClientName --clientAdmin --superAdmin`
- Open your browser and go to `http://127.0.0.1:8000`

## Contributing

## Code of Conduct

## Security Vulnerabilities

If you discover a security vulnerability please send an e-mail to Lambert Beekhuis via [lambert@jovisst.nl](maillto:lambert@jovisst.nl). All security vulnerabilities will be promptly addressed.

## License


