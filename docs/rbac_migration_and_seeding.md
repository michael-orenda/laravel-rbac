
# Running RBAC Migrations and Seeders

## 1. Run Package Migrations

Run from your Laravel **application**:

```
php artisan migrate
```

This loads migrations from your RBAC package and creates:

- roles
- permissions
- model_has_roles
- model_has_permissions
- role_has_permissions

---

## 2. Config‑Driven RBAC Seeder (Recommended)

This uses:

- `rbac_roles.php`
- `rbac_permissions.php`
- `rbac_role_permissions.php`

### Run:

```
php artisan db:seed --class="MichaelOrenda\Rbac\Database\Seeders\ConfigDrivenRbacSeeder"
```

It will automatically:

- Create base roles  
- Build hierarchy  
- Create permissions  
- Group permissions  
- Assign permissions to roles  

---

### Optional: Auto‑Seeding

Enable in `config/rbac.php`:

```
'auto_seed' => true,
```

Then run:

```
php artisan migrate
```

The RBAC system seeds automatically after migrations.

---

## 3. Traditional Seeder System

Run:

```
php artisan db:seed --class="MichaelOrenda\Rbac\Database\Seeders\RbacSeeder"
```

This seeds:

- Base roles  
- Extended roles  
- Permissions  
- Role-permission relationships  

---

## 4. Verifying Seeders

Run:

```
php artisan tinker
```

### Check roles:

```
MichaelOrenda\Rbac\Models\Role::pluck('slug');
```

### Check permissions:

```
MichaelOrenda\Rbac\Models\Permission::count();
```

### Check admin permissions:

```
Role::where('slug','admin')->first()->permissions;
```

---

## 5. Common Issues

### Config Not Found  
Ensure `config/rbac.php` contains:

```
'auto_seed' => false,
```

### Migrations Not Loading  
Ensure your package provider has:

```
$this->loadMigrationsFrom(__DIR__.'/../database/migrations');
```

### Seeders Running Too Early  
Ensure they run under:

```
$app->booted(function () { ... });
```

### Missing Tables  
Run:

```
php artisan migrate:fresh
```

---

## You're Ready!

You now know how to run:

- Migrations  
- Traditional seeders  
- Config-driven RBAC seeders  
- Auto-seeding  
- RBAC verification  
