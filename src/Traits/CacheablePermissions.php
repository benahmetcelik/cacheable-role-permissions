<?php

namespace Cacheable\CacheablePermissions\Traits;

use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

trait CacheablePermissions
{
    protected static $permissionCache = [];

    /**
     * Boot the trait
     */
    public static function bootCacheablePermissions()
    {
        // Model events'lerini dinle
        static::saved(function ($model) {
            if (config('cacheable-permissions.auto_clear_cache', true)) {
                $model->clearPermissionCache();
            }
        });

        static::deleted(function ($model) {
            if (config('cacheable-permissions.auto_clear_cache', true)) {
                $model->clearPermissionCache();
            }
        });
    }

    public function checkPermissionTo($permission, $guardName = null): bool
    {
        $cacheKey = $this->buildPermissionCacheKey($permission, $guardName, 'check');

        return $this->getCachedPermissionResult($cacheKey, function () use ($permission, $guardName) {
            try {
                return $this->hasPermissionTo($permission, $guardName);
            } catch (PermissionDoesNotExist $e) {
                return false;
            }
        });
    }

    protected function getCachedPermissionResult(string $cacheKey, callable $callback)
    {
        if (isset(static::$permissionCache[$cacheKey])) {
            return static::$permissionCache[$cacheKey];
        }

        $ttl = now()->addMinutes(config('cacheable-permissions.permission_cache_ttl', 15));
        $result = Cache::remember($cacheKey, $ttl, $callback);
        static::$permissionCache[$cacheKey] = $result;

        return $result;
    }

    protected function buildPermissionCacheKey($permission, $guardName, $method): string
    {
        $permissionKey = is_object($permission) ? $permission->getKey() : $permission;

        return sprintf(
            'user_perm_%s:%s:%s:%s',
            $method,
            $this->getKey(),
            $permissionKey,
            $guardName ?? 'default'
        );
    }

    public function clearPermissionCache(): void
    {
        $pattern = "user_perm_*:{$this->getKey()}:*";

        // Memory cache temizle
        static::$permissionCache = array_filter(
            static::$permissionCache,
            fn($key) => !str_contains($key, ":{$this->getKey()}:"),
            ARRAY_FILTER_USE_KEY
        );

        // Cache driver'a göre temizleme
        $driver = config('cacheable-permissions.cache_driver', 'redis');

        if ($driver === 'redis' && config('cache.default') === 'redis') {
            $keys = Cache::getRedis()->keys($pattern);
            if ($keys) {
                Cache::getRedis()->del($keys);
            }
        }
    }

    /**
     * Permission atandığında cache temizle
     */
    public function givePermissionTo(...$permissions)
    {
        $result = parent::givePermissionTo(...$permissions);
        $this->clearPermissionCache();
        return $result;
    }

    /**
     * Permission kaldırıldığında cache temizle
     */
    public function revokePermissionTo($permission)
    {
        $result = parent::revokePermissionTo($permission);
        $this->clearPermissionCache();
        return $result;
    }
}