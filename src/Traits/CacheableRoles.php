<?php

namespace Cacheable\CacheablePermissions\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheableRoles
{
    /**
     * Boot the trait
     */
    public static function bootCacheableRoles()
    {
        // Model events'lerini dinle
        static::saved(function ($model) {
            if (config('cacheable-permissions.auto_clear_cache', true)) {
                $model->clearRoleCache();
            }
        });

        static::deleted(function ($model) {
            if (config('cacheable-permissions.auto_clear_cache', true)) {
                $model->clearRoleCache();
            }
        });
    }

    public function hasRole($roles, ?string $guard = null): bool
    {
        $cacheKey = $this->getRoleCacheKey($roles, $guard);

        return Cache::remember($cacheKey, $this->getRoleCacheTtl(), function () use ($roles, $guard) {
            return $this->checkRolesUncached($roles, $guard);
        });
    }

    protected function checkRolesUncached($roles, ?string $guard = null): bool
    {
        $this->loadMissing('roles');

        if (is_string($roles) && str_contains($roles, '|')) {
            $roles = $this->convertPipeToArray($roles);
        }

        if (is_string($roles)) {
            return $this->roles->contains('name', $roles);
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->checkRolesUncached($role, $guard)) {
                    return true;
                }
            }
            return false;
        }

        return $roles->intersect($this->roles)->isNotEmpty();
    }

    protected function getRoleCacheKey($roles, ?string $guard = null): string
    {
        return sprintf(
            'user_has_role:%s:%s:%s',
            $this->getKey(),
            md5(serialize($roles)),
            $guard ?? 'default'
        );
    }

    protected function getRoleCacheTtl(): int
    {
        return config('cacheable-permissions.role_cache_ttl', 900);
    }

    public function clearRoleCache(): void
    {
        $pattern = "user_has_role:{$this->getKey()}:*";

        $driver = config('cacheable-permissions.cache_driver', 'redis');

        if ($driver === 'redis' && config('cache.default') === 'redis') {
            $keys = Cache::getRedis()->keys($pattern);
            if ($keys) {
                Cache::getRedis()->del($keys);
            }
        }
    }

    /**
     * Role atandığında cache temizle
     */
    public function assignRole(...$roles)
    {
        $result = parent::assignRole(...$roles);
        $this->clearRoleCache();
        $this->clearPermissionCache(); // Role değişince permission cache'i de temizle
        return $result;
    }

    /**
     * Role kaldırıldığında cache temizle
     */
    public function removeRole($role)
    {
        $result = parent::removeRole($role);
        $this->clearRoleCache();
        $this->clearPermissionCache(); // Role değişince permission cache'i de temizle
        return $result;
    }
}