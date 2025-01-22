## About Laravel-Filament-Saas-Starter-Kit

This a Starter Kit for building your next Saas-application. It saves you many hours for setting up the basics for your Saas:

1) Access control for your Saas-frontend
2) An Admin-panel for your saas-clients/tenants to manage their users and settings
3) A SuperAdmin-panel for you to manage your tenants and users 

For this Starter Kit, Laravel Breeze (version 11, with LiveWire) is expanded with 'Tenants' (database and model) and Filament for the Admin-panels.
Users can be part of 1 or more Tenants. In the base setup, it assumes a single database for all tenants, with a 'tenant-column' for some tables to separate the data per tenant.

After cloning and installing this Starter Kit, you can start directly with implementing your own business logic, without spending time on setting up the basic Saas-structure.

An example of the application can be found at [https://starterkit.jovisst.nl](https://starterkit.jovisst.nl). 

You can login as a SuperAdmin by starterkit@jovisst.nl with password 'password'. You can create your own Tenants and Users from there.

The laravel-filament-saas-starter-kit includes the following features:
- A Tenant can have multiple users
- A user can have access to one or multiple Tenants
- AuthTenantMiddleware for securing application access for Tenant-users only
- User-registration for the new users and new tenants (public or invite-only)
- A user profile with basic information, including a profile picture (with Spatie Media Library)
- A (tenant)Admin for your tenants (admin-users) to manage their users, settings etc
- A superAdmin for you to manage your Tenants and Users

## Used components
The Laravel-Filament-Saas-Starter-Kit is based upon Laravel Breeze with the following components (see also the composer.json for a complete list):
- Laravel 11
- Breeze (Volt Class API with AlpineJS)
- Filament 3.0
- [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary/v11/introduction) (used for e.g. profile picture)
- Sentry-setup for error tracking
- Mailgun-setup for sending emails
- Laravel-debug bar for debugging
- see composer.json for a complete list

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
- Add your own remote repository and push it to your own git remote
- Optionally, remove the starter kit remote (or leave it, to fetch updates)
- Start building your own Saas-application

## Contributing
You can contribute to this project by forking the repository and making a pull request.

## Code of Conduct

## Security Vulnerabilities

If you discover a security vulnerability please send an e-mail to Lambert via [lambert@jovisst.nl](maillto:lambert@jovisst.nl). All security vulnerabilities will be promptly addressed.

## License
Laravel-Filament-Saas-Starter-Kit code is, in line with Laravel, released under the MIT license:

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

