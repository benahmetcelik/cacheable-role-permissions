<?php

namespace Cacheable\CacheablePermissions;

use Illuminate\Support\ServiceProvider;
use Cacheable\CacheablePermissions\Observers\UserCacheObserver;

class CacheablePermissionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Config dosyasını yayınla
        $this->publishes([
            __DIR__.'/../config/cacheable-permissions.php' => config_path('cacheable-permissions.php'),
        ], 'cacheable-permissions-config');

        // Config dosyasını merge et
        $this->mergeConfigFrom(
            __DIR__.'/../config/cacheable-permissions.php',
            'cacheable-permissions'
        );

        // Observer'ı kaydet
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
        // Config'den User model class'ını al
        $userModel = config('cacheable-permissions.user_model', config('auth.providers.users.model', 'App\\Models\\User'));

        if (class_exists($userModel)) {
            $userModel::observe(UserCacheObserver::class);
        }

        // Spatie Permission model'larını da observe et
        $this->observePermissionModels();
    }

    /**
     * Spatie Permission model'larını observe et
     */
    protected function observePermissionModels(): void
    {
        // Permission model değişikliklerinde ilgili user cache'lerini temizle
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
                    // Tüm permission cache'lerini temizle (opsiyonel)
                    if (config('cacheable-permissions.clear_all_on_permission_change', false)) {
                        \Illuminate\Support\Facades\Cache::flush();
                    }
                }
            });
        }

        // Role model değişikliklerinde ilgili user cache'lerini temizle
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
                    // Tüm role cache'lerini temizle (opsiyonel)
                    if (config('cacheable-permissions.clear_all_on_role_change', false)) {
                        \Illuminate\Support\Facades\Cache::flush();
                    }
                }
            });
        }
    }
}