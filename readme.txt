TODO:
- add extra fields to user with separate update-entity
- show profile information in frontend


- apply to specific Saas-registration (with or without approval)
- invite a new user for registration
- SaasAuth maken? Sla ik de Tenant op in de sessie? Ja! https://laracasts.com/discuss/channels/laravel/add-cutom-function-to-auth-facade
- Switchen naar een ander User maken

DONE
- Permission and roles with Spatie https://spatie.be/docs/laravel-permission/v6/introduction
    - add migration tenant foreign key
    - use seeds for 'admin' (both permission and role)
    - TenantAdmin via Roles/Permissions
- edit profile-photo in frontend (used the excellent tutorial on https://www.youtube.com/watch?v=kfkKUuvF2Lc&list=PLaDrsvip-wJvbi8t1zq3mG16Wk8DTwuYT&index=4)
- tenantAdmin: edit tenant itself
- tenant name sluggable
- show tenant-logo in the header
- Combine all users-stuff of Filament into a Base-class
- Admin en Superadmin links under 'Lambert'/Profile
- php artisan command for making a new User
- make a component met all users
- Superadmin: Add Users (basically to search!)
- Admin: access based on Roles
- tenantUser: is_admin => is_tenant_admin
- tenantUser: is_active => is_tenant_active
- Superadmin: tenant edit users: doe iets met wel/niet email sturen
- Add registration of new Tenant/tenant @superadmin
- Use the Laravel Breeze email-template for own emails https://laraveldaily.com/post/laravel-breeze-user-name-auth-email-templates
- SuperAdmin: add relation to users
- Login and select last Tenant/tenant
- Added debugbar
- Profile photo for user
- Access restrictions to Admin and SuperAdmin
- add Sentry
- Add media libary https://spatie.be/docs/laravel-medialibrary/v11/introduction
- admin and superadmin filament panel
- added filament
- basis Laravel + Breeze

DEVELOPMENT ENVIRONMENT
- npm run dev
- a serve (php artisan serve)

