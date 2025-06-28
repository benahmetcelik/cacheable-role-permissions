<?php

namespace Cacheable\CacheablePermissions\Observers;

use Illuminate\Database\Eloquent\Model;
use Cacheable\CacheablePermissions\Traits\CacheablePermissions;
use Cacheable\CacheablePermissions\Traits\CacheableRoles;

class UserCacheObserver
{
    /**
     * User güncellendiğinde cache'leri temizle
     */
    public function updated(Model $user): void
    {
        $this->clearUserCache($user);
    }

    /**
     * User silindiğinde cache'leri temizle
     */
    public function deleted(Model $user): void
    {
        $this->clearUserCache($user);
    }

    /**
     * User'a role atandığında cache'leri temizle
     */
    public function saved(Model $user): void
    {
        if ($user->relationLoaded('roles') && $user->isDirty()) {
            $this->clearUserCache($user);
        }
    }

    /**
     * User cache'lerini temizle
     */
    protected function clearUserCache(Model $user): void
    {
        $traits = class_uses_recursive($user);

        if (in_array(CacheablePermissions::class, $traits)) {
            $user->clearPermissionCache();
        }

        if (in_array(CacheableRoles::class, $traits)) {
            $user->clearRoleCache();
        }
    }
}