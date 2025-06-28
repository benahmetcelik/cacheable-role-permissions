# Cacheable Permissions

Laravel için optimize edilmiş permission ve role caching trait'leri.

## Kurulum

```bash
composer require benahmetcelik/cacheable-permissions
```

Config Dosyasını Paylaşma

```bash
php artisan vendor:publish --tag=cacheable-permissions-config
```

## Kullanım
User Modelinizde
```php
<?php


use Cacheable\CacheablePermissions\Traits\CacheablePermissions;
use Cacheable\CacheablePermissions\Traits\CacheableRoles;

class User extends Model
{
use CacheablePermissions, CacheableRoles;

    // ...
}
```

Permission Kontrolü
```php
$user->checkPermissionTo('edit-posts');
```
Role Kontrolü
```php
$user->hasRole('admin');
```
Cache Temizleme
```php
$user->clearPermissionCache();
$user->clearRoleCache();
```
Konfigürasyon
.env dosyanızda:
```dotenv
PERMISSION_CACHE_TTL=15
ROLE_CACHE_TTL=900
CACHEABLE_PERMISSIONS_DRIVER=redis
```


## 8. LICENSE Dosyası (MIT)
MIT License
Copyright (c) 2025 Adınız
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.