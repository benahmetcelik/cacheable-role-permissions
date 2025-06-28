<?php

namespace Cacheable\CacheablePermissions;

use Illuminate\Support\ServiceProvider;
use Cacheable\CacheablePermissions\Observers\UserCacheObserver;

class CacheablePermissionsServiceProvider extends ServiceProvider
{
    public function boot()
    {

        $this->publishes([
            __DIR__.'/../config/cacheable-permissions.php' => config_path('cacheable-permissions.php'),
        ], 'cacheable-permissions-config');


        $this->mergeConfigFrom(
            __DIR__.'/../config/cacheable-permissions.php',
            'cacheable-permissions'
        );


        $this->registerObservers();
    }

    public function register()
    {
        //
    }

    /**
     * Observer'ları kaydet
     */
    protected function registerObservers(): void
    {
        $userModel = config('cacheable-permissions.user_model', config('auth.providers.users.model', 'App\\Models\\User'));

        if (class_exists($userModel)) {
            $userModel::observe(UserCacheObserver::class);
        }

        $this->observePermissionModels();
    }

    /**
     * Spatie Permission model'larını observe et
     */
    protected function observePermissionModels(): void
    {
        if (class_exists(\Spatie\Permission\Models\Permission::class)) {
            \Spatie\Permission\Models\Permission::observe(new class {
                public function saved($permission): void
                {
                    $this->clearRelatedUserCaches();
                }

                public function deleted($permission): void
                {
                    $this->clearRelatedUserCaches();
                }

                protected function clearRelatedUserCaches(): void
                {
                    if (config('cacheable-permissions.clear_all_on_permission_change', false)) {
                        \Illuminate\Support\Facades\Cache::flush();
                    }
                }
            });
        }

        if (class_exists(\Spatie\Permission\Models\Role::class)) {
            \Spatie\Permission\Models\Role::observe(new class {
                public function saved($role): void
                {
                    $this->clearRelatedUserCaches();
                }

                public function deleted($role): void
                {
                    $this->clearRelatedUserCaches();
                }

                protected function clearRelatedUserCaches(): void
                {
                    if (config('cacheable-permissions.clear_all_on_role_change', false)) {
                        \Illuminate\Support\Facades\Cache::flush();
                    }
                }
            });
        }
    }
}