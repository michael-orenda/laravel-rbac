<?php

namespace MichaelOrenda\Rbac;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class RbacServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/rbac.php', 'rbac');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/rbac.php' => config_path('rbac.php'),
            __DIR__.'/../config/rbac_roles.php'           => config_path('rbac_roles.php'),
            __DIR__.'/../config/rbac_permissions.php'      => config_path('rbac_permissions.php'),
            __DIR__.'/../config/rbac_role_permissions.php' => config_path('rbac_role_permissions.php'),            
        ], 'rbac-config');

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

                // Register middleware aliases
        $router = $this->app['router'];  // FIX: Define $router here

        $router->aliasMiddleware('role', \MichaelOrenda\Rbac\Http\Middleware\RoleMiddleware::class);
        $router->aliasMiddleware('permission', \MichaelOrenda\Rbac\Http\Middleware\PermissionMiddleware::class);
  
        // Auto-run Config-Driven RBAC Seeder if enabled
        $this->autoRunConfigSeeds();
    }

    protected function autoRunConfigSeeds()
    {
        if (!config('rbac.auto_seed', false)) return;

        $this->app->booted(function () {
            if (Schema::hasTable('roles') && Schema::hasTable('permissions')) {
                Artisan::call('db:seed', [
                    '--class' => \MichaelOrenda\Rbac\Database\Seeders\ConfigDrivenRbacSeeder::class,
                    '--force' => true
                ]);
            }
        });
    }
}
