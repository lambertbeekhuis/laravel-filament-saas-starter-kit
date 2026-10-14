## About Laravel-Filament-Saas-Starter-Kit (Laravel 13)

You can build your next SaaS application using this starter kit. It saves many hours setting up the basics:

1. Access control for your SaaS application, including optional roles and permissions (public and authenticated)
2. An admin panel for your SaaS tenants to manage users and settings
3. A superadmin panel to manage tenants and users
4. Registration logic with options for new users

This starter kit is based on **Laravel 13**, **Livewire / Volt** (Breeze-style auth UI), and **Filament 5** for admin panels. Users can belong to one or more tenants. The default setup uses a single database with a tenant column on relevant tables.

After cloning and installing, you can focus on business logic instead of SaaS scaffolding.

Demo (Laravel 11 version): [https://starterkit.jovisst.nl](https://starterkit.jovisst.nl)

### Features

- Tenants with many users; users can access multiple tenants
- `AuthTenantMiddleware` for tenant-scoped authenticated routes
- Optional Spatie Permission (team = tenant) — toggle with `FEATURE_PERMISSIONS_ENABLED`
- User registration for new users and tenants (public or invite-only)
- Profile with Spatie Media Library (profile picture)
- Tenant admin at `/admin/{tenant}`
- Superadmin at `/superadmin`

### Stack

- Laravel 13
- Livewire 4 + Volt
- Filament 5
- Spatie Media Library (profile pictures)
- Spatie Permission (optional, tenant-scoped)
- Sentry (error tracking)
- Mailgun (mail)
- Laravel Debugbar (local debugging)

See `composer.json` for the full dependency list.

### Installation

```sh
composer install
npm install
cp .env.example .env   # fill in app, mail, Sentry, etc.
npm run dev              # or npm run build
php artisan migrate --seed
php artisan serve
php artisan app:make-user your@mail yourName yourPassword yourTenantName --tenantAdmin --superAdmin
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

### Optional roles & permissions

Spatie Permission is **on by default**. Tenant-scoped roles use `tenant_id` as the permission “team”, and the tenant Filament panel requires the `admin` permission (see `AdminRolesAndPermissionsSeeder`).

If you do not need fine-grained roles, or you want to avoid the extra queries Spatie runs on authenticated requests, turn permissions **off** at project level:

```env
FEATURE_PERMISSIONS_ENABLED=false
```

After changing this value, clear config cache in deployed environments:

```sh
php artisan config:clear
```

#### When permissions are off

| Area | Behavior |
|------|----------|
| **Web routes** | `role`, `permission`, and `role_or_permission` middleware aliases are not registered. The `/testPermissions` demo route is not registered. |
| **Tenant routes** | `AuthTenantMiddleware` still enforces tenant membership; it does **not** call `setPermissionsTeamId()`. |
| **Filament tenant admin** | Any authenticated user who can access the panel for that tenant may use `/admin/{tenant}` (no `admin` permission check). |
| **Filament superadmin** | Roles and Permissions resources are hidden from navigation (direct URLs should still be avoided in production if you rely on “off”). |
| **User forms** | The roles multi-select is hidden in tenant user management. |
| **Seeding** | `AdminRolesAndPermissionsSeeder` is skipped when you run `php artisan db:seed`. |
| **`app:make-user`** | The `--tenantAdmin` flag does not assign the `admin` role (user and tenant are still created). |
| **Database** | Permission migrations still run; tables stay in place so you can enable the feature later without new migrations. |

Helper in code: `App\Support\Features::permissionsEnabled()` (reads `config/features.php`, backed by `FEATURE_PERMISSIONS_ENABLED`).

#### Turning permissions back on

1. Set `FEATURE_PERMISSIONS_ENABLED=true` in `.env` and run `php artisan config:clear`.
2. Seed roles and permissions: `php artisan db:seed --class=AdminRolesAndPermissionsSeeder`.
3. Assign the `admin` role per tenant for users who should manage Filament tenant admin, e.g. via `php artisan app:make-user ... --tenantAdmin` (with permissions enabled) or manually in the superadmin Roles UI.

Use **permissions on** when you need tenant-specific roles (editor, billing, etc.) or must restrict who can open the tenant admin panel. Use **permissions off** for simpler SaaS setups where tenant membership alone is enough authorization.

### License

MIT — see the original starter kit license text in the Laravel 11 repository.
